<?php

use App\Models\Autocompleter;
use App\Models\File;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subtitle');
            $table->string('slug');
            $table->integer('order');
            $table->text('comments')->nullable();
            $table->boolean('nominations_enabled');
            $table->boolean('secret');
            $table->foreignIdFor(File::class, 'winner_image_id')->nullable()->constrained();
            $table->foreignIdFor(Autocompleter::class)->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};
