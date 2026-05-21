@extends('layouts.admin')
@section('title', 'RSVP List')

@section('content')
<div class="page-header">
    <h2 class="page-title">RSVP List</h2>
    <p class="page-subtitle">All guest responses</p>
</div>

{{-- Filter tabs --}}
<div style="display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
    @foreach(['all' => 'All', 'hadir' => 'Hadir', 'tidak hadir' => 'Tidak Hadir', 'ragu' => 'Ragu'] as $val => $label)
        <a href="{{ route('admin.rsvp', ['status' => $val]) }}"
            style="padding:7px 16px; border-radius:20px; font-size:13px; text-decoration:none; font-weight:500;
                   {{ $filter === $val
                        ? 'background:var(--gold); color:#0a0a0f;'
                        : 'background:var(--card); border:1px solid var(--border); color:var(--muted);' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<div class="card">
    @if($rsvps->isEmpty())
        <p style="color:var(--muted); font-size:13px; text-align:center; padding:30px 0">
            No RSVPs found for this filter.
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
                    @foreach($rsvps as $rsvp)
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
                        <td style="color:var(--muted); max-width:300px">{{ $rsvp->message ?: '—' }}</td>
                        <td style="color:var(--muted); white-space:nowrap">
                            {{ $rsvp->submitted_at?->format('d M Y, H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $rsvps->links('vendor.pagination.simple') }}
        </div>
    @endif
</div>
@endsection
