<?php

namespace App\Console\Commands;

use App\Models\AccessCode;
use Illuminate\Console\Command;

class DeleteAccessCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:delete-code {code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete access code';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $code = $this->argument("code");

        AccessCode::where("code", $code)
            ->delete();

        $this->info("Done!");

        return 0;
    }
}
