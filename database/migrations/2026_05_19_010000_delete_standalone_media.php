<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration {
    public function up(): void
    {
        $collectionMediaIds = DB::table('media_details')
            ->where('key', 'collection')
            ->pluck('media_id')
            ->all();

        $standalone = DB::table('media')
            ->when(!empty($collectionMediaIds), fn($q) => $q->whereNotIn('id', $collectionMediaIds))
            ->get(['id', 'file_path', 'thumbnail_path']);

        foreach ($standalone as $row) {
            if ($row->file_path) {
                Storage::disk('public')->delete($row->file_path);
            }
            if ($row->thumbnail_path) {
                Storage::disk('public')->delete($row->thumbnail_path);
            }
        }

        DB::table('media')
            ->when(!empty($collectionMediaIds), fn($q) => $q->whereNotIn('id', $collectionMediaIds))
            ->delete();
    }

    public function down(): void
    {
        // One-way cleanup — no rollback.
    }
};
