<?php

namespace App\Http\Controllers;

use App\Models\RndProcess;
use Illuminate\Http\Request;

class RNDProcessController extends Controller
{
    public function index()
    {
        // Mengambil semua RndProcess beserta RndProcessDetail-nya
        $processes = RndProcess::with('rndProcessDetails')->get();

        // Format response yang diinginkan
        $result = $processes->map(function($process) {
            return [
                'category' => $process->name,
                'details' => $process->rndProcessDetails->map(function($detail) {
                    return [
                        'name' => $detail->name,
                    ];
                })
            ];
        });

        return response()->json($result);
    }
}
