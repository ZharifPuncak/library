<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('media', 'description')) {
            Schema::table('media', function (Blueprint $table) {
                $table->text('description')->nullable()->after('title');
            });
        }

        if (!Schema::hasColumn('collections', 'description')) {
            Schema::table('collections', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('media', 'description')) {
            Schema::table('media', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('collections', 'description')) {
            Schema::table('collections', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
