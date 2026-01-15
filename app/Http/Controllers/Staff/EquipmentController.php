<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    /**
     * Display a listing of equipment (read-only for staff).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $query = DB::connection('facilities_db')->table('equipment_items')
            ->where('is_available', 1);

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($category) {
            $query->where('category', $category);
        }

        $equipment = $query->orderBy('category')
            ->orderBy('name')
            ->paginate(15);

        // Categories
        $categories = [
            'chairs' => 'Chairs',
            'tables' => 'Tables',
            'sound_system' => 'Sound System',
            'lighting' => 'Lighting',
            'decoration' => 'Decoration',
            'kitchen_equipment' => 'Kitchen Equipment',
            'sports_equipment' => 'Sports Equipment',
            'stage_equipment' => 'Stage Equipment',
            'tents_canopy' => 'Tents & Canopy',
            'other' => 'Other'
        ];

        return view('staff.equipment.index', compact(
            'equipment',
            'categories',
            'search',
            'category'
        ));
    }
}

