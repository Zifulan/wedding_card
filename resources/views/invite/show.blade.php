<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Invitation — {{ $guest->wedding->groom_name }} & {{ $guest->wedding->bride_name }}</title>
    <meta property="og:title" content="You're Invited — {{ $guest->wedding->groom_name }} & {{ $guest->wedding->bride_name }}">
    <meta property="og:description" content="Dengan hormat kami mengundang Anda ke pernikahan kami.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #0a0a0f;
            --card: #1a1a2e;
            --gold: #c9a84c;
            --gold-light: #e8c97a;
            --gold-dim: rgba(201,168,76,0.3);
            --text: #e8e8f0;
            --muted: #6b7280;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Wrapper */
        .invite-wrapper {
            max-width: 480px;
            margin: 0 auto;
            padding: 0 0 60px;
            position: relative;
        }

        /* Fade-in animation */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .fade-in { animation: fadeUp 0.8s ease both; }
        .delay-1 { animation-delay: .15s; }
        .delay-2 { animation-delay: .30s; }
        .delay-3 { animation-delay: .50s; }
        .delay-4 { animation-delay: .70s; }
        .delay-5 { animation-delay: .90s; }
        .delay-6 { animation-delay: 1.1s; }

        /* Gold shimmer background band */
        .hero-band {
            background: linear-gradient(180deg, #0d0d18 0%, var(--bg) 100%);
            padding: 48px 28px 32px;
            text-align: center;
            position: relative;
        }

        /* Decorative SVG divider */
        .divider { width: 100%; margin: 24px 0; }

        /* Addressing */
        .addressing {
            padding: 0 28px;
            text-align: center;
        }

        .addressing .kepada {
            font-size: 12px;
            letter-spacing: .15em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 500;
        }

        .addressing .guest-name {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: var(--gold);
            margin-top: 6px;
            line-height: 1.2;
        }

        /* Opening verse */
        .verse-section {
            margin: 32px 28px;
            padding: 24px;
            background: var(--card);
            border: 1px solid var(--gold-dim);
            border-radius: 16px;
            text-align: center;
        }

        .arabic {
            font-family: 'Amiri', serif;
            font-size: 24px;
            color: var(--gold);
            line-height: 2;
            direction: rtl;
        }

        .verse-translation {
            margin-top: 12px;
            font-size: 12px;
            color: var(--muted);
            font-style: italic;
            line-height: 1.7;
        }

        /* Intro text */
        .intro-text {
            text-align: center;
            padding: 0 28px;
            font-size: 13px;
            color: var(--muted);
            line-height: 1.8;
        }

        /* Names */
        .names-section {
            text-align: center;
            padding: 28px 28px 12px;
        }

        .couple-name {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.15;
        }

        .couple-name .gold { color: var(--gold); }

        .ampersand {
            font-family: 'Playfair Display', serif;
            font-size: 52px;
            color: var(--gold-dim);
            line-height: 1;
            display: block;
            margin: 4px 0;
        }

        /* Event cards */
        .events-section { padding: 12px 28px 0; }

        .event-card {
            background: var(--card);
            border: 1px solid var(--gold-dim);
            border-radius: 14px;
            padding: 22px 24px;
            margin-bottom: 14px;
        }

        .event-type {
            font-size: 10px;
            letter-spacing: .15em;
            text-transform: uppercase;
            color: var(--gold);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .event-date {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--text);
        }

        .event-time {
            font-size: 13px;
            color: var(--muted);
            margin-top: 2px;
        }

        .event-venue {
            margin-top: 10px;
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
        }

        .event-venue strong { color: var(--text); }

        /* Buttons */
        .btn-maps {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 20px 28px 0;
            padding: 12px 24px;
            background: var(--card);
            border: 1px solid var(--gold-dim);
            border-radius: 10px;
            color: var(--gold);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all .2s;
            width: calc(100% - 56px);
            justify-content: center;
        }

        .btn-maps:hover { background: rgba(201,168,76,0.1); border-color: var(--gold); }

        /* Love quote */
        .love-quote {
            margin: 28px 28px;
            padding: 22px 24px;
            background: var(--card);
            border-left: 3px solid var(--gold);
            border-radius: 0 12px 12px 0;
            font-family: 'Playfair Display', serif;
            font-style: italic;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.8;
        }

        /* RSVP Section */
        .rsvp-section {
            margin: 28px 28px 0;
            padding: 24px;
            background: var(--card);
            border: 1px solid var(--gold-dim);
            border-radius: 16px;
        }

        .rsvp-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--gold);
            margin-bottom: 4px;
        }

        .rsvp-subtitle { font-size: 12px; color: var(--muted); margin-bottom: 20px; }

        .radio-group { display: flex; flex-direction: column; gap: 10px; margin-bottom: 18px; }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border: 1px solid var(--gold-dim);
            border-radius: 10px;
            cursor: pointer;
            transition: all .2s;
            font-size: 14px;
        }

        .radio-label:has(input:checked) {
            border-color: var(--gold);
            background: rgba(201,168,76,0.08);
            color: var(--gold);
        }

        .radio-label input { accent-color: var(--gold); width: 16px; height: 16px; }

        .rsvp-textarea {
            width: 100%;
            padding: 12px 14px;
            background: #0f0f1a;
            border: 1px solid var(--gold-dim);
            border-radius: 10px;
            color: var(--text);
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            resize: vertical;
            min-height: 90px;
            transition: border-color .2s;
            margin-bottom: 16px;
        }

        .rsvp-textarea:focus { outline: none; border-color: var(--gold); }
        .rsvp-textarea::placeholder { color: var(--muted); }

        .btn-rsvp {
            width: 100%;
            padding: 14px;
            background: var(--gold);
            color: #0a0a0f;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .02em;
            transition: background .2s;
        }

        .btn-rsvp:hover { background: var(--gold-light); }

        .rsvp-success {
            text-align: center;
            padding: 20px;
        }

        .rsvp-success .check {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .rsvp-success p {
            color: var(--text);
            font-size: 15px;
            line-height: 1.6;
        }

        /* Closing */
        .closing {
            text-align: center;
            padding: 32px 28px 20px;
        }

        .closing p { font-size: 13px; color: var(--muted); line-height: 1.8; }

        /* Print button */
        .btn-print {
            display: block;
            margin: 16px 28px 0;
            padding: 11px;
            background: transparent;
            border: 1px solid var(--gold-dim);
            border-radius: 10px;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all .2s;
            width: calc(100% - 56px);
        }

        .btn-print:hover { border-color: var(--gold); color: var(--gold); }

        /* Footer */
        .invite-footer {
            padding: 20px 28px;
            text-align: center;
        }

        .invite-footer p { font-size: 11px; color: var(--muted); }

        /* Print styles */
        @media print {
            body { background: white; color: #1a1a2e; }
            .invite-wrapper { max-width: 100%; }
            .btn-maps, .btn-print, .rsvp-section, .btn-rsvp { display: none !important; }
            .event-card, .verse-section, .love-quote { background: #f8f8ff; border-color: #c9a84c; }
            .couple-name { color: #1a1a2e; }
            .couple-name .gold, .rsvp-title, .event-type, .arabic { color: #c9a84c; }
            .addressing .guest-name { color: #c9a84c; }
        }
    </style>
</head>
<body>
<div class="invite-wrapper">

    {{-- Gold decorative header SVG --}}
    <div class="hero-band fade-in">
        <svg class="divider" viewBox="0 0 400 60" fill="none" xmlns="http://www.w3.org/2000/svg">
            <line x1="0" y1="30" x2="155" y2="30" stroke="#c9a84c" stroke-width="0.5" stroke-dasharray="4 3"/>
            <g transform="translate(200,30)">
                <circle r="12" fill="none" stroke="#c9a84c" stroke-width="0.8"/>
                <circle r="6" fill="none" stroke="#c9a84c" stroke-width="0.8"/>
                <circle r="2" fill="#c9a84c"/>
                <line x1="-22" y1="0" x2="-14" y2="0" stroke="#c9a84c" stroke-width="0.8"/>
                <line x1="14" y1="0" x2="22" y2="0" stroke="#c9a84c" stroke-width="0.8"/>
                <g transform="rotate(45)"><circle r="12" fill="none" stroke="#c9a84c" stroke-width="0.4" stroke-dasharray="2 4"/></g>
                <g transform="rotate(-45)"><circle r="12" fill="none" stroke="#c9a84c" stroke-width="0.4" stroke-dasharray="2 4"/></g>
            </g>
            <line x1="245" y1="30" x2="400" y2="30" stroke="#c9a84c" stroke-width="0.5" stroke-dasharray="4 3"/>
        </svg>

        <p style="font-size:11px; letter-spacing:.2em; text-transform:uppercase; color:var(--muted); margin-bottom:6px">
            Wedding Invitation
        </p>
        <p style="font-size:11px; color:var(--muted); letter-spacing:.1em; text-transform:uppercase;">
            Undangan Pernikahan
        </p>
    </div>

    {{-- Addressing --}}
    <div class="addressing fade-in delay-1">
        <p class="kepada">Kepada Yth. / Dear,</p>
        <p class="guest-name">{{ $guest->name }}</p>
    </div>

    {{-- Opening verse --}}
    <div class="verse-section fade-in delay-2">
        <p class="arabic">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
        <p class="verse-translation">
            "Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, supaya kamu cenderung dan merasa tenteram kepadanya."<br>
            <span style="color:#444d60">— QS. Ar-Rum: 21</span>
        </p>
    </div>

    {{-- Intro --}}
    <div class="intro-text fade-in delay-3">
        <p>
            Dengan penuh rasa syukur dan kebahagiaan,<br>
            kami mengundang Anda untuk turut merayakan<br>
            pernikahan putra-putri kami.
        </p>
        <br>
        <p style="color:#555e72; font-size:12px">
            With gratitude and joy, we humbly invite you<br>
            to the wedding celebration of
        </p>
    </div>

    {{-- Names --}}
    <div class="names-section fade-in delay-3">
        <span class="ampersand">&amp;</span>
        <div class="couple-name">
            <span class="gold">{{ $guest->wedding->groom_name }}</span>
        </div>
        <div style="font-size:16px; color:var(--muted); margin:8px 0; font-family:'Playfair Display',serif; font-style:italic">
            &amp;
        </div>
        <div class="couple-name">
            <span class="gold">{{ $guest->wedding->bride_name }}</span>
        </div>
    </div>

    {{-- Decorative divider --}}
    <svg class="divider fade-in delay-3" style="margin:24px 0" viewBox="0 0 400 30" fill="none">
        <line x1="20" y1="15" x2="175" y2="15" stroke="#c9a84c" stroke-width="0.5" stroke-dasharray="3 3"/>
        <circle cx="200" cy="15" r="5" fill="none" stroke="#c9a84c" stroke-width="0.8"/>
        <circle cx="200" cy="15" r="2" fill="#c9a84c"/>
        <line x1="225" y1="15" x2="380" y2="15" stroke="#c9a84c" stroke-width="0.5" stroke-dasharray="3 3"/>
    </svg>

    {{-- Events --}}
    <div class="events-section fade-in delay-4">

        @if($guest->wedding->akad_date)
        <div class="event-card">
            <p class="event-type">Akad Nikah / Ceremony</p>
            <p class="event-date">
                {{ $guest->wedding->akad_date->translatedFormat('l, d F Y') }}
            </p>
            @if($guest->wedding->akad_time)
            <p class="event-time">
                Pukul {{ \Carbon\Carbon::createFromTimeString($guest->wedding->akad_time)->format('H.i') }} WIB
            </p>
            @endif
            @if($guest->wedding->venue_name)
            <div class="event-venue">
                <strong>{{ $guest->wedding->venue_name }}</strong><br>
                {{ $guest->wedding->venue_address }}
            </div>
            @endif
        </div>
        @endif

        @if($guest->wedding->resepsi_date)
        <div class="event-card">
            <p class="event-type">Resepsi / Reception</p>
            <p class="event-date">
                {{ $guest->wedding->resepsi_date->translatedFormat('l, d F Y') }}
            </p>
            @if($guest->wedding->resepsi_time)
            <p class="event-time">
                Pukul {{ \Carbon\Carbon::createFromTimeString($guest->wedding->resepsi_time)->format('H.i') }} WIB
            </p>
            @endif
            @if($guest->wedding->venue_name)
            <div class="event-venue">
                <strong>{{ $guest->wedding->venue_name }}</strong><br>
                {{ $guest->wedding->venue_address }}
            </div>
            @endif
        </div>
        @endif

    </div>

    {{-- Google Maps button --}}
    @if($guest->wedding->gmaps_link)
    <a href="{{ $guest->wedding->gmaps_link }}" target="_blank" rel="noopener" class="btn-maps fade-in delay-4">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
        Lihat Lokasi / Open Maps
    </a>
    @endif

    {{-- Love quote --}}
    @if($guest->wedding->love_quote)
    <blockquote class="love-quote fade-in delay-5">
        "{{ $guest->wedding->love_quote }}"
    </blockquote>
    @endif

    {{-- RSVP Section --}}
    <div class="rsvp-section fade-in delay-5">

        @if(session('rsvp_success'))
            <div class="rsvp-success">
                <div class="check">✨</div>
                <p>
                    <strong style="color:var(--gold)">Terima kasih, {{ $guest->name }}!</strong><br>
                    Respons Anda telah kami terima.<br>
                    <span style="color:var(--muted); font-size:12px">Thank you — your RSVP has been recorded.</span>
                </p>
            </div>
        @else
            <p class="rsvp-title">Konfirmasi Kehadiran</p>
            <p class="rsvp-subtitle">Apakah Anda akan hadir? / Will you be attending?</p>

            <form method="POST" action="{{ route('invite.rsvp', $guest->unique_token) }}">
                @csrf

                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="status" value="hadir"
                            {{ $guest->rsvp?->status === 'hadir' ? 'checked' : '' }}>
                        <span>✓ &nbsp;Ya, saya akan hadir &nbsp;<span style="color:var(--muted); font-size:12px">/ Yes, I'll attend</span></span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="status" value="tidak hadir"
                            {{ $guest->rsvp?->status === 'tidak hadir' ? 'checked' : '' }}>
                        <span>✗ &nbsp;Maaf, saya tidak bisa hadir &nbsp;<span style="color:var(--muted); font-size:12px">/ Unable to attend</span></span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="status" value="ragu"
                            {{ (!$guest->rsvp || $guest->rsvp?->status === 'ragu') ? 'checked' : '' }}>
                        <span>? &nbsp;Belum pasti &nbsp;<span style="color:var(--muted); font-size:12px">/ Not sure yet</span></span>
                    </label>
                </div>

                @error('status')
                    <p style="color:#fca5a5; font-size:12px; margin-bottom:10px">{{ $message }}</p>
                @enderror

                <textarea name="message" class="rsvp-textarea"
                    placeholder="Pesan / Message (opsional)...">{{ old('message', $guest->rsvp?->message) }}</textarea>

                <button type="submit" class="btn-rsvp">
                    Kirim Konfirmasi / Send RSVP
                </button>
            </form>
        @endif
    </div>

    {{-- Closing --}}
    <div class="closing fade-in delay-6">
        <svg viewBox="0 0 400 40" fill="none" style="width:100%; margin-bottom:20px">
            <line x1="0" y1="20" x2="160" y2="20" stroke="#c9a84c" stroke-width="0.4" stroke-dasharray="3 4"/>
            <path d="M190,10 Q200,5 210,10 Q220,15 200,25 Q180,15 190,10Z" fill="none" stroke="#c9a84c" stroke-width="0.7"/>
            <line x1="240" y1="20" x2="400" y2="20" stroke="#c9a84c" stroke-width="0.4" stroke-dasharray="3 4"/>
        </svg>

        <p>
            Merupakan suatu kehormatan dan kebahagiaan bagi kami<br>
            apabila Bapak / Ibu / Saudara/i berkenan hadir<br>
            dan memberikan do'a restu kepada kami.
        </p>
        <br>
        <p style="color:#444d60; font-size:12px">
            Your presence and blessings would mean the world to us.<br>
            We look forward to celebrating this special day with you.
        </p>

        <br>

        <p style="font-family:'Playfair Display',serif; font-size:16px; color:var(--gold); font-style:italic; margin-top:8px">
            Wassalamu'alaikum Warahmatullahi Wabarakatuh
        </p>

        <br>

        <p>
            Hormat kami,<br>
            <strong style="color:var(--text); font-family:'Playfair Display',serif">
                {{ $guest->wedding->groom_name }} &amp; {{ $guest->wedding->bride_name }}
            </strong>
        </p>
    </div>

    {{-- Save as PDF button --}}
    <button class="btn-print fade-in delay-6" onclick="window.print()">
        💾 &nbsp;Simpan sebagai PDF / Save as PDF
    </button>

    {{-- Footer --}}
    <div class="invite-footer fade-in delay-6">
        <svg viewBox="0 0 400 24" fill="none" style="width:100%; margin-bottom:12px">
            <line x1="0" y1="12" x2="180" y2="12" stroke="#c9a84c" stroke-width="0.3"/>
            <circle cx="200" cy="12" r="3" fill="#c9a84c" opacity="0.5"/>
            <line x1="220" y1="12" x2="400" y2="12" stroke="#c9a84c" stroke-width="0.3"/>
        </svg>
        <p>Made with love &hearts;</p>
    </div>

</div>
</body>
</html>
