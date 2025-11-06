<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('table_history', function (Blueprint $table) {
            $table->id();
            $table->string('table');
            $table->string('entry');
            $table->json('values');
            $table->foreignIdFor(User::class)->nullable()->constrained();
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_history');
    }
};
