<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Назови файл: 2024_01_01_000003_create_permissions_table.php

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Schema::create только создаёт структуру таблицы.
        // DB::insert вызывается ОТДЕЛЬНО — в сидерах, не здесь.
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('resource');
            $table->string('action');
            $table->string('scope')->default('global');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['resource', 'action', 'scope']);
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
    }
};