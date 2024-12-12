<?php

namespace App\Http\Controllers;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuManagementController extends Controller
{
    /**
     * Display a listing of the menus.
     */
    public function index()
    {
        $menus = Menu::with('children')->whereNull('parent_id')->get();

        return response()->json([
            'success' => true,
            'data' => $menus,
        ]);
    }

    /**
     * Store a newly created menu item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $menu = Menu::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu created successfully.',
            'data' => $menu,
        ], 201);
    }

    /**
     * Display the specified menu item.
     */
    public function show($id)
    {
        $menu = Menu::with('children')->find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $menu,
        ]);
    }

    /**
     * Update the specified menu item.
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $menu->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu updated successfully.',
            'data' => $menu,
        ]);
    }

    /**
     * Remove the specified menu item.
     */
    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found.',
            ], 404);
        }

        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu deleted successfully.',
        ]);
    }
}
