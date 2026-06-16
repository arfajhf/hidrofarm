// Fungsi untuk Sinkronisasi Status (Jalan saat halaman dibuka)
async function syncPumpStatus() {
    try {
        const response = await fetch('https://siphantom.realtywire.web.id/api_datarelay.php');
        const result = await response.json();

        if (result.status === 'success') {
            const toggle = document.getElementById('pompa-toggle');
            const statusLabel = document.getElementById('status-label');
            const statusDot = document.querySelector('.status-dot');

            const isON = parseInt(result.data.status) === 1;
            toggle.checked = isON;
            statusLabel.textContent = isON ? 'NYALA' : 'MATI';
            statusDot.style.backgroundColor = isON ? '#2ecc71' : 'red';
        }
    } catch (error) { console.error("Gagal sinkronisasi:", error); }
}

window.togglePompaUI = async function(checkbox) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
    const status = checkbox.checked ? 'on' : 'off';
    console.log("Toggle dipencet:", checkbox.checked); // Ini udah jalan tadi

    try {
        const response = await fetch('/penyiraman/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status: status })
        });

        const data = await response.json();
        console.log("Respon dari Server:", data); // Liat di console setelah ini
    } catch (error) {
        console.error("Gagal nembak API:", error); // INI BAKAL KELUAR KALAU ADA ERROR
    }
};

document.addEventListener('DOMContentLoaded', syncPumpStatus);
