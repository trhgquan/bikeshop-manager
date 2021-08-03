<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bike;
use App\Models\Order;

class ReportController extends Controller
{
    /**
     * Number of records to display per page.
     * 
     * @var int
     */
    private $resultsPerPage = 10;

    /**
     * Return list of out-of-stock items.
     * 
     * @return \Illuminate\Http\Response
     */
    public function out_of_stock() {
        $bikes = Bike::where('bike_stock', '<', 10)
            ->with('brand')
            ->paginate($this->resultsPerPage);

        return view('content.report.out-of-stock', ['items' => $bikes]);
    }

    public function month_quantity_stat() {
        // TODO: embed some quality code in here
        // mostly in raw DB.

        return view('content.report.month-quantity-stat');
    }
}
