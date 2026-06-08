<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HYDROFARM Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="hydro-auth-page min-h-screen text-black antialiased">
    <main class="hydro-auth-shell">
        <section id="one-tap-card" class="hydro-card hydro-one-tap hidden" aria-label="Login cepat">
            <div class="hydro-brand">
                <span class="hydro-brand-mark" aria-hidden="true">
                    <span></span>
                </span>
                <span>HYDROFARM</span>
            </div>

            <button id="one-tap-button" type="button" class="hydro-profile-button" aria-label="Masuk dengan profil terakhir">
                <span id="one-tap-avatar" class="hydro-avatar">P</span>
                <span id="one-tap-name" class="hydro-profile-name">Nama Pengguna</span>
                <span class="hydro-profile-hint">Ketuk Profil Untuk Masuk</span>
            </button>
        </section>

        <button id="switch-account-button" type="button" class="hydro-switch-account hidden">Masuk Dengan Akun Lain <span aria-hidden="true">-></span></button>

        <section id="login-card" class="hydro-card hydro-login-card" aria-label="Form login">
            <div class="hydro-brand hydro-brand-form">
                <span class="hydro-brand-mark" aria-hidden="true">
                    <span></span>
                </span>
                <span>HYDROFARM</span>
            </div>

            <form id="auth-form" class="hydro-form">
                <div class="hydro-field">
                    <label for="name">Nama</label>
                    <input id="name" name="name" type="text" autocomplete="username" placeholder="Masukan Nama Anda" required>
                </div>

                <div class="hydro-field">
                    <label for="password">Kata Sandi</label>
                    <div class="hydro-password-field">
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukan Kata Sandi" minlength="8" required>
                        <button id="toggle-password" type="button" aria-label="Tampilkan kata sandi">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <p id="auth-message" class="hydro-message hidden"></p>

                <button id="submit-button" type="submit" class="hydro-submit">Masuk</button>
            </form>
        </section>
    </main>
</body>
</html>
