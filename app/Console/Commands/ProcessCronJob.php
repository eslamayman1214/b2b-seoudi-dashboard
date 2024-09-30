<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ProductController;
use Illuminate\Console\Command;

// Assuming your function is in a service

class ProcessCronJob extends Command
{
    protected $signature = 'cron:product-send-api';
    protected $description = 'Process cron job every minute';

    protected $service;

    public function __construct(ProductController $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $this->service->processCronJob();
        $this->info('Cron job processed successfully.');
    }
}
