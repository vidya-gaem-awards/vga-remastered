<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('soundtrack_songs', function (Blueprint $table) {
            $table->id();
            $table->string('section')->nullable();
            $table->integer('order')->default(0);
            $table->integer('number')->nullable();
            $table->string('title');
            $table->string('artist')->nullable();
            $table->string('context')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soundtrack_songs');
    }
};
