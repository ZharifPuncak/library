<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('slideshows') && !Schema::hasTable('sliders')) {
            Schema::rename('slideshows', 'sliders');
        }

        if (Schema::hasTable('sliders') && Schema::hasColumn('sliders', 'slideshow_pic')) {
            Schema::table('sliders', function (Blueprint $table) {
                $table->renameColumn('slideshow_pic', 'slider_pic');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sliders') && Schema::hasColumn('sliders', 'slider_pic')) {
            Schema::table('sliders', function (Blueprint $table) {
                $table->renameColumn('slider_pic', 'slideshow_pic');
            });
        }

        if (Schema::hasTable('sliders') && !Schema::hasTable('slideshows')) {
            Schema::rename('sliders', 'slideshows');
        }
    }
};
