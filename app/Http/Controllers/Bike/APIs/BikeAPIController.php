<?php

namespace App\Http\Controllers\Bike\APIs;

use App\Http\Controllers\Controller;
use App\Http\Resources\BikeResource;
use App\Models\Bike;
use App\Models\Brand;
use Illuminate\Http\Request;

class BikeAPIController extends Controller
{
    /**
     * Number of records per page.
     * 
     * @var int
     */
    private $resultsPerPage = 10;

    /**
     * API method - List out all Bikes.
     * 
     * @return \App\Http\Resources\BikeResource
     */
    public function all() {
        return BikeResource::collection(
            Bike::paginate($this->resultsPerPage)
        );
    }

    /**
     * API method - Search a Bike with a given keyword.
     * 
     * @param  string $keyword
     * @return \App\Http\Resources\BikeResource
     */
    public function search($keyword) {
        return BikeResource::collection(
            Bike::where('bike_name', 'LIKE', '%' . $keyword . '%')
                ->paginate($this->resultsPerPage)
        );
    }
}
