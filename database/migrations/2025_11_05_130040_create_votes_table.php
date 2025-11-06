<?php

use App\Models\Award;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Award::class)->constrained();
            $table->foreignIdFor(User::class)->nullable()->constrained();
            $table->string('cookie_id');
            $table->json('preferences');
            $table->string('ip');
            $table->string('voting_code')->nullable();
            $table->integer('voting_group')->nullable();
            $table->timestamps();

            $table->unique(['award_id', 'cookie_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
