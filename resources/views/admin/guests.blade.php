@extends('layouts.admin')
@section('title', 'Guest Management')

@section('content')
<div class="page-header" style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 class="page-title">Guest Management</h2>
        <p class="page-subtitle">Manage your guest list and invitation links</p>
    </div>
    <a href="{{ route('admin.guests.export') }}" class="btn btn-outline">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export CSV
    </a>
</div>

{{-- Bulk Import --}}
<div class="card" style="margin-bottom:24px;">
    <h3 style="font-family:'Playfair Display',serif; color:var(--gold); font-size:16px; margin-bottom:14px;">
        Bulk Import Guests
    </h3>
    <form method="POST" action="{{ route('admin.guests.import') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Guest Names (one per line, max 50)</label>
            <textarea name="names" class="form-control" rows="6"
                placeholder="Ahmad Fauzi&#10;Siti Rahayu&#10;Budi Santoso"></textarea>
            @error('names') <div style="color:#fca5a5; font-size:12px; margin-top:4px">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-gold">Import Guests</button>
    </form>
</div>

{{-- Guest Table --}}
<div class="card">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <h3 style="font-family:'Playfair Display',serif; color:var(--gold); font-size:16px;">
            Guest List ({{ $guests->total() }})
        </h3>
    </div>

    @if($guests->isEmpty())
        <p style="color:var(--muted); font-size:13px; text-align:center; padding:30px 0">
            No guests yet. Import some above.
        </p>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Opens</th>
                        <th>Last Opened</th>
                        <th>RSVP</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guests as $guest)
                    <tr>
                        <td>{{ $guest->name }}</td>
                        <td>{{ $guest->open_count }}</td>
                        <td style="color:var(--muted); white-space:nowrap">
                            {{ $guest->last_opened_at?->format('d M, H:i') ?? '—' }}
                        </td>
                        <td>
                            @if($guest->rsvp)
                                @if($guest->rsvp->status === 'hadir')
                                    <span class="badge badge-hadir">Hadir</span>
                                @elseif($guest->rsvp->status === 'tidak hadir')
                                    <span class="badge badge-tidak">Tidak Hadir</span>
                                @else
                                    <span class="badge badge-ragu">Ragu</span>
                                @endif
                            @else
                                <span class="badge badge-pending">Belum</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <button class="btn btn-outline btn-sm"
                                    onclick="copyUrl('{{ $guest->invite_url }}', this)">
                                    Copy URL
                                </button>
                                <button class="btn btn-outline btn-sm"
                                    onclick="showQr('{{ $guest->name }}', '{{ $guest->invite_url }}')">
                                    QR
                                </button>
                                <form method="POST" action="{{ route('admin.guests.destroy', $guest) }}"
                                    onsubmit="return confirm('Remove {{ addslashes($guest->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $guests->links('vendor.pagination.simple') }}
        </div>
    @endif
</div>

{{-- QR Modal --}}
<div class="modal-overlay" id="qrModal">
    <div class="modal">
        <h3 id="qrName"></h3>
        <canvas id="qrCanvas"></canvas>
        <div style="margin-top:16px; display:flex; gap:10px; justify-content:center;">
            <button class="btn btn-gold btn-sm" onclick="downloadQr()">Download PNG</button>
            <button class="btn btn-outline btn-sm" onclick="closeQr()">Close</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
let currentQrUrl = '';

function copyUrl(url, btn) {
    navigator.clipboard.writeText(url).then(() => {
        const orig = btn.textContent;
        btn.textContent = 'Copied!';
        btn.style.color = '#86efac';
        btn.style.borderColor = '#86efac';
        setTimeout(() => { btn.textContent = orig; btn.style.color = ''; btn.style.borderColor = ''; }, 1500);
    });
}

function showQr(name, url) {
    currentQrUrl = url;
    document.getElementById('qrName').textContent = name;
    const canvas = document.getElementById('qrCanvas');
    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    // Remove old QRCode instance
    canvas.replaceWith(canvas.cloneNode(false));

    new QRCode(document.getElementById('qrCanvas'), {
        text: url,
        width: 220,
        height: 220,
        colorDark: '#c9a84c',
        colorLight: '#1a1a2e',
        correctLevel: QRCode.CorrectLevel.H,
    });
    document.getElementById('qrModal').classList.add('open');
}

function closeQr() {
    document.getElementById('qrModal').classList.remove('open');
}

function downloadQr() {
    const canvas = document.querySelector('#qrCanvas canvas') || document.getElementById('qrCanvas');
    const link = document.createElement('a');
    link.download = 'qr-invite.png';
    link.href = canvas.toDataURL ? canvas.toDataURL() : canvas.src;
    link.click();
}

document.getElementById('qrModal').addEventListener('click', function(e) {
    if (e.target === this) closeQr();
});
</script>
@endsection
