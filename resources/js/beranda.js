document.addEventListener('DOMContentLoaded', () => {
    const alertCard = document.getElementById('beranda-alert-card');
    if (!alertCard) return;

    async function updateSystem() {
        try {
            const response = await fetch('/api/beranda-data');
            const data = await response.json();
            const subtitleEl = document.getElementById('beranda-alert-subtitle');

             if (!subtitleEl) return; // Mencegah error kalau elemennya belum ke-load

            // Update Angka
            document.getElementById('beranda-suhu').innerHTML = `${data.suhu}<span>°C</span>`;
            document.getElementById('beranda-kelembaban').innerHTML = `${data.kelembaban}<span>%</span>`;

            // Update UI Berdasarkan Status & Mode
            const statusText = data.status === 'kritis' ? 'Tanah Kering (Pompa Aktif)' :
                               data.status === 'kurang_aman' ? 'Tanah Terlalu Basah' : 'Kondisi Aman';

            document.getElementById('beranda-alert-title').textContent = `${statusText} (${data.mode.toUpperCase()})`;

            // Tambahkan tombol reset jika manual
            if(data.mode === 'manual') {
                subtitleEl.innerHTML = `Sistem Manual. <button id="btn-reset" onclick="resetToAuto()" style="background:#fff; border-radius: 10px; color:#ff252b; border:none; padding:5px; cursor:pointer;">Reset ke Auto</button>`;
            }else if(data.mode === 'auto') {
                subtitleEl.innerHTML = `Sistem Otomatis. <button id="btn-reset" onclick="setKeManual()" style="background:#fff; border-radius: 10px; color:#ff252b; border:none; padding:5px; cursor:pointer;">Reset ke Manual</button>`;
            }else {
                subtitleEl.textContent = 'Mode Otomatis Aktif';
            }

            alertCard.style.background = data.status === 'kritis' ? '#ff252b' : '#39a940';
        } catch (error) { console.error(error); }
    }

    // Di bagian dalam `togglePompa` lo, ganti jadi ini:
    async function togglePompa(status) {
        // 1. Tampilan UI langsung berubah (Biar gak kelihatan delay)
        // await fetch('https://siphantom.realtywire.web.id/api_restar_mode.php');

        // 2. Baru panggil API buat nyalain/matiin POMPA
        const res = await fetch('https://siphantom.realtywire.web.id/api_pump.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-API-KEY': 'token-rahasia-hydrofarm' },
            body: JSON.stringify({ status: status })
        });

        if (res.ok) {
            console.log("Mode terkunci ke Manual & Pompa diupdate.");
            updateSystem(); // Refresh UI
        }
    }

    // Fungsi Global buat Reset
    // window.resetToAuto = async () => {
    //     await fetch('https://siphantom.realtywire.web.id/api_reset_mode.php');
    //     alert("Sistem kembali ke Otomatis!");
    //     updateSystem();
    // };
    window.resetToAuto = async () => {
        // 1. UBAH UI INSTAN (Gak perlu nunggu server)
        const subtitleEl = document.getElementById('beranda-alert-subtitle');
        subtitleEl.textContent = "Sedang Mereset...";

        try {
            // 2. Fetch ke server
            await fetch('https://siphantom.realtywire.web.id/api_reset_mode.php?t=' + Date.now());

            // 3. Paksa refresh data biar langsung berubah jadi AUTO di UI
            updateSystem();
        } catch (e) {
            alert("Gagal reset!");
            subtitleEl.textContent = "Sistem Manual (Reset Gagal)";
        }
    };

    window.setKeManual = async function() {
        // 1. UBAH UI INSTAN (Gak perlu nunggu server)
        const subtitleEl = document.getElementById('beranda-alert-subtitle');
        subtitleEl.textContent = "Sedang Mengunci ke Manual...";

        try {
            // 2. Fetch ke server
            await fetch('https://siphantom.realtywire.web.id/api_restar_mode.php?t=' + Date.now());

            // 3. Paksa refresh data biar langsung berubah jadi MANUAL di UI
            updateSystem();
        } catch (e) {
            alert("Gagal mengunci ke Manual!");
            subtitleEl.textContent = "Sistem Otomatis (Gagal Mengunci)";
        }
    }

    updateSystem();
    setInterval(updateSystem, 10000);
});
