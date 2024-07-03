<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Restaurant;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Restaurant $restaurant)
    {
        $menus = $restaurant->menus()->paginate();
        return jsonResponse($menus);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuRequest $request, Restaurant $restaurant)
    {
        $menu = $restaurant->menus()->create($request->except("plates"));
        $menu->plates()->attach($request->plates);
        $menu->load("restaurant", "plates");
        return jsonResponse($menu, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant, Menu $menu)
    {
        $menu = $restaurant->menus()->with("plates")->findOrFail($menu->id);
        return jsonResponse($menu, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Restaurant $restaurant, Menu $menu)
    {
        $menu = $restaurant->menus()->findOrFail($menu->id);
        $menu->update($request->except("plates"));
        $menu->plates()->sync($request->plates);
        $menu->load("restaurant", "plates");
        return jsonResponse($menu);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Menu $menu)
    {
        $menu = $restaurant->menus()->findOrFail($menu->id);
        $menu->delete();
        return jsonResponse();
    }
}