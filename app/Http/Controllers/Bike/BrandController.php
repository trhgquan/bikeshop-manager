<?php

namespace App\Http\Controllers\Bike;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBrandRequest;
use App\Models\Brand;

class BrandController extends Controller
{
    /**
     * Success messages to send.
     * 
     * @var array
     */
    private $successMessages = [
        'create' => [
            'success' => 'Thêm hãng xe mới thành công!'
        ],
        'update' => [
            'success' => 'Chỉnh sửa hãng xe thành công!'
        ],
        'destroy' => [
            'success' => 'Xóa hãng xe thành công!'
        ]
    ];

    /**
     * Constructor for BrandController.
     * 
     * @return void
     */
    public function __construct() {
        $this->authorizeResource(Brand::class, 'brand');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $brands = Brand::all();
        return view('content.brand.dashboard', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('content.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateBrandRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBrandRequest $request) {
        // Get request validated data.
        $validator = $request->validated();
        
        // Create a new brand!
        $new_brand = Brand::create([
            'brand_name' => $validator['brand_name'],
            'brand_description' => $validator['brand_description'],
        ]);

        // Return with a congratulation message!
        return redirect()
                ->route('brands.show', $new_brand)
                ->with('notify', $this->successMessages['create']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand) {
        $bikes = $brand->bikes()->get();
        return view('content.brand.details', compact('brand', 'bikes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand) {
        return view('content.brand.update', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CreateBrandRequest  $request
     * @param  \App\Models\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function update(CreateBrandRequest $request, Brand $brand) {
        // Get validated data.
        $validator = $request->validated();

        // Update brand's information.
        $brand->update($validator);

        // Return back with message.
        return redirect()
                ->route('brands.edit', compact('brand'))
                ->with('notify', $this->successMessages['update']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand) {
        // Soft-delete the branch.
        $brand->delete();

        // Redirect back, with destroy message.
        return redirect()
            ->route('brands.index')
            ->with('notify', $this->successMessages['destroy']);
    }
}
