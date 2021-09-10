<?php

namespace App\Http\Controllers\Bike;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBikeRequest;
use App\Models\Bike;
use App\Models\Brand;

class BikeController extends Controller
{
    /**
     * Success messages to send.
     *
     * @var array
     */
    private $successMessages = [
        'create' => [
            'success' => 'Tạo loại xe thành công.',
        ],
        'update' => [
            'success' => 'Sửa loại xe thành công.',
        ],
        'destroy' => [
            'success' => 'Xóa loại xe thành công.',
        ],
    ];

    /**
     * Constructor for BikeController.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Bike::class, 'bike');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bikes = Bike::with('brand')->get();

        return view('content.bike.dashboard', compact('bikes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::all();

        return view('content.bike.create', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateBikeRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBikeRequest $request)
    {
        $validator = $request->validated();

        $new_bike = Bike::create($validator);

        return redirect()
            ->route('bikes.show', $new_bike)
            ->with('notify', $this->successMessages['create']);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Bike $bike
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Bike $bike)
    {
        return view('content.bike.details', compact('bike'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Bike $bike
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Bike $bike)
    {
        $brands = Brand::all();

        return view('content.bike.update', compact('brands', 'bike'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\CreateBikeRequest $request
     * @param \App\Models\Bike                     $bike
     *
     * @return \Illuminate\Http\Response
     */
    public function update(CreateBikeRequest $request, Bike $bike)
    {
        $validator = $request->validated();

        $bike->update($validator);

        return redirect()
            ->route('bikes.edit', $bike)
            ->with('notify', $this->successMessages['update']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Bike $bike
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bike $bike)
    {
        $bike->delete();

        return redirect()
            ->route('bikes.index')
            ->with('notify', $this->successMessages['destroy']);
    }
}
