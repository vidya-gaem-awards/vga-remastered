<?php

use App\Models\Award;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('award_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Award::class)->constrained();
            $table->string('opinion');
            $table->string('fuzzy_user_id');
            $table->timestamps();

            $table->unique(['fuzzy_user_id', 'award_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('award_feedback');
    }
};
