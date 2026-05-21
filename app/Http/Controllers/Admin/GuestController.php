<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Wedding;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    public function index()
    {
        $guests = Guest::with('rsvp')->orderByDesc('created_at')->paginate(50);

        return view('admin.guests', compact('guests'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'names' => ['required', 'string'],
        ]);

        $wedding = Wedding::first();
        if (! $wedding) {
            return back()->with('error', 'Please create wedding details first.');
        }

        $lines = array_filter(
            array_map('trim', explode("\n", $request->input('names'))),
            fn ($l) => $l !== ''
        );

        $lines = array_slice($lines, 0, 50);

        foreach ($lines as $name) {
            $token = Str::uuid()->toString();
            $url   = route('invite.show', ['token' => $token]);

            Guest::create([
                'wedding_id'   => $wedding->id,
                'name'         => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                'unique_token' => $token,
                'invite_url'   => $url,
                'open_count'   => 0,
                'created_at'   => now(),
            ]);
        }

        return back()->with('success', count($lines) . ' guest(s) imported.');
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();

        return back()->with('success', 'Guest removed.');
    }

    public function export()
    {
        $guests = Guest::with('rsvp')->orderByDesc('created_at')->get();

        $csvRows = [];
        $csvRows[] = ['Name', 'Invite URL', 'Open Count', 'Last Opened', 'RSVP Status', 'RSVP Message'];

        foreach ($guests as $guest) {
            $csvRows[] = [
                $guest->name,
                $guest->invite_url,
                $guest->open_count,
                $guest->last_opened_at?->format('Y-m-d H:i') ?? '',
                $guest->rsvp?->status ?? 'Belum RSVP',
                $guest->rsvp?->message ?? '',
            ];
        }

        $output = fopen('php://temp', 'w');
        foreach ($csvRows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="guests_' . now()->format('Ymd') . '.csv"',
        ]);
    }
}
