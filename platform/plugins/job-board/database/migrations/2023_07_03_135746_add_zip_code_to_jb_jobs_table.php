<?php

use Botble\Base\Supports\Database\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('jb_jobs', function (Blueprint $table) {
            $table->string('zip_code', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('jb_jobs', function (Blueprint $table) {
            $table->dropColumn('zip_code');
        });
    }
};
