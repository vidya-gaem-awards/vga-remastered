<?php

use App\Models\Award;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('award_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('fuzzy_user_id');
            $table->string('suggestion');
            $table->foreignIdFor(Award::class)->nullable()->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('award_suggestions');
    }
};
