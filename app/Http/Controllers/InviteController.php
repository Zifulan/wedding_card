<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Rsvp;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    public function show(string $token)
    {
        $guest = Guest::with(['wedding', 'rsvp'])
            ->where('unique_token', $token)
            ->firstOrFail();

        $guest->increment('open_count');
        $guest->last_opened_at = now();
        $guest->save();

        return view('invite.show', compact('guest'));
    }

    public function rsvp(Request $request, string $token)
    {
        $guest = Guest::where('unique_token', $token)->firstOrFail();

        $validated = $request->validate([
            'status'  => ['required', 'in:hadir,tidak hadir,ragu'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        Rsvp::updateOrCreate(
            ['guest_id' => $guest->id],
            [
                'wedding_id'   => $guest->wedding_id,
                'status'       => $validated['status'],
                'message'      => $validated['message'] ?? null,
                'submitted_at' => now(),
            ]
        );

        return back()->with('rsvp_success', true);
    }
}
