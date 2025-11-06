<?php

use App\Models\LootboxItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('fuzzy_user_id');
            $table->foreignIdFor(LootboxItem::class)->constrained('lootbox_items');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_inventory_items');
    }
};
