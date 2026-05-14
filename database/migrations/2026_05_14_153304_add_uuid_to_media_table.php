<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        // 1) Add nullable so existing rows aren't rejected.
        Schema::table('media', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // 2) Backfill any rows missing a UUID.
        DB::table('media')->whereNull('uuid')->orderBy('id')->each(function ($row) {
            DB::table('media')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
        });

        // 3) Lock it down: not null + unique + indexed.
        Schema::table('media', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
            $table->unique('uuid');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
