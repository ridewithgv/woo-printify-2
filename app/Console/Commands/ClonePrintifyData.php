<?php

namespace App\Console\Commands;

use App\Http\Controllers\PrintifyController;
use Illuminate\Console\Command;

class ClonePrintifyData extends Command
{
    protected $signature = 'app:clone-printify-data';
    protected $description = 'Clone data from Printify API';

    public function handle(PrintifyController $printifyController)
    {
        $printifyController->newImportProducts();

        $this->info('data added to queue successfully.');
    }
}
