<?php

namespace App\Http\Controllers\Plate;

use App\Http\Controllers\Controller;
use App\Models\Plate;
use App\Http\Requests\StorePlateRequest;
use App\Http\Requests\UpdatePlateRequest;
use App\Models\Restaurant;

class PlateController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        $plates = $restaurant->plates()->paginate();
        return jsonResponse($plates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlateRequest $request, Restaurant $restaurant)
    {
        $plate = $restaurant->plates()->create($request->validated());
        return jsonResponse($plate, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant, Plate $plate)
    {
        $plate = $restaurant->plates()->findOrFail($plate->id);
        return jsonResponse($plate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlateRequest $request, Restaurant $restaurant, Plate $plate)
    {
        $plate = $restaurant->plates()->findOrFail($plate->id);
        $plate->update($request->validated());
        return jsonResponse($plate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Plate $plate)
    {
        $plate = $restaurant->plates()->findOrFail($plate->id);
        $plate->delete();
        return jsonResponse();
    }
}