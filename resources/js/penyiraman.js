document.addEventListener('DOMContentLoaded', () => {
    // Cek apakah kita beneran lagi di halaman penyiraman
    const kritisEl = document.getElementById('penyiraman-kritis');
    if (!kritisEl) return;

    async function loadPenyiramanData() {
        try {
            const response = await fetch('/api/penyiraman-data');
            const data = await response.json();

            // Ambil elemen HTML-nya
            const kurangAmanEl = document.getElementById('penyiraman-kurang-aman');
            const amanEl = document.getElementById('penyiraman-aman');

            // Format data array jadi teks (contoh: "Blok A, Blok B")
            // Kalau array-nya kosong, kita kasih teks alternatif
            kritisEl.textContent = data.kritis.length > 0 ? data.kritis.join(', ') : 'Tidak ada blok kritis';
            kurangAmanEl.textContent = data.kurang_aman.length > 0 ? data.kurang_aman.join(', ') : 'Semua blok aman';
            amanEl.textContent = data.aman.length > 0 ? data.aman.join(', ') : '-';

        } catch (error) {
            console.error("Gagal narik data Penyiraman:", error);
            kritisEl.textContent = 'Gagal memuat data';
        }
    }

    // Panggil fungsinya
    loadPenyiramanData();
});