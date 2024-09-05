<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE TABLE IF NOT EXISTS jb_reviews_tmp LIKE jb_reviews');
        DB::statement('TRUNCATE TABLE jb_reviews_tmp');
        DB::statement('INSERT jb_reviews_tmp SELECT * FROM jb_reviews');
        DB::statement('TRUNCATE TABLE jb_reviews');

        Schema::table('jb_reviews', function (Blueprint $table) {
            $table->unique(['account_id', 'company_id'], 'reviews_unique');
        });

        DB::table('jb_reviews_tmp')->oldest()->chunk(1000, function ($chunked) {
            DB::table('jb_reviews')->insertOrIgnore(array_map(fn($item) => (array) $item, $chunked->toArray()));
        });

         Schema::dropIfExists('jb_reviews_tmp');
    }

    public function down(): void
    {
        Schema::table('jb_reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_unique');
        });
    }
};
