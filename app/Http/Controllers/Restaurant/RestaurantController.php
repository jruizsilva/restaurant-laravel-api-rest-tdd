<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use Gate;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request("search") ?? "";
        $sortBy = request("sortBy") ?? "id";
        $sortDirection = request("sortDirection") ?? "asc";
        $restaurants = Auth::user()->restaurants()->search($search)->sort($sortBy, $sortDirection)->paginate();
        return jsonResponse($restaurants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRestaurantRequest $request)
    {
        $restaurant = Auth::user()->restaurants()->create($request->validated());

        return jsonResponse($restaurant, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant)
    {
        Gate::authorize("view", $restaurant);
        return jsonResponse($restaurant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant)
    {
        Gate::authorize("update", $restaurant);
        $restaurant->update($request->validated());
        return jsonResponse($restaurant, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        Gate::authorize("delete", $restaurant);
        $restaurant->delete();
        return jsonResponse();
    }
}