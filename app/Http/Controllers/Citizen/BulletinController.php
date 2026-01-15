<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BulletinController extends Controller
{
    /**
     * Display bulletin board for citizens
     */
    public function index(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $type = $request->get('type', 'all');
        $search = $request->get('search', '');

        // Base query for active announcements targeting citizens
        $query = DB::connection('facilities_db')
            ->table('announcements')
            ->select('announcements.*')
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('target_audience', 'citizens')
                  ->orWhere('target_audience', 'all');
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->where('start_date', '<=', now()->toDateString());

        // Filter by type
        if ($type !== 'all') {
            $query->where('type', $type);
        }

        // Search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Order by pinned first, then by priority and date
        $announcements = $query->orderBy('is_pinned', 'desc')
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get type counts for filter badges
        $typeCounts = [
            'all' => DB::connection('facilities_db')
                ->table('announcements')
                ->where('is_active', true)
                ->where(function($q) {
                    $q->where('target_audience', 'citizens')
                      ->orWhere('target_audience', 'all');
                })
                ->where(function($q) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', now()->toDateString());
                })
                ->where('start_date', '<=', now()->toDateString())
                ->count(),
            'general' => DB::connection('facilities_db')
                ->table('announcements')
                ->where('is_active', true)
                ->where('type', 'general')
                ->where(function($q) {
                    $q->where('target_audience', 'citizens')
                      ->orWhere('target_audience', 'all');
                })
                ->count(),
            'maintenance' => DB::connection('facilities_db')
                ->table('announcements')
                ->where('is_active', true)
                ->where('type', 'maintenance')
                ->count(),
            'event' => DB::connection('facilities_db')
                ->table('announcements')
                ->where('is_active', true)
                ->where('type', 'event')
                ->count(),
            'urgent' => DB::connection('facilities_db')
                ->table('announcements')
                ->where('is_active', true)
                ->where('type', 'urgent')
                ->count(),
        ];

        return view('citizen.bulletin.index', compact('announcements', 'type', 'typeCounts'));
    }

    /**
     * Display specific announcement details
     */
    public function show($id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $announcement = DB::connection('facilities_db')
            ->table('announcements')
            ->where('id', $id)
            ->where('is_active', true)
            ->first();

        if (!$announcement) {
            return redirect()->route('citizen.bulletin')->with('error', 'Announcement not found.');
        }

        // Get related announcements (same type, excluding current)
        $relatedAnnouncements = DB::connection('facilities_db')
            ->table('announcements')
            ->where('type', $announcement->type)
            ->where('id', '!=', $id)
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('target_audience', 'citizens')
                  ->orWhere('target_audience', 'all');
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('citizen.bulletin.show', compact('announcement', 'relatedAnnouncements'));
    }

    /**
     * Download announcement attachment
     */
    public function downloadAttachment($id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $announcement = DB::connection('facilities_db')
            ->table('announcements')
            ->where('id', $id)
            ->where('is_active', true)
            ->first();

        if (!$announcement || !$announcement->attachment_path) {
            return redirect()->back()->with('error', 'Attachment not found.');
        }

        $filePath = storage_path('app/public/' . $announcement->attachment_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return response()->download($filePath);
    }
}

