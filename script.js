/*
 * script.js
 * File ini berisi semua JavaScript untuk fungsionalitas client-side.
 * Fokus utama adalah fitur live-search (suggestion).
 * Kode ini ditulis dalam vanilla JavaScript (murni) dan
 * direkomendasikan untuk divalidasi menggunakan JSHint.
 */

// Menunggu hingga seluruh konten halaman dimuat sebelum menjalankan script
document.addEventListener('DOMContentLoaded', function() {

    const searchBox = document.getElementById('search-box');
    const suggestionsContainer = document.getElementById('search-suggestions');

    // Hanya jalankan jika elemen pencarian ada di halaman
    if (searchBox && suggestionsContainer) {
        
        // Tambahkan event listener untuk setiap ketikan di kotak pencarian
        searchBox.addEventListener('keyup', function() {
            const query = searchBox.value;

            // Hanya kirim request jika query tidak kosong
            if (query.length > 1) {
                // Membuat objek XMLHttpRequest untuk berkomunikasi dengan server
                const xhr = new XMLHttpRequest();

                // Konfigurasi request: metode POST, URL ke script PHP, dan asynchronous
                xhr.open('POST', 'live_search.php', true);
                
                // Set header untuk request POST
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                // Fungsi yang akan dijalankan ketika status request berubah
                xhr.onreadystatechange = function() {
                    // Cek jika request selesai (readyState 4) dan berhasil (status 200)
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Masukkan response dari server (hasil pencarian) ke dalam container
                        suggestionsContainer.innerHTML = xhr.responseText;
                        suggestionsContainer.style.display = 'block';
                    }
                };

                // Kirim request dengan data query
                xhr.send('query=' + encodeURIComponent(query));
            } else {
                // Sembunyikan container jika kotak pencarian kosong
                suggestionsContainer.innerHTML = '';
                suggestionsContainer.style.display = 'none';
            }
        });

        // Menutup suggestions jika klik di luar area pencarian
        document.addEventListener('click', function(event) {
            if (!searchBox.contains(event.target)) {
                 suggestionsContainer.style.display = 'none';
            }
        });
    }

    // Fungsionalitas untuk konfirmasi penghapusan
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            const confirmation = confirm('Apakah Anda yakin ingin menghapus data ini?');
            if (!confirmation) {
                event.preventDefault(); // Batalkan aksi default jika pengguna menekan "Cancel"
            }
        });
    });

});
