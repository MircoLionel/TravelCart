<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $tours = Tour::where('is_active', true)
            ->with(['dates' => function ($q) {
                $q->where('is_active', true)
                  ->whereDate('start_date', '>=', now()->toDateString())
                  ->orderBy('start_date');
            }])
            ->orderBy('title')
            ->paginate(9);

        return view('tours.index', compact('tours'));
    }

    public function show(Tour $tour)
    {
        abort_unless($tour->is_active, 404);

        $tour->load(['dates' => function ($q) {
            $q->where('is_active', true)
              ->whereDate('start_date', '>=', now()->toDateString())
              ->orderBy('start_date');
        }]);

        return view('tours.show', compact('tour'));
    }
}
