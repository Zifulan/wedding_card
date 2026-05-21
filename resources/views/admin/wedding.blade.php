@extends('layouts.admin')
@section('title', 'Wedding Details')

@section('content')
<div class="page-header">
    <h2 class="page-title">Wedding Details</h2>
    <p class="page-subtitle">Set up your ceremony and reception information</p>
</div>

@if($errors->any())
    <div class="alert alert-error">
        @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
    </div>
@endif

<div class="card">
    <form method="POST" action="{{ route('admin.wedding.update') }}">
        @csrf

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
                <label class="form-label">Groom Name</label>
                <input type="text" name="groom_name" class="form-control"
                    value="{{ old('groom_name', $wedding->groom_name) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Bride Name</label>
                <input type="text" name="bride_name" class="form-control"
                    value="{{ old('bride_name', $wedding->bride_name) }}" required>
            </div>
        </div>

        <hr style="border-color:var(--border); margin:8px 0 24px">
        <p style="color:var(--gold); font-size:13px; font-weight:600; margin-bottom:16px; text-transform:uppercase; letter-spacing:.05em">Akad / Ceremony</p>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" name="akad_date" class="form-control"
                    value="{{ old('akad_date', $wedding->akad_date?->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Time</label>
                <input type="time" name="akad_time" class="form-control"
                    value="{{ old('akad_time', $wedding->akad_time) }}">
            </div>
        </div>

        <hr style="border-color:var(--border); margin:8px 0 24px">
        <p style="color:var(--gold); font-size:13px; font-weight:600; margin-bottom:16px; text-transform:uppercase; letter-spacing:.05em">Resepsi / Reception</p>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" name="resepsi_date" class="form-control"
                    value="{{ old('resepsi_date', $wedding->resepsi_date?->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Time</label>
                <input type="time" name="resepsi_time" class="form-control"
                    value="{{ old('resepsi_time', $wedding->resepsi_time) }}">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Venue Name</label>
            <input type="text" name="venue_name" class="form-control"
                value="{{ old('venue_name', $wedding->venue_name) }}">
        </div>

        <div class="form-group">
            <label class="form-label">Venue Address</label>
            <textarea name="venue_address" class="form-control" rows="3">{{ old('venue_address', $wedding->venue_address) }}</textarea>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
                <label class="form-label">Google Maps Link</label>
                <input type="url" name="gmaps_link" class="form-control"
                    value="{{ old('gmaps_link', $wedding->gmaps_link) }}" placeholder="https://maps.google.com/...">
            </div>
            <div class="form-group">
                <label class="form-label">RSVP WhatsApp Number</label>
                <input type="text" name="rsvp_whatsapp" class="form-control"
                    value="{{ old('rsvp_whatsapp', $wedding->rsvp_whatsapp) }}" placeholder="628123456789">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Love Quote / Opening Message</label>
            <textarea name="love_quote" class="form-control" rows="3" placeholder="And of His signs is that He created for you from yourselves mates...">{{ old('love_quote', $wedding->love_quote) }}</textarea>
        </div>

        <div style="display:flex; gap:12px; align-items:center; margin-top:8px;">
            <button type="submit" class="btn btn-gold">Save Wedding Details</button>
            @if($wedding->id)
                @php $sampleGuest = \App\Models\Guest::where('wedding_id', $wedding->id)->first(); @endphp
                @if($sampleGuest)
                    <a href="{{ route('invite.show', $sampleGuest->unique_token) }}" target="_blank" class="btn btn-outline">
                        Preview Invite →
                    </a>
                @endif
            @endif
        </div>
    </form>
</div>
@endsection
