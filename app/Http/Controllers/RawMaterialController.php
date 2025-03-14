<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
{
    public function index()
    {
        $rawMaterials = RawMaterial::all();

        return response()->json([
            'status' => 'success',
            'data' => $rawMaterials
        ]);
    }
}
