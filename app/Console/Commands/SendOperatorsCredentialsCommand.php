<?php

namespace App\Console\Commands;

use App\Models\Station;
use App\Models\User;
use Illuminate\Console\Command;
use League\Csv\Exception as CsvException;
use League\Csv\Reader;

class SendOperatorsCredentialsCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = "operators:send-crendentials";

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = "Send the credentials for each operator";

    /**
     * Execute the console command.
     *
     * @return void
     * @throws CsvException
     */
    public function handle(): void
    {
        $operators = User::get();

        $this->info('Done.');
    }
}
