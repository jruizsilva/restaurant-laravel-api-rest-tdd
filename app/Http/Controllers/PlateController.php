<?php

namespace App\Http\Controllers;

use App\Models\Plate;
use App\Http\Requests\StorePlateRequest;
use App\Http\Requests\UpdatePlateRequest;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class PlateController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        Gate::authorize('viewPlates', $restaurant);
        $plates = $restaurant->plates()->paginate();
        return jsonResponse($plates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlateRequest $request, Restaurant $restaurant)
    {
        Gate::authorize('addPlate', $restaurant);
        $plate = $restaurant->plates()->create($request->validated());
        return jsonResponse($plate, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant, Plate $plate)
    {
        Gate::authorize('viewPlate', $restaurant);
        $plate = $restaurant->plates()->findOrFail($plate->id);
        return jsonResponse($plate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlateRequest $request, Restaurant $restaurant, Plate $plate)
    {
        Gate::authorize('editPlate', $restaurant);
        $plate = $restaurant->plates()->findOrFail($plate->id);
        $plate->update($request->validated());
        return jsonResponse($plate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Plate $plate)
    {
        Gate::authorize('deletePlate', $restaurant);
        $plate = $restaurant->plates()->findOrFail($plate->id);
        $plate->delete();
        return jsonResponse();
    }
}