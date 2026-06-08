<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HYDROFARM Detail Riwayat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="hydro-dashboard-page min-h-screen text-black antialiased">
    <header class="home-topbar">
        <a href="/dashboard" class="hydro-brand home-brand" aria-label="HYDROFARM">
            <span class="hydro-brand-mark" aria-hidden="true"><span></span></span>
            <span>HYDROFARM</span>
        </a>

        <nav class="home-nav" aria-label="Navigasi utama">
            <a href="/dashboard">Beranda</a>
            <a href="/penyiraman">Penyiraman</a>
            <a href="/riwayat" class="is-active">Riwayat</a>
        </nav>

        <div class="home-user-menu">
            <button id="dashboard-menu-button" type="button" class="home-user-button" aria-expanded="false"
                aria-controls="dashboard-dropdown">
                <span class="home-user-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4" fill="none" stroke="currentColor"
                            stroke-width="2" />
                        <path d="M4 21c1.6-4.3 4.2-6.5 8-6.5s6.4 2.2 8 6.5" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" />
                    </svg>
                </span>
                <span id="dashboard-menu-name">Admin</span>
                <svg class="home-chevron" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="m6 9 6 6 6-6" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>

            <div id="dashboard-dropdown" class="home-dropdown hidden">
                <p id="dashboard-dropdown-name">Admin</p>
                <a href="/profile" class="home-dropdown-item">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="8" r="4" fill="none" stroke="currentColor"
                            stroke-width="2" />
                        <path d="M4 21c1.6-4.3 4.2-6.5 8-6.5s6.4 2.2 8 6.5" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Profile
                </a>
                <button id="dashboard-logout-button" type="button" class="home-dropdown-item">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M10 6H5v12h5" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M13 8l4 4-4 4M17 12H9" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Logout
                </button>
            </div>
        </div>
    </header>

    <main class="history-dashboard">
        <section class="history-heading">
            <h1 id="detail-title">Catatan Penyiraman ...</h1>
            <a href="/riwayat" class="back-button">
                &larr; Kembali ke Riwayat
            </a>
        </section>

        <div class="history-grid" id="detail-grid">
        </div>
    </main>

    <nav class="home-bottom-nav" aria-label="Navigasi mobile">
        <a href="/dashboard" aria-label="Beranda">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 11.5 12 4l9 7.5" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round" />
                <path d="M6.5 10.5V20h11v-9.5" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" />
            </svg>
        </a>
        <a href="/penyiraman" aria-label="Penyiraman">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 3s6 6.4 6 11a6 6 0 0 1-12 0c0-4.6 6-11 6-11Z" fill="currentColor" />
            </svg>
        </a>
        <a href="/riwayat" class="is-active" aria-label="Riwayat">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M4 12a8 8 0 1 0 2.4-5.7M4 5v5h5" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round" />
                <path d="M12 8v5l3 2" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </a>
    </nav>

    <footer class="home-footer">Copyright &copy; 2026 Hydrofarm All Rights Reserved.</footer>

    {{-- scripts js --}}
    @vite('resources/js/detail-riwayat.js')
</body>

</html>
