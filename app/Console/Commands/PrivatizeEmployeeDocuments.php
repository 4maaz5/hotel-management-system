<?php

namespace App\Console\Commands;

use App\Models\EmployeeDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PrivatizeEmployeeDocuments extends Command
{
    protected $signature = 'employee-documents:privatize {--delete-public : Delete public copies after they are copied to private storage}';

    protected $description = 'Copy existing employee document files from public storage to private local storage.';

    public function handle(): int
    {
        $copied = 0;
        $missing = 0;
        $deleted = 0;

        EmployeeDocument::query()
            ->select(['id', 'file_path', 'image'])
            ->chunkById(100, function ($documents) use (&$copied, &$missing, &$deleted): void {
                foreach ($documents as $document) {
                    foreach (['file_path', 'image'] as $column) {
                        $path = $document->{$column};

                        if (! $path || Storage::disk('local')->exists($path)) {
                            continue;
                        }

                        if (! Storage::disk('public')->exists($path)) {
                            $missing++;
                            $this->warn("Missing public file for employee document {$document->id}: {$path}");

                            continue;
                        }

                        Storage::disk('local')->put($path, Storage::disk('public')->get($path));
                        $copied++;

                        if ($this->option('delete-public')) {
                            Storage::disk('public')->delete($path);
                            $deleted++;
                        }
                    }
                }
            });

        $this->info("Employee document privatization complete. Copied: {$copied}. Missing: {$missing}. Public deleted: {$deleted}.");

        return self::SUCCESS;
    }
}
