<?php

use App\Models\Award;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->foreignIdFor(Award::class)->constrained('awards');
            $table->string('filter');
            $table->string('algorithm');
            $table->json('results');
            $table->json('steps');
            $table->json('warnings');
            $table->integer('votes');
            $table->string('time_key');
            $table->timestamps();

            $table->unique(['award_id', 'filter', 'algorithm', 'time_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
