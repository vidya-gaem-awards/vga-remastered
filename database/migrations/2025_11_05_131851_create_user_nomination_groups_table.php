<?php

use App\Models\Award;
use App\Models\Nominee;
use App\Models\UserNominationGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_nomination_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Award::class)->constrained();
            $table->string('name');
            $table->boolean('ignored');
            $table->foreignIdFor(Nominee::class)->nullable()->constrained();
            $table->foreignIdFor(UserNominationGroup::class, 'merged_into_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_nomination_groups');
    }
};
