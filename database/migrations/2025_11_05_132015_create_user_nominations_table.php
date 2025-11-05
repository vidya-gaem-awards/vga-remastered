<?php

use App\Models\Award;
use App\Models\UserNominationGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Award::class)->nullable()->constrained();
            $table->string('fuzzy_user_id');
            $table->string('nomination');
            $table->foreignIdFor(UserNominationGroup::class)->constrained();
            $table->foreignIdFor(UserNominationGroup::class, 'original_group_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_nominations');
    }
};
