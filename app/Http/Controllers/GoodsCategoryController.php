<?php

namespace App\Http\Controllers;
use App\Models\GoodsCategories;
use Illuminate\Http\Request;

class GoodsCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(GoodsCategories::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $goods_categories = GoodsCategories::create($validated);
        return response()->json($goods_categories, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(GoodsCategories $goods_category)
    {
        return response()->json($goods_category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $goods_categories->update($validated);
        return response()->json($goods_categories);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodsCategories $goods_category)
    {
        $goods_category->delete();
        return response()->json(null, 204);
    }
}
