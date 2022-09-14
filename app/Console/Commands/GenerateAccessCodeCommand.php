<?php

namespace App\Console\Commands;

use App\Models\AccessCode;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateAccessCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:generate-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Access Code';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $code = Str::random(10);

        AccessCode::create(["code" => $code]);

        $this->info("Your access code:");
        echo($code).PHP_EOL;
        return 0;
    }
}
