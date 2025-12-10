<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DirectionController extends Controller
{
    public function setDirection(Request $request)
    {
        $direction = $request->input('direction', 'ltr');
        
        if (!in_array($direction, ['ltr', 'rtl'])) {
            return response()->json(['error' => 'Invalid direction'], 400);
        }

        return redirect()->back()->cookie('direction', $direction, 60 * 24 * 365);
    }
}

