<?php

namespace App\Http\Controllers\Bike;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBikeRequest;
use App\Models\Brand;
use App\Models\Bike;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BikeController extends Controller
{
    /**
     * Success messages to send.
     * 
     * @var array
     */
    private $successMessages = [
        'create' => [
            'success' => 'Tao loai xe moi thanh cong.'
        ],
        'update' => [
            'success' => 'Sua loai xe thanh cong.'
        ],
        'destroy' => [
            'success' => 'Xoa loai xe thanh cong.'
        ]
    ];

    /**
     * Number of records to display per page.
     * 
     * @var int
     */
    private $resultsPerPage = 10;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $bikes = Bike::paginate($this->resultsPerPage);
        return view('content.bike.dashboard', compact('bikes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $brands = Brand::all();
        return view('content.bike.create', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateBikeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBikeRequest $request) {
        $validator = $request->validated();

        $new_bike = Bike::create([
            'brand_id' => $validator['brand_id'],
            'bike_name' => $validator['bike_name'],
            'bike_description' => $validator['bike_description'],
            'created_by_user' => Auth::id(),
            'updated_by_user' => Auth::id()
        ]);

        $new_bike_stock = Stock::create([
            'bike_id' => $new_bike->id,
            'stock' => $validator['stock'],
            'buy_price' => $validator['buy_price'],
            'sell_price' => $validator['sell_price']
        ]);

        return redirect()
            ->route('bikes.show', $new_bike)
            ->with('notify', $this->successMessages['create']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bike  $bike
     * @return \Illuminate\Http\Response
     */
    public function show(Bike $bike) {
        $stock = $bike->stock;
        return view('content.bike.details', compact('bike', 'stock'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bike  $bike
     * @return \Illuminate\Http\Response
     */
    public function edit(Bike $bike) {
        $brands = Brand::all();
        return view('content.bike.update', compact('brands', 'bike'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CreateBikeRequest  $request
     * @param  \App\Models\Bike  $bike
     * @return \Illuminate\Http\Response
     */
    public function update(CreateBikeRequest $request, Bike $bike) {
        $validator = $request->validated();

        $bike->update($validator);

        $bike->stock->first()->update($validator);

        $bike->updated_by_user = Auth::id();

        $bike->save();

        return redirect()
            ->route('bikes.edit', $bike)
            ->with('notify', $this->successMessages['update']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bike  $bike
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bike $bike) {
        $bike->updated_by_user = Auth::id();

        $bike->save();

        $bike->delete();

        return redirect()
            ->route('bikes.index')
            ->with('notify', $this->successMessages['destroy']);
    }
}
