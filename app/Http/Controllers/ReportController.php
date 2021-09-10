<?php

namespace App\Http\Controllers;

use App\Models\Bike;

class ReportController extends Controller
{
    /**
     * Return list of out-of-stock items.
     * 
     * @return \Illuminate\Http\Response
     */
    public function out_of_stock_index() {
        $bikes = Bike::where('bike_stock', '<', 10)
            ->with('brand')
            ->get();

        return view('content.report.out-of-stock', ['items' => $bikes]);
    }

    /**
     * Return how many bikes were sold in a month.
     * 
     * @return \Illuminate\Http\Response
     */
    public function month_quantity_stat_index() {
        return view('content.report.month-quantity-stat');
    }

    /**
     * Return revenue of a month.
     * 
     * @return \Illuminate\Http\Response
     */
    public function month_revenue_stat_index() {
        return view('content.report.month-revenue-stat');
    }
}
