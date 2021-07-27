<?php

namespace App\Http\Controllers\Bike\APIs;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandAPIController extends Controller
{
    // TODO: make the index of brands a vue.js on load page (for searching).

    /**
     * Number of records per page.
     * 
     * @var int
     */
    private $resultsPerPage = 10;

    /**
     * API method - List out all Brands.
     * 
     * @return \App\Http\Resources\BrandResource
     */
    public function all() {
        return BrandResource::collection(
            Brand::paginate($this->resultsPerPage)
        );
    }

    /**
     * API method - Search a Brand with a given keyword.
     * 
     * @param  string $keyword
     * @return \App\Http\Resources\BrandResource
     */
    public function search($keyword) {
        return BrandResource::collection(
            Brand::where('brand_name', 'LIKE', '%' . $keyword . '%')
                ->paginate($this->resultsPerPage)
        );
    }
}
