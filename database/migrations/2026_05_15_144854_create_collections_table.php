<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Backfill: for every existing collection name referenced in media_details,
        // create a matching row so it has a UUID we can route by.
        $names = DB::table('media_details')
            ->where('key', 'collection')
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->distinct()
            ->pluck('value');

        foreach ($names as $name) {
            DB::table('collections')->updateOrInsert(
                ['name' => $name],
                [
                    'uuid'       => (string) Str::uuid(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
