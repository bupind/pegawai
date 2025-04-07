<?php

namespace App\Console\Commands;

use App\Services\ExpiredService;
use Illuminate\Console\Command;

class ExpiredDocumentStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expired-document-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(ExpiredService $service)
    {
        $this->info('Updating all statuses...');
        $service->updateAllStatuses();
        $this->info('All statuses updated successfully.');
    }
}
