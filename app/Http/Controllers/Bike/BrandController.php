<?php

namespace App\Http\Controllers\Bike;

use Illuminate\Http\Request;
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
            'success' => 'Them hang xe moi thanh cong.'
        ],
        'update' => [
            'success' => 'Chinh sua hang xe thanh cong.'
        ],
        'destroy' => [
            'success' => 'Xoa hang xe thanh cong.'
        ]
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('content.brand.brands', [
            'brands' => Brand::all()
        ]);
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
        Brand::create($validator);

        // Return with a congratulation message!
        return redirect()
                ->route('brands.create')
                ->with('notify', $this->successMessages['create']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand) {
        return view('content.brand.brands', compact('brand'));
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

        // Update brand
        $brand->update($validator);

        // Save
        $brand->save();

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
        echo $brand->brand_name . ' is going to be deleted!';
    }
}
