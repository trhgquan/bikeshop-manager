<?php

namespace App\Http\Controllers\Bike;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBrandRequest;
use App\Models\Brand;

class BrandController extends Controller
{
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
        return redirect()->route('brands.create')->with(
            'notify',
            $this->successMessages['create']
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id) {
        return view('content.brand.brands', [
            'brand' => Brand::findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id) {
        return view('content.brand.update', [
            'brand' => Brand::findOrFail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        dd($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        echo $id . ' is going to be deleted!';
    }
}
