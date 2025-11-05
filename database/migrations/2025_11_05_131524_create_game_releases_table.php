<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_releases', function (Blueprint $table) {
            $table->id();
            $table->string('list');
            $table->string('name');
            $table->json('platforms');
            $table->string('url')->nullable();
            $table->string('source');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_releases');
    }
};
