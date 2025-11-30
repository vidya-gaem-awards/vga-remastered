<?php

namespace App\Http\Controllers;

use App\Facade\FuzzyUser;
use App\Models\UserInventoryItem;
use App\Services\LootboxService;
use App\Settings\AppSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        private readonly AppSettings $settings,
        private readonly LootboxService $lootboxService,
    )
    {
    }

    public function purchaseLootbox(Request $request): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The lootbox shop has closed for the year. No refunds!'], 400);
        }

        $rewards = [];
        for ($i = 0; $i < 3; $i++) {
            if (random_int(1, 3) === 3) {
                $rewards[] = ['type' => 'shekels', 'amount' => random_int(2, 1000)];
            } elseif ($item = $this->lootboxService->getRandomItem()) {
                $rewards[] = ['type' => 'item', 'item' => $item];

                if (!$request->query->get('test')) {
                    $userItem = new UserInventoryItem();
                    $userItem->lootbox_item_id = $item->id;
                    $userItem->fuzzy_user_id = FuzzyUser::id();
                    $userItem->save();
                }
            } else {
                // Failsafe for if no lootbox items are available
                $rewards[] = ['type' => 'shekels', 'amount' => 1];
            }
        }

        return response()->json(['rewards' => $rewards]);
    }
}
