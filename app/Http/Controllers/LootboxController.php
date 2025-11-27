<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Award;
use App\Models\File;
use App\Models\LootboxItem;
use App\Models\LootboxTier;
use App\Models\TableHistory;
use App\Services\AuditService;
use App\Services\FileService;
use App\Services\LootboxService;
use App\Settings\AppSettings;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LootboxController extends Controller
{
    public function __construct(
        private readonly AppSettings $settings,
        private readonly AuditService $auditService,
        private readonly LootboxService $lootboxService,
        private readonly FileService $fileService,
    ) {
    }

    public function lootboxRedirect(): RedirectResponse
    {
        return redirect()->route('lootbox.items');
    }

    public function items(): View
    {
        $items = LootboxItem::query()
            ->with('image')
            ->with('musicFile')
            ->with('additionalFiles')
            ->get()
            ->keyBy('id');

        $tiers = LootboxTier::query()
            ->with('lootboxItems')
            ->with('lootboxItems.image')
            ->orderByDesc('drop_chance')
            ->get()
            ->keyBy('id');

        $firstAward = Award::first();

        return view('lootboxes.items', [
            'items' => $items,
            'tiers' => $tiers,
            'firstAward' => $firstAward,
        ]);
    }

    public function itemPost(Request $request): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $action = $request->post('action');

        if (!in_array($action, ['new', 'edit', 'delete'], true)) {
            return response()->json(['error' => 'Invalid action specified.']);
        }

        if ($action === 'new') {
            $item = new LootboxItem();
        } else {
            $item = LootboxItem::with('userInventoryItems')->find($request->post('id'));
            if (!$item) {
                return response()->json(['error' => 'Invalid item specified.']);
            }
        }

        if ($action === 'delete') {
            if ($item->userInventoryItems->isNotEmpty()) {
                return response()->json(['error' => 'This drop has already been acquired by somebody, and it cannot be deleted.']);
            }
            $item->delete();
            $this->auditService->add(
                Action::makeWith('item-delete', $item->id)
            );

            return response()->json(['success' => true]);
        }

        if (strlen(trim($request->post('slug', ''))) === 0) {
            return response()->json(['error' => 'You need to enter a slug.']);
        }

        if (!preg_match('/^[0-9a-z-]+$/', $request->post('slug'))) {
            return response()->json(['error' => 'Slug must consist of lowercase letters and dashes only.']);
        }

        if (strlen(trim($request->post('name', ''))) === 0) {
            return response()->json(['error' => 'You need to enter a name.']);
        }

        if (!$request->integer('tier')) {
            return response()->json(['error' => 'You need to select a tier.']);
        }

        $tier = LootboxTier::find($request->integer('tier'));
        if (!$tier) {
            return response()->json(['error' => 'Invalid tier selected.']);
        }

        $item->series = year();
        $item->slug = $request->post('slug');
        $item->name = $request->post('name');
        $item->lootboxTier()->associate($tier);
        $item->css = $request->boolean('css');
        $item->buddie = $request->boolean('buddie');
        $item->music = $request->boolean('music');
        $item->css_contents = $request->post('cssContents');

        if ($request->boolean('drop-chance-override')) {
            // @TODO: check this
            if ($request->post('drop-chance-relative') !== null && $request->post('drop-chance-absolute') !== null) {
                return response()->json(['error' => 'You can\'t have a relative and absolute drop chance set at the same time.']);
            }

            if ($request->post('drop-chance-relative') !== null) {
                $item->drop_chance = $request->post('drop-chance-relative');
            } else {
                $item->drop_chance = null;
            }

            if ($request->post('drop-chance-absolute') !== null) {
                $totalDropChance = LootboxItem::getTotalAbsoluteDropChance();

                if ($totalDropChance - (float)$item->absolute_drop_chance + (float)$request->post('drop-chance-absolute') / 100 > 1) {
                    return response()->json(['error' => 'Absolute drop chance is too high: the total of all items in the database cannot be over 100%']);
                }

                $item->absolute_drop_chance = $request->post('drop-chance-absolute') / 100;
            } else {
                $item->absolute_drop_chance = null;
            }
        } else {
            $item->drop_chance = null;
            $item->absolute_drop_chance = null;
        }

        if (!$item->exists && !$request->file('image')) {
            return response()->json(['error' => 'An image is required.']);
        }

        $item->save();

        if ($request->file('image')) {
            try {
                $file = $this->fileService->handleUploadedFile(
                    $request->file('image'),
                    'LootboxItem.image',
                    'rewards',
                    $item->id,
                );
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }

            if ($item->image) {
                $oldFile = $item->image;
                $item->image()->dissociate();
                $item->save();
                $this->fileService->deleteFile($oldFile);
            }

            $item->image()->associate($file);
            $item->save();
        }

        if ($request->file('musicFile')) {
            try {
                $file = $this->fileService->handleUploadedFile(
                    $request->file('musicFile'),
                    'LootboxItem.musicFile',
                    'music',
                    $item->id,
                );
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }

            if ($item->musicFile) {
                $oldFile = $item->musicFile;
                $item->musicFile()->dissociate();
                $item->save();
                $this->fileService->deleteFile($oldFile);
            }

            $item->musicFile()->associate($file);
            $item->save();
        }

        if (Gate::allows('items_manage_special')) {
            $additionalFiles = $request->file('additionalFile', []);
            for ($i = 0; $i < count($additionalFiles); $i++) {
                try {
                    $file = $this->fileService->handleUploadedFile(
                        $additionalFiles[$i],
                        'LootboxItem.additionalFile',
                        'lootboxExtras',
                        null,
                        true,
                    );
                } catch (Exception $e) {
                    return response()->json(['error' => $e->getMessage()]);
                }

                $item->additionalFiles()->attach($file);
            }

            $additionalFilesToDelete = $request->post('deleteAdditionalFile', []);

            foreach ($additionalFilesToDelete as $fileId) {
                $file = File::find($fileId);
                if (!$file) {
                    return response()->json(['error' => 'Invalid file ID specified.']);
                }

                $item->additionalFiles()->detach($file);
                $this->fileService->deleteFile($file);
            }
        }

        $this->auditService->add(
            Action::makeWith('item-' . $action, $item->id),
            TableHistory::makeWith(LootboxItem::class, $item->id, $request->post())
        );

        $this->lootboxService->updateCachedValues();

        return response()->json(['success' => true]);
    }

    public function itemUpdateCss(Request $request): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $item = LootboxItem::find($request->integer('id'));
        if (!$item) {
            return response()->json(['error' => 'Invalid item specified.']);
        }

        $item->css_contents = $request->get('cssContents');
        $item->save();

        $this->auditService->add(
            Action::makeWith('item-edit', $item->id),
            TableHistory::makeWith(LootboxItem::class, $item->id, $request->post())
        );

        return response()->json(['success' => true]);
    }

    public function itemCalculation(Request $request): JsonResponse
    {
        if ($request->post('id')) {
            $originalItem = LootboxItem::find($request->post('id'));
            if (!$originalItem) {
                return response()->json(['error' => 'Invalid item ID.'], 400);
            }
        } else {
            $originalItem = null;
        }

        $tier = LootboxTier::find($request->post('tier'));
        if (!$tier) {
            return response()->json(['error' => 'Invalid tier ID.'], 400);
        }

        $item = new LootboxItem();
        $item->id = -1;
        $item->lootbox_tier_id = $tier->id;

        if ($request->boolean('dropChanceOverride')) {
            if ($request->post('absoluteDropChance') !== null) {
                $item->absolute_drop_chance = $request->post('absoluteDropChance') / 100;
            } elseif ($request->post('dropChance') !== null) {
                $item->drop_chance = $request->post('dropChance');
            }
        }

        $itemChances = $this->lootboxService->getItemArray($item, $originalItem);
        if (!isset($itemChances[$item->id])) {
            $absoluteDropChance = 0.0;
        } else if (array_sum($itemChances) === 0.0) {
            // Edge case where there are only items with an absolute drop chance override in the database
            $absoluteDropChance = null;
        } else {
            $absoluteDropChance = $itemChances[$item->id] / array_sum($itemChances);
        }

        return response()->json([
            'success' => true,
            'absoluteDropChance' => $absoluteDropChance,
        ]);
    }

    public function tiers(): View
    {
        $tiers = LootboxTier::query()
            ->orderByDesc('drop_chance')
            ->get()
            ->keyBy('id');

        return view('lootboxes.tiers', [
            'tiers' => $tiers,
        ]);
    }

    public function tierPost(Request $request): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $action = $request->post('action');

        if (!in_array($action, ['new', 'edit', 'delete'], true)) {
            return response()->json(['error' => 'Invalid action specified.']);
        }

        if ($action === 'new') {
            $tier = new LootboxTier();
        } else {
            $tier = LootboxTier::find($request->post('id'));
            if (!$tier) {
                return response()->json(['error' => 'Invalid lootbox tier specified.']);
            }
        }

        if ($action === 'delete') {
            if (!$tier->lootboxItems->isEmpty()) {
                return response()->json(['error' => 'This tier can\'t be deleted while it still contains items.']);
            }

            $this->auditService->add(
                Action::makeWith('lootbox-tier-delete', $tier->id)
            );

            $tier->delete();

            return response()->json(['success' => true]);
        }

        if (strlen(trim($request->post('name', ''))) === 0) {
            return response()->json(['error' => 'You need to enter a name.']);
        }

        if (strlen(trim($request->post('color', ''))) === 0) {
            return response()->json(['error' => 'You need to enter a color.']);
        }

        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $request->post('color'))) {
            return response()->json(['error' => 'Color is invalid: must be in the format #000000']);
        }

        if (!preg_match('/^(\d+.)?\d+$/', $request->post('dropChance'))) {
            return response()->json(['error' => 'You need to enter a drop chance.']);
        }

        $tier->name = $request->post('name');
        $tier->drop_chance = $request->post('dropChance');
        $tier->color = $request->post('color');
        $tier->save();

        $this->auditService->add(
            Action::makeWith('lootbox-tier-' . $action, $tier->id),
            TableHistory::makeWith(LootboxTier::class, $tier->id, $request->post()),
        );

        $this->lootboxService->updateCachedValues();

        return response()->json(['success' => true]);
    }

    public function tierCalculation(Request $request): JsonResponse
    {
        if ($request->integer('id')) {
            $tier = LootboxTier::find($request->integer('id'));
            if (!$tier) {
                return response()->json(['error' => 'Invalid tier ID.'], 400);
            }
        } else {
            $tier = null;
        }

        $newChance = (float) $request->post('dropChance');

        $absoluteDropChance = $this->lootboxService->getAbsoluteDropChanceFromRelativeChance($newChance, !$tier, $tier);

        return response()->json([
            'success' => true,
            'absoluteDropChance' => $absoluteDropChance,
        ]);
    }

    public function settings(): View
    {
        return view('lootbox.settings');
    }

    public function settingsSave(Request $request): RedirectResponse
    {
        if ($this->settings->read_only) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return redirect()->back();
        }

        $this->settings->lootbox_cost = $request->integer('cost');
        $this->settings->save();

        $this->auditService->add(
            Action::makeWith('lootbox-settings-update')
        );

        $this->addFlash('success', 'Lootbox settings saved.');
        return redirect()->back();
    }
}
