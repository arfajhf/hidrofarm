document.addEventListener('DOMContentLoaded', () => {
    const filterEl = document.getElementById('filter-riwayat');
    const gridEl = document.getElementById('riwayat-grid');

    // Kalau bukan di halaman riwayat, stop scriptnya
    if (!filterEl || !gridEl) return;

    // Fungsi buat narik dan nyetak data
    async function loadRiwayatData(jumlahHari) {
        try {
            // Kasih tulisan loading sementara
            gridEl.innerHTML = '<p style="grid-column: 1 / -1; text-align: center;">Memuat data riwayat...</p>';

            // Nembak API bawa parameter filter (contoh: ?filter=7)
            const response = await fetch(`/api/riwayat-data?filter=${jumlahHari}`);
            const data = await response.json();

            // Kosongin grid sebelum dicetak ulang
            gridEl.innerHTML = '';

            // Looping data dan cetak HTML kartunya
            data.forEach(item => {
                const cardHtml = `
                    <article class="history-shell">
                        <div class="history-card">
                            <h2>${item.hari}</h2>
                            <div class="history-card-body">
                                <p>${item.status}</p>
                                <strong>${item.blok}</strong>
                                <img src="/assets/icons/riwayat/check.svg" alt="Selesai">
                            </div>
                            <a href="/detail-riwayat?hari=${encodeURIComponent(item.hari)}">Lihat Detail &rarr;</a>
                        </div>
                    </article>
                `;
                // Tempelin kartunya ke dalem grid
                gridEl.insertAdjacentHTML('beforeend', cardHtml);
            });

        } catch (error) {
            console.error("Gagal narik data Riwayat:", error);
            gridEl.innerHTML = '<p style="grid-column: 1 / -1; color: red;">Gagal memuat data.</p>';
        }
    }

    // 1. Eksekusi pertama kali pas halaman dibuka (ngambil value default dari select)
    loadRiwayatData(filterEl.value);

    // 2. Event Listener: Kalau filter diganti, jalankan ulang fungsinya!
    filterEl.addEventListener('change', (event) => {
        const filterValue = event.target.value;
        loadRiwayatData(filterValue);
    });
});