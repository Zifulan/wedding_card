<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Rsvp;
use App\Models\Wedding;

class DashboardController extends Controller
{
    public function index()
    {
        $wedding = Wedding::first();

        $totalGuests    = Guest::count();
        $confirmed      = Rsvp::where('status', 'hadir')->count();
        $declined       = Rsvp::where('status', 'tidak hadir')->count();
        $pending        = $totalGuests - Rsvp::count();
        $totalOpens     = Guest::sum('open_count');
        $notYetOpened   = Guest::where('open_count', 0)->count();

        $recentRsvps = Rsvp::with('guest')
            ->orderByDesc('submitted_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'wedding',
            'totalGuests', 'confirmed', 'declined', 'pending',
            'totalOpens', 'notYetOpened', 'recentRsvps'
        ));
    }
}
