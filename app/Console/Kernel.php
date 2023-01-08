<?php

namespace App\Console;

use App\Models\Brand;
use App\Models\BrandVisitors;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            foreach (Brand::all() as $brand) {
                $brand_visitors = new BrandVisitors();
                $brand_visitors->brand_id = $brand->id;
                $brand_visitors->counter = 0;
                //gets the date now and gets the last day of this month along with the time
                $brand_visitors->end_date = Carbon::parse(Carbon::now())->endOfMonth()->toDateTimeLocalString();
                $brand_visitors->save();
            }
            return true;
        })->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
