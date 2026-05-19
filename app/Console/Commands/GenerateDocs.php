<?php

namespace App\Console\Commands;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

class GenerateDocs extends Command
{
    protected $signature = 'docs:generate';
    protected $description = 'Render the technical documentation + user manual to PDFs under public/docs/';

    public function handle(): int
    {
        $outDir = public_path('docs');
        if (!is_dir($outDir)) {
            mkdir($outDir, 0755, true);
        }

        $jobs = [
            'technical-documentation.pdf' => 'docs.technical',
            'user-manual.pdf'             => 'docs.user-manual',
        ];

        foreach ($jobs as $file => $view) {
            $path = $outDir . DIRECTORY_SEPARATOR . $file;
            Pdf::loadView($view)
                ->setPaper('a4')
                ->setOption('isRemoteEnabled', true)
                ->save($path);

            $this->info("Wrote {$path}");
        }

        return self::SUCCESS;
    }
}
