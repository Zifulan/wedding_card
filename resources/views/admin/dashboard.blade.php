@use('Illuminate\Support\Str')
@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h2 class="page-title">Dashboard</h2>
    <p class="page-subtitle">Overview of your wedding invitation</p>
</div>

@if(!$wedding)
    <div class="alert alert-error">
        No wedding details yet. <a href="{{ route('admin.wedding') }}" style="color:var(--gold)">Set up your wedding →</a>
    </div>
@else
    <div style="margin-bottom:16px; color:var(--muted); font-size:13px;">
        {{ $wedding->groom_name }} &amp; {{ $wedding->bride_name }}
        @if($wedding->resepsi_date)
            · {{ $wedding->resepsi_date->format('d F Y') }}
        @endif
    </div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Guests</div>
        <div class="stat-value">{{ $totalGuests }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Confirmed (Hadir)</div>
        <div class="stat-value" style="color:#86efac">{{ $confirmed }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Declined</div>
        <div class="stat-value" style="color:#fca5a5">{{ $declined }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending RSVP</div>
        <div class="stat-value" style="color:#fde68a">{{ $pending }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Opens</div>
        <div class="stat-value">{{ $totalOpens }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Not Opened</div>
        <div class="stat-value" style="color:var(--muted)">{{ $notYetOpened }}</div>
    </div>
</div>

<div class="card">
    <h3 style="font-family:'Playfair Display',serif; color:var(--gold); font-size:16px; margin-bottom:16px;">
        Recent RSVP Submissions
    </h3>

    @if($recentRsvps->isEmpty())
        <p style="color:var(--muted); font-size:13px; text-align:center; padding:20px 0">
            No RSVPs submitted yet.
        </p>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Status</th>
                        <th>Message</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentRsvps as $rsvp)
                    <tr>
                        <td>{{ $rsvp->guest->name ?? '—' }}</td>
                        <td>
                            @if($rsvp->status === 'hadir')
                                <span class="badge badge-hadir">Hadir</span>
                            @elseif($rsvp->status === 'tidak hadir')
                                <span class="badge badge-tidak">Tidak Hadir</span>
                            @else
                                <span class="badge badge-ragu">Ragu</span>
                            @endif
                        </td>
                        <td style="color:var(--muted); max-width:240px">{{ Str::limit($rsvp->message, 60) }}</td>
                        <td style="color:var(--muted); white-space:nowrap">
                            {{ $rsvp->submitted_at?->format('d M, H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
