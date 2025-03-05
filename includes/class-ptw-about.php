<?php
if (!defined('ABSPATH')) {
    exit; // Mencegah akses langsung
}

class PTW_About {
    public static function add_about_submenu() {
        add_submenu_page(
            'ptw-settings', // Pastikan ini sesuai dengan menu utama plugin
            'Tentang Plugin',
            'Tentang',
            'manage_options',
            'ptw-about',
            [self::class, 'render_about_page']
        );
    }

    public static function render_about_page() {
        ?>
<div class="wrap" style="display: flex; gap: 20px; align-items: flex-start;">
    <!-- Konten Utama -->
    <div style="flex: 3;">
        <h1 style="border-bottom: 2px solid #0073aa; padding-bottom: 10px;">Tentang Plugin Prediksi Togel</h1>
        <div style="margin-top: 10px;background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <p><strong>Plugin Prediksi Togel</strong> adalah alat yang dirancang untuk menghasilkan dan menampilkan prediksi togel secara otomatis di situs WordPress Anda.</p>
            
            <h2 style="color: #0073aa;">Fitur Utama:</h2>
            <ul style="list-style: disc; padding-left: 20px;">
                <li>Prediksi otomatis berdasarkan algoritma yang telah disiapkan.</li>
                <li>Mendukung berbagai pasaran togel seperti HK, SGP, SDY, dan lainnya.</li>
                <li>Prediksi dapat dihasilkan melalui editor sebelum dimasukkan ke dalam postingan.</li>
            </ul>

            <h2 style="color: #0073aa;">Cara Penggunaan:</h2>
            <ol style="list-style: decimal; padding-left: 20px;">
                <li>Akses halaman prediksi melalui menu yang tersedia di dashboard.</li>
                <li>Buka editor WordPress dan klik tombol "Generate Prediksi".</li>
                <li>Pilih pasaran dan tanggal prediksi yang diinginkan.</li>
                <li>Tekan tombol "Generate" untuk mendapatkan prediksi.</li>
                <li>Gunakan opsi "Insert Teks" atau "Insert Tabel HTML" untuk menambahkan hasil prediksi ke dalam postingan.</li>
            </ol>

            <p>Untuk dokumentasi lengkap dan bantuan, silakan kunjungi <a href="https://example.com" target="_blank" style="color: #0073aa; text-decoration: none;">dokumentasi resmi</a>.</p>
        </div>
    </div>
    
    <!-- Sidebar Kanan -->
    <aside style="margin-top: 60px;flex: 1; background: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); align-self: start;">
        <h2 style="color: #0073aa;">Informasi Plugin</h2>
        <p><strong>Versi:</strong> 1.0</p>
        <p><strong>Author:</strong> Nama Anda</p>
        <p><strong>Website:</strong> <a href="https://example.com" target="_blank">example.com</a></p>
        <p><strong>Dukungan & Donasi:</strong></p>
        <a href="https://www.paypal.com/donate?hosted_button_id=EXAMPLE" target="_blank" style="display: block; text-align: center; background: #0073aa; color: #fff; padding: 10px; border-radius: 5px; text-decoration: none;">Donasi via PayPal</a>
    </aside>
</div>

        <?php
    }
}

