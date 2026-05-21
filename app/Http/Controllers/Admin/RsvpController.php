<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rsvp;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('status', 'all');

        $query = Rsvp::with('guest')->orderByDesc('submitted_at');

        if (in_array($filter, ['hadir', 'tidak hadir', 'ragu'])) {
            $query->where('status', $filter);
        }

        $rsvps = $query->paginate(50)->withQueryString();

        return view('admin.rsvp', compact('rsvps', 'filter'));
    }
}
