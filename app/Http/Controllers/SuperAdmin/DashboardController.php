<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the Super Admin dashboard.
     */
    public function index()
    {
        return view('superadmin.dashboard', [
            'title' => 'Super Admin Dashboard',
            'user' => auth()->user(),
        ]);
    }
}

