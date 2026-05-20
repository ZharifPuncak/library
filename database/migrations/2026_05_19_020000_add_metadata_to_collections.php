<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->string('status', 20)->default('draft')->after('description');
            $table->dateTime('date')->nullable()->after('status');
            $table->string('thumbnail_path')->nullable()->after('date');
        });

        Schema::create('collection_category', function (Blueprint $table) {
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->primary(['collection_id', 'category_id']);
        });

        Schema::create('collection_tag', function (Blueprint $table) {
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->primary(['collection_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_tag');
        Schema::dropIfExists('collection_category');
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['status', 'date', 'thumbnail_path']);
        });
    }
};
