<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('access', function (Blueprint $table) {
            $table->timestamp('created_at')->useCurrent();
            $table->string('cookie_id');
            $table->string('route');
            $table->string('controller');
            $table->string('request_string');
            $table->string('request_method');
            $table->string('ip');
            $table->string('user_agent');
            $table->string('filename');
            $table->string('referer')->nullable();
            $table->json('headers')->nullable();
            $table->foreignIdFor(User::class)->nullable()->constrained();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access');
    }
};
