<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use Illuminate\Http\Request;

class WeddingController extends Controller
{
    public function edit()
    {
        $wedding = Wedding::first() ?? new Wedding();

        return view('admin.wedding', compact('wedding'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'groom_name'    => ['required', 'string', 'max:100'],
            'bride_name'    => ['required', 'string', 'max:100'],
            'akad_date'     => ['nullable', 'date'],
            'akad_time'     => ['nullable', 'date_format:H:i'],
            'resepsi_date'  => ['nullable', 'date'],
            'resepsi_time'  => ['nullable', 'date_format:H:i'],
            'venue_name'    => ['nullable', 'string', 'max:200'],
            'venue_address' => ['nullable', 'string'],
            'gmaps_link'    => ['nullable', 'url'],
            'rsvp_whatsapp' => ['nullable', 'string', 'max:20'],
            'love_quote'    => ['nullable', 'string'],
        ]);

        Wedding::updateOrCreate(['id' => 1], $validated);

        return redirect()->route('admin.wedding')->with('success', 'Wedding details saved.');
    }
}
