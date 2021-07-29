<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bike;

class ReportController extends Controller
{
    private $resultsPerPage = 10;

    public function out_of_stock() {
        $bikes = Bike::whereHas('stock', function($query) {
            $query->where('stock', '<', 10);
        })->with('brand', 'stock')->paginate($this->resultsPerPage);
        
        return view('content.report.out-of-stock', ['items' => $bikes]);
    }
}
