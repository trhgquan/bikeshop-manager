<?php

namespace App\Http\Controllers\Bike\APIs;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandAPIController extends Controller
{
    // TODO: make the index of brands a vue.js on load page (for searching).
    private $resultsPerPage = 10;

    /**
     * API method - List out all brands.
     * 
     * @return \App\Http\Resources\BrandResource
     */
    public function all() {
        return BrandResource::collection(
            Brand::all()
        );
    }

    /**
     * API method - Search a brand.
     * 
     * @param string $keyword
     * @return \App\Http\Resources\BrandResource
     */
    public function search($keyword) {
        return BrandResource::collection(
            Brand::where(
                'brand_name', 'LIKE', '%' . $keyword . '%'
            )->paginate($this->resultsPerPage)
        );
    }
}
