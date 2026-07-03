<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::table('log_makans')
                ->whereNotIn('status', ['menunggu', 'sudah', 'telat'])
                ->update(['status' => 'menunggu']);

            return;
        }

        DB::statement("ALTER TABLE `log_makans` MODIFY `status` ENUM('belum','menunggu','sudah','telat') NOT NULL DEFAULT 'menunggu'");
        DB::statement("UPDATE `log_makans` SET `status` = 'menunggu' WHERE `status` NOT IN ('menunggu','sudah','telat')");
        DB::statement("ALTER TABLE `log_makans` MODIFY `status` ENUM('menunggu','sudah','telat') NOT NULL DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::table('log_makans')
                ->where('status', 'menunggu')
                ->update(['status' => 'belum']);

            return;
        }

        DB::statement("UPDATE `log_makans` SET `status` = 'belum' WHERE `status` = 'menunggu'");
        DB::statement("UPDATE `log_makans` SET `status` = 'sudah' WHERE `status` = 'telat'");
        DB::statement("ALTER TABLE `log_makans` MODIFY `status` ENUM('belum','sudah') NOT NULL DEFAULT 'belum'");
    }
};
