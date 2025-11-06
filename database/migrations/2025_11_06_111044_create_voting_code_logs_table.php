<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('voting_code_logs', function (Blueprint $table) {
            $table->string('cookie_id');
            $table->timestamp('created_at');
            $table->string('ip');
            $table->string('code');
            $table->string('referer')->nullable();
            $table->foreignIdFor(User::class)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_code_logs');
    }
};
