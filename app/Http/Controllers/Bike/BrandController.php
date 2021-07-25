<?php

namespace App\Http\Controllers\Bike;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BrandController extends Controller
{
    /**
     * Delete a brand.
     */
    public function delete(int $id) {
        // do nothing.
        echo $id . ' is going to be deleted!';
    }

    /**
     * Store (add) a new brand.
     */
    public function store() {

    }

    /**
     * Update a brand.
     * 
     */
    public function update(int $id) {
        return $id . ' is being edited!';
    }

    public function view() {
        return view('content.brand.view');
    }

    public function viewId(int $id) {
        return view('content.brand.view', [
            'id' => $id
        ]);
    }

    public function add() {
        return view('content.brand.add');
    }

    public function edit(int $id) {
        return view('content.brand.edit', [
            'id' => $id
        ]);
    }
}
