<?php

use App\Models\File;
use App\Models\LootboxItem;
use App\Models\LootboxTier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lootbox_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->boolean('css');
            $table->boolean('buddie');
            $table->boolean('music');
            $table->text('css_contents')->nullable();
            $table->string('series');
            $table->decimal('drop_chance', 10, 5)->nullable();
            $table->decimal('absolute_drop_chance', 10, 5)->nullable();
            $table->decimal('cached_drop_chance_start', 10, 5)->nullable();
            $table->decimal('cached_drop_chance_end', 10, 5)->nullable();
            $table->string('extra')->nullable();
            $table->foreignIdFor(File::class, 'image_id')->nullable()->constrained();
            $table->foreignIdFor(File::class, 'music_file_id')->nullable()->constrained();
            $table->foreignIdFor(LootboxTier::class)->constrained('lootbox_tiers');
            $table->timestamps();
        });

        Schema::create('lootbox_item_files', function (Blueprint $table) {
            $table->foreignIdFor(LootboxItem::class)->constrained();
            $table->foreignIdFor(File::class)->constrained();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lootbox_items');
        Schema::dropIfExists('lootbox_item_files');
    }
};
