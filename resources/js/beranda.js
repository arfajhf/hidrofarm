document.addEventListener('DOMContentLoaded', () => {
    const alertCard = document.getElementById('beranda-alert-card');
    if (!alertCard) return;

    async function updateSystem() {
        try {
            const response = await fetch('/api/beranda-data');
            const data = await response.json();

            // Update Angka
            document.getElementById('beranda-suhu').innerHTML = `${data.suhu}<span>°C</span>`;
            document.getElementById('beranda-kelembaban').innerHTML = `${data.kelembaban}<span>%</span>`;

            // Update UI Berdasarkan Status & Mode
            const statusText = data.status === 'kritis' ? 'Tanah Kering (Pompa Aktif)' :
                               data.status === 'kurang_aman' ? 'Tanah Terlalu Basah' : 'Kondisi Aman';

            document.getElementById('beranda-alert-title').textContent = `${statusText} (${data.mode.toUpperCase()})`;

            // Tambahkan tombol reset jika manual
            if(data.mode === 'manual') {
                document.getElementById('beranda-alert-subtitle').innerHTML =
                    `Sistem Manual. <button onclick="resetToAuto()" style="background:white; border:none; padding:5px; cursor:pointer;">Reset ke Auto</button>`;
            } else {
                document.getElementById('beranda-alert-subtitle').textContent = 'Mode Otomatis Aktif';
            }

            alertCard.style.background = data.status === 'kritis' ? '#ff252b' : '#39a940';
        } catch (error) { console.error(error); }
    }

    // Fungsi Global buat Reset
    window.resetToAuto = async () => {
        await fetch('https://siphantom.realtywire.web.id/api_reset_mode.php');
        alert("Sistem kembali ke Otomatis!");
        updateSystem();
    };

    updateSystem();
    setInterval(updateSystem, 10000);
});
