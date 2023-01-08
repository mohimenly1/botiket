<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandVisitors;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class BrandVisitorsController extends Controller
{
    public function brandMonthVisitors(Request $request)
    {
        //$brand_id, $date
        $year = $request->year;
        $brand_visitors = BrandVisitors::where('brand_id', $request->brand_id)->get();

        $start_of_current_month = Carbon::now()->startOfMonth();
        $end_of_current_month = Carbon::now()->endOfMonth();

        $current_month_visitors = $brand_visitors->whereBetween('created_at', [$start_of_current_month, $end_of_current_month])->sum('counter');
        // return $brand_visitors->where('created_at', Carbon::now()->startOfMonth());
        return [
            'brand' => Brand::find($request->brand_id),
            'all_time' => $brand_visitors->sum('counter'),
            'current_month' => $current_month_visitors,
            'months' => $this->getMonthsArray($year, $brand_visitors),
            'year_range' => $this->calculateYearRange($brand_visitors->min('created_at'), $brand_visitors->max('end_date'))
        ];
    }
    protected function calculateYearRange($min_year, $max_year)
    {
        $min = new Carbon($min_year);
        $max = new Carbon($max_year);
        return range($min->year, $max->year);
    }
    protected function getMonthsArray($year, $brand_visitors)
    {
        $months = [];
        $i = 0;
        for ($index = 1; $index <= 12; $index++) {
            $months[$i] = $this->monthVisitors($index, $year, $brand_visitors);
            $i++;
        }
        return $months;
    }
    public function monthVisitors($month, $year, $visitors)
    {
        $start = Carbon::create($year, $month);
        $end = Carbon::create($year, $month)->endOfMonth();
        $visitors = $visitors->whereBetween('created_at', [$start, $end])->sum('counter');
        return $visitors;
    }
}
