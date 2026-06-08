document.addEventListener('DOMContentLoaded', () => {
    const titleEl = document.getElementById('detail-title');
    const gridEl = document.getElementById('detail-grid');

    if (!titleEl || !gridEl) return;

    async function loadDetailData() {
        try {
            // 1. TANGKAP NAMA HARI DARI URL BROWSER
            const urlParams = new URLSearchParams(window.location.search);
            // Kalau di URL gak ada parameter '?hari=', otomatis nampilin 'Hari Ini'
            const hariMinta = urlParams.get('hari') || 'Hari Ini'; 

            // 2. UBAH JUDUL HALAMAN SECARA LANGSUNG
            titleEl.textContent = `Catatan Penyiraman ${hariMinta}`;
            
            // Kasih loading di kotaknya
            gridEl.innerHTML = '<p style="grid-column: 1 / -1; text-align: center;">Memuat detail...</p>';

            // 3. Tembak API dummy bawa nama harinya
            const response = await fetch(`/api/riwayat-detail-data?hari=${encodeURIComponent(hariMinta)}`);
            const data = await response.json();

            // Kosongin grid loading
            gridEl.innerHTML = '';

            // 4. Cetak kartunya
            data.catatan.forEach(item => {
                const cardHtml = `
                    <article class="history-shell">
                        <div class="history-card">
                            <h2>${item.waktu}</h2>
                            <div class="history-card-body">
                                <p>${item.status}</p>
                                <strong>${item.blok}</strong>
                                <img src="/assets/icons/penyiraman/success.svg" alt="Selesai">
                            </div>
                        </div>
                    </article>
                `;
                gridEl.insertAdjacentHTML('beforeend', cardHtml);
            });

        } catch (error) {
            console.error("Gagal narik data Detail Riwayat:", error);
            gridEl.innerHTML = '<p style="grid-column: 1 / -1; color: red;">Gagal memuat data.</p>';
        }
    }

    loadDetailData();
});