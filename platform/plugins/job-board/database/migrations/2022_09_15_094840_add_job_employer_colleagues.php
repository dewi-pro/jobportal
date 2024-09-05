<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('jb_jobs', function (Blueprint $table) {
            $table->text('employer_colleagues')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('jb_jobs', function (Blueprint $table) {
            $table->dropColumn('employer_colleagues');
        });
    }
};
