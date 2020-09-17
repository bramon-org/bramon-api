<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeStoragePublicCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = "make:symlink";

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = "Make the storage directory public";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $orig = storage_path() . "/captures";
        $dest = base_path() . "/public/captures";

        if (!file_exists($dest)) {
            symlink($orig, $dest);
        }

        $this->info('Done.');
    }
}
