document.addEventListener('DOMContentLoaded', () => {
    // Cuma jalanin script ini kalau elemen beranda ada di halaman
    const suhuEl = document.getElementById('beranda-suhu');
    if (!suhuEl) return; 

    async function loadBerandaData() {
        try {
            // Nembak ke API dummy yang baru kita buat
            const response = await fetch('/api/beranda-data');
            const data = await response.json();

            // 1. Update Angka Sensor
            document.getElementById('beranda-suhu').innerHTML = `${data.suhu}<span>°C</span>`;
            document.getElementById('beranda-kelembaban').innerHTML = `${data.kelembaban}<span>%</span>`;

            // 2. Update Kartu Status (Kritis / Kurang Aman / Aman)
            const alertCard = document.getElementById('beranda-alert-card');
            const alertTitle = document.getElementById('beranda-alert-title');
            const alertSubtitle = document.getElementById('beranda-alert-subtitle');
            const alertBlocks = document.getElementById('beranda-alert-blocks');
            const alertIcon = document.getElementById('beranda-alert-icon');

            // Format blok array jadi string (cth: "Blok A, Blok B")
            const blokString = data.blok_terdampak.join(', ');

            // Logika ganti warna dan teks
            if (data.status === 'kritis') {
                alertCard.style.background = '#ff252b'; // Merah
                alertIcon.src = '/assets/icons/darurat.svg'; // Pastiin path icon lo bener
                alertTitle.textContent = 'Kondisi Kritis';
                alertSubtitle.textContent = 'Siram Blok Berikut';
                alertBlocks.textContent = blokString;
            } 
            else if (data.status === 'kurang_aman') {
                alertCard.style.background = '#fff475'; // Kuning
                alertCard.style.color = '#5f090f'; // Ubah teks jadi gelap biar kebaca
                alertIcon.src = '/assets/icons/penyiraman/warning.svg';
                alertTitle.textContent = 'Kondisi Kurang Aman';
                alertSubtitle.textContent = 'Siap-siap Siram Blok Berikut';
                alertBlocks.textContent = blokString;
            } 
            else if (data.status === 'aman') {
                alertCard.style.background = '#39a940'; // Hijau
                alertCard.style.color = '#ffffff';
                alertIcon.src = '/assets/icons/penyiraman/success.svg';
                alertTitle.textContent = 'Kondisi Aman';
                alertSubtitle.textContent = 'Semua Blok Terkendali';
                alertBlocks.textContent = blokString || 'Tidak ada tindakan diperlukan';
            }

        } catch (error) {
            console.error("Gagal narik data Beranda:", error);
        }
    }

    // Eksekusi fungsinya
    loadBerandaData();
    
    // (Opsional) Bikin dia update otomatis tiap 5 detik tanpa refresh halaman!
    // setInterval(loadBerandaData, 5000); 
});