<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // MySQL/MariaDB
        DB::statement("
            INSERT IGNORE INTO area_user (user_id, area_id, created_at, updated_at)
            SELECT id AS user_id, primary_area_id AS area_id, NOW(), NOW()
            FROM users
            WHERE primary_area_id IS NOT NULL
        ");
    }

    public function down(): void
    {
        // Revertir solo lo que vino del primary_area_id
        DB::statement("
            DELETE au FROM area_user au
            JOIN users u ON u.id = au.user_id
            WHERE u.primary_area_id = au.area_id
        ");
    }
};
