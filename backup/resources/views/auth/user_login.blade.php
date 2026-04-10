<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — {{ config('app.name', 'Mirror CRM') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* ── Wrapper ─────────────────────────────── */
        .login-card {
            display: flex;
            width: 100%;
            max-width: 820px;
            min-height: 520px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(0,0,0,0.12);
        }

        /* ── Left panel ──────────────────────────── */
        .login-left {
            flex: 1;
            background: #0f1923;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 36px;
            position: relative;
            min-width: 220px;
        }

        .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.06;
            background-image:
                linear-gradient(rgba(255,255,255,0.6) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.6) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .brand-mark {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 1;
            text-decoration: none;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: #e8c97e;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: #fff;
            letter-spacing: 0.3px;
        }

        .left-tagline {
            position: relative;
            z-index: 1;
        }

        .left-tagline h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 600;
            color: #fff;
            margin: 0 0 10px;
            line-height: 1.35;
        }

        .left-tagline p {
            font-size: 13px;
            color: rgba(255,255,255,0.45);
            line-height: 1.65;
        }

        .status-pills {
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
            z-index: 1;
        }

        .status-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: rgba(255,255,255,0.55);
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── Right panel ─────────────────────────── */
        .login-right {
            flex: 1.2;
            background: #fff;
            padding: 44px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-heading {
            font-size: 24px;
            font-weight: 500;
            color: #0f1923;
            margin-bottom: 6px;
        }

        .login-sub {
            font-size: 13px;
            color: #7a7f87;
            margin-bottom: 30px;
        }

        /* Alert */
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #b91c1c;
            margin-bottom: 20px;
        }

        .alert-error ul { margin: 0; padding-left: 16px; }

        /* Fields */
        .field-group { margin-bottom: 18px; }

        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #7a7f87;
            margin-bottom: 6px;
        }

        .field-wrap { position: relative; }

        .field-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb2ba;
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        .field-input {
            width: 100%;
            height: 42px;
            padding: 0 14px 0 38px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: #0f1923;
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .field-input::placeholder { color: #adb2ba; }

        .field-input:focus {
            border-color: #e8c97e;
            box-shadow: 0 0 0 3px rgba(232,201,126,0.18);
            background: #fff;
        }

        .field-input.is-invalid {
            border-color: #f87171;
        }

        .field-error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 4px;
        }

        /* Remember row */
        .remember-row {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: #6b7280;
            cursor: pointer;
        }

        .remember-label input[type="checkbox"] {
            accent-color: #0f1923;
            width: 14px;
            height: 14px;
        }

        /* Submit button */
        .btn-signin {
            width: 100%;
            height: 44px;
            background: #0f1923;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: opacity 0.15s, transform 0.1s;
        }

        .btn-signin:hover   { opacity: 0.87; }
        .btn-signin:active  { transform: scale(0.99); }

        /* Footer */
        .login-footer {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #f0f0f0;
            text-align: center;
            font-size: 11px;
            color: #b0b5bc;
        }

        .role-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 8px;
            justify-content: center;
        }

        .role-chip {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            color: #b0b5bc;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .login-left { display: none; }
            .login-right { padding: 32px 24px; border-radius: 16px; }
            .login-card { max-width: 400px; }
        }
    </style>
</head>
<body>

<div class="login-card">

    {{-- ── Left decorative panel ─────────────────── --}}
    <div class="login-left">
        <a href="#" class="brand-mark">
            <div class="brand-icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M4 10 L10 4 L16 10 L10 16 Z" fill="#0f1923"/>
                    <circle cx="10" cy="10" r="3" fill="#0f1923"/>
                </svg>
            </div>
            <span class="brand-name">{{ config('app.name', 'Mirror CRM') }}</span>
        </a>

        <div class="left-tagline">
            <h2>Manage every lead,<br>end to end.</h2>
            <p>From first visit to final fitting —<br>track every step in one place.</p>
        </div>

        <div class="status-pills">
            <div class="status-pill">
                <div class="status-dot" style="background:#e8c97e;"></div>
                In Measurement
            </div>
            <div class="status-pill">
                <div class="status-dot" style="background:#5DCAA5;"></div>
                Quotation Approved
            </div>
            <div class="status-pill">
                <div class="status-dot" style="background:#85B7EB;"></div>
                Ready to Dispatch
            </div>
            <div class="status-pill">
                <div class="status-dot" style="background:#F0997B;"></div>
                Fitting Done
            </div>
        </div>
    </div>

    {{-- ── Right: form panel ────────────────────── --}}
    <div class="login-right">

        <p class="login-heading">Welcome back</p>
        <p class="login-sub">Sign in with your mobile number and password.</p>

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            {{-- Mobile Number --}}
            <div class="field-group">
                <label class="field-label" for="strUserMobile">Mobile Number</label>
                <div class="field-wrap">
                    <span class="field-icon">
                        <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="4" y="1" width="8" height="14" rx="1.5"/>
                            <circle cx="8" cy="12.5" r="0.7" fill="currentColor" stroke="none"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        id="strUserMobile"
                        name="strUserMobile"
                        class="field-input {{ $errors->has('strUserMobile') ? 'is-invalid' : '' }}"
                        value="{{ old('strUserMobile') }}"
                        placeholder="Enter your 10-digit mobile number"
                        maxlength="15"
                        autofocus
                        autocomplete="username"
                    >
                </div>
                @error('strUserMobile')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="field-group">
                <label class="field-label" for="password">Password</label>
                <div class="field-wrap">
                    <span class="field-icon">
                        <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="7" width="10" height="8" rx="1.5"/>
                            <path d="M5 7V5a3 3 0 016 0v2"/>
                        </svg>
                    </span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="field-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                    >
                </div>
                @error('password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="remember-row">
                <label class="remember-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-signin">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 8H3M13 8l-3-3m3 3l-3 3"/>
                    <path d="M6 4V3a1 1 0 011-1h5a1 1 0 011 1v10a1 1 0 01-1 1H7a1 1 0 01-1-1v-1"/>
                </svg>
                Sign in
            </button>

        </form>

        <div class="login-footer">
            Access is managed by your administrator
            <div class="role-chips">
                <span class="role-chip">StoreManager</span>
                <span class="role-chip">Measurement</span>
                <span class="role-chip">Production</span>
                <span class="role-chip">Dispatch</span>
                <span class="role-chip">Fitting</span>
                <span class="role-chip">Account</span>
            </div>
        </div>

    </div>
</div>

</body>
</html>
