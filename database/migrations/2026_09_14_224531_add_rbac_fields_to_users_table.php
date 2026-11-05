<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'warehouse_manager', 'purchasing', 'picker'])
                ->default('picker')->after('email');
            $table->foreignId('warehouse_id')->nullable()->after('role')
                ->constrained('warehouses')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('warehouse_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('warehouse_id');
            $table->dropColumn(['role', 'is_active']);
        });
    }
};
