<?php

namespace App\Console\Commands;

use App\Jobs\ProcessScrapedData;
use App\Services\BrandService;
use App\Services\ProductService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class scrape extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public $product_service;
    public $brand_service;

    public function __construct(BrandService $b, ProductService $p)
    {
        parent::__construct();

        $this->product_service = $p;
        $this->brand_service = $b;

        $this->p = App::getFacadeApplication()->make(ProcessScrapedData::class);
    }

    private $p;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //loop over the products 

        //get the product details 
        $this->p->dispatch( $this->product_service, $this->brand_service );
        return 0;
    }
}
