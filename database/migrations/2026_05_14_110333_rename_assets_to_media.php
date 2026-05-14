<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Rename the main table first so subsequent FK constraints can target it.
        Schema::rename('assets', 'media');

        // 2) Rename pivot/detail tables, then rename their FK columns from asset_id → media_id.
        Schema::rename('asset_details', 'media_details');
        Schema::table('media_details', function (Blueprint $table) {
            $table->renameColumn('asset_id', 'media_id');
        });

        Schema::rename('asset_category', 'media_category');
        Schema::table('media_category', function (Blueprint $table) {
            $table->renameColumn('asset_id', 'media_id');
        });

        Schema::rename('asset_tag', 'media_tag');
        Schema::table('media_tag', function (Blueprint $table) {
            $table->renameColumn('asset_id', 'media_id');
        });
    }

    public function down(): void
    {
        Schema::table('media_tag', function (Blueprint $table) {
            $table->renameColumn('media_id', 'asset_id');
        });
        Schema::rename('media_tag', 'asset_tag');

        Schema::table('media_category', function (Blueprint $table) {
            $table->renameColumn('media_id', 'asset_id');
        });
        Schema::rename('media_category', 'asset_category');

        Schema::table('media_details', function (Blueprint $table) {
            $table->renameColumn('media_id', 'asset_id');
        });
        Schema::rename('media_details', 'asset_details');

        Schema::rename('media', 'assets');
    }
};
