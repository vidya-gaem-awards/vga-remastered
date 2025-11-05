<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->string('id');
            $table->string('description');

            $table->primary('id');
        });

        Schema::create('permission_children', function (Blueprint $table) {
            $table->foreignIdString('parent_id')->constrained('permissions', 'id');
            $table->foreignIdString('child_id')->constrained('permissions', 'id');

            $table->unique(['parent_id', 'child_id']);
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdString('permission_id')->constrained('permissions', 'id');

            $table->unique(['permission_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('permission_children');
        Schema::dropIfExists('permissions');
    }
};
