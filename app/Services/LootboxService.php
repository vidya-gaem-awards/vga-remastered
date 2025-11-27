<?php
declare(strict_types = 1);

namespace App\Services;

use App\Models\LootboxItem;
use App\Models\LootboxTier;

/**
 * @TODO: This class would be fun to unit test
 */
class LootboxService {
    public function getItemArray(?LootboxItem $itemToAdd = null, ?LootboxItem $itemToRemove = null): array
    {
        /** @var LootboxTier[] $tiers */
        $tiers = LootboxTier::all()->keyBy('id');

        /** @var LootboxItem[] $items */
        $items = LootboxItem::all()->keyBy('id');

        if ($itemToAdd) {
            $items[] = $itemToAdd;
        }

        $absolute_items = [];
        $relative_items = [];
        $standard_items_by_tier = [];

        foreach ($items as $item) {
            if ($itemToRemove?->id === $item->id) {
                continue;
            }

            if ($item->absolute_drop_chance !== null) {
                $absolute_items[] = $item;
                continue;
            }

            if ($item->drop_chance !== null) {
                $relative_items[] = $item;
                continue;
            }

            $standard_items_by_tier[$item->lootbox_tier_id][] = $item;
        }

        $full_item_array = [];

        foreach ($relative_items as $item) {
            $full_item_array[$item->id] = (float) $item->drop_chance;
        }

        foreach ($standard_items_by_tier as $tier_id => $items) {
            foreach ($items as $item) {
                $full_item_array[$item->id] = (float) $tiers[$tier_id]->drop_chance / count($items);
            }
        }

        $full_item_array = array_filter($full_item_array, fn ($dropChance) => $dropChance > 0);

        $absolute_total = array_sum(array_map(fn (LootboxItem $item) => $item->absolute_drop_chance, $absolute_items));
        $relative_total = array_sum($full_item_array) / (1 - $absolute_total);

        foreach ($absolute_items as $item) {
            $full_item_array[$item->id] = (float) $item->absolute_drop_chance * $relative_total;
        }

        return $full_item_array;
    }

    public function updateCachedValues(): void
    {
        /** @var LootboxItem[] $items */
        $items = LootboxItem::all()->keyBy('id');

        $itemArray = $this->getItemArray();

        $count = 0.0;

        foreach ($items as $itemID => $item) {
            if (!isset($itemArray[$itemID])) {
                $item->cached_drop_value_start = null;
                $item->cached_drop_value_end = null;
            } else {
                $item->cached_drop_value_start = (string) $count;
                $count += $itemArray[$itemID];
                $item->cached_drop_value_end = (string) ($count - 0.00001);
            }
            $item->save();
        }
    }

    public function getRandomItem(): ?LootboxItem
    {
        $maxDropValue = LootboxItem::max('cached_drop_value_end');
        $maxDropValue = (int) ($maxDropValue * 100000);

        // The drop values aren't calculated correctly when there's only items with an absolute drop chance in the
        // database. Return no items when this is the case.
        if ($maxDropValue <= 0) {
            return null;
        }

        $randomNumber = random_int(0, $maxDropValue);

        return LootboxItem::query()
            ->whereRaw('? BETWEEN cached_drop_value_start AND cached_drop_value_end', [$randomNumber / 100000])
            ->first();
    }

    public function getTotalRelativeDropChance(): float
    {
        return LootboxItem::getTotalRelativeDropChance() + LootboxTier::getTotalRelativeDropChance();
    }

    public function getTotalAbsoluteDropChance(): float
    {
        return LootboxItem::getTotalAbsoluteDropChance();
    }

    /**
     * Gets the absolute drop chance (between 0.00 and 1.00) for a given relative drop chance.
     *
     * @param float $dropChance The relative drop chance of the item or tier.
     * @param bool $new True if this is for an item or tier that hasn't yet been saved to the database.
     * @param LootboxItem|LootboxTier|null $originalObject Should only be provided if editing an existing item or tier and the
     *                                                     changes haven't yet been saved to the database.
     *
     * @return float
     */
    public function getAbsoluteDropChanceFromRelativeChance(float $dropChance, bool $new, LootboxItem|LootboxTier|null $originalObject = null): float
    {
        $total = $this->getTotalRelativeDropChance();

        if ($new) {
            $total += $dropChance;
        } elseif ($originalObject) {
            $total = $total - $originalObject->drop_chance + $dropChance;
        }

        if ($total === 0.0) {
            return 0.0;
        }

        $totalAbsoluteDropChance = $this->getTotalAbsoluteDropChance();
        if ($originalObject instanceof LootboxItem) {
            $totalAbsoluteDropChance -= $originalObject->absolute_drop_chance;
        }

        return $dropChance / $total * max(1 - $totalAbsoluteDropChance, 0);
    }
}
