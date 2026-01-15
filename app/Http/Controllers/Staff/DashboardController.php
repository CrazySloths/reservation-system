<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the Staff dashboard.
     */
    public function index()
    {
        return view('staff.dashboard', [
            'title' => 'Staff Dashboard',
            'user' => auth()->user(),
        ]);
    }
}

