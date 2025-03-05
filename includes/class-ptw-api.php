<?php
if (!defined('ABSPATH')) {
    exit;
}

class PTW_API {
    public static function register_routes() {
        register_rest_route('ptw/v1', '/generate/', array(
            'methods' => 'POST',
            'callback' => [self::class, 'generate_prediction'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));
    }

    public static function generate_prediction($request) {
        // Ambil parameter pasaran dan tanggal dari request
        $pasaran = $request->get_param('pasaran');
        $date = $request->get_param('date');
    
        if (empty($pasaran)) {
            return [
                'status' => 'error',
                'message' => 'Pasaran harus dipilih'
            ];
        }
    
        // Generate tanggal untuk judul
        $date_obj = new DateTime($date);
        $hari = $date_obj->format('d');
    
        // Mapping nama bulan dalam bahasa Indonesia
        $bulan_indonesia = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
    
        $bulan_angka = $date_obj->format('n'); // Format 'n' untuk angka bulan tanpa leading zero
        $bulan = $bulan_indonesia[$bulan_angka]; // Konversi ke nama bulan dalam bahasa Indonesia
    
        $tahun = $date_obj->format('Y');
        $judul = "Prediksi $pasaran $hari $bulan $tahun"; // Contoh: Prediksi HK 25 Desember 2023
    
        // Generate BBFS (6 angka unik dari 0 sampai 9)
        $bbfs_digits = range(0, 9); // Buat array angka dari 0 sampai 9
        shuffle($bbfs_digits); // Acak urutan angka
        $bbfs_digits = array_slice($bbfs_digits, 0, 6); // Ambil 6 angka pertama
        $bbfs = implode('', $bbfs_digits); // Gabungkan menjadi string
    
        // Generate Angka Main (4 angka acak dari BBFS)
        $angka_main_digits = array_slice($bbfs_digits, 0, 4); // Ambil 4 angka pertama dari BBFS
        shuffle($angka_main_digits); // Acak urutan angka
        $angka_main = implode('', $angka_main_digits); // Gabungkan menjadi string
    
        // Generate CB (2 angka acak dari BBFS, dipisahkan oleh "/")
        $cb_digits = array_slice($bbfs_digits, 0, 2); // Ambil 2 angka pertama dari BBFS
        shuffle($cb_digits); // Acak urutan angka
        $cb = implode('/', $cb_digits); // Gabungkan angka dengan pemisah "/"
    
        // Generate CM (3 pasang angka acak dari BBFS)
        $cm_pairs = [];
        while (count($cm_pairs) < 3) {
            $pair = $bbfs_digits[array_rand($bbfs_digits)] . $bbfs_digits[array_rand($bbfs_digits)]; // Ambil 2 angka acak dari BBFS
            if (!in_array($pair, $cm_pairs)) { // Pastikan pasangan angka unik
                $cm_pairs[] = $pair;
            }
        }
        $cm = implode('/', $cm_pairs); // Gabungkan pasangan angka dengan pemisah "/"
    
        // Pilih shio secara acak
        $shio_list = ['Tikus', 'Kerbau', 'Macan', 'Kelinci', 'Naga', 'Ular', 'Kuda', 'Kambing', 'Monyet', 'Ayam', 'Anjing', 'Babi'];
        $shio = $shio_list[array_rand($shio_list)];
    
        // Generate angka 2D (8 pasang angka acak dari BBFS)
        $two_d_pairs = [];
        while (count($two_d_pairs) < 8) {
            $pair = $bbfs_digits[array_rand($bbfs_digits)] . $bbfs_digits[array_rand($bbfs_digits)]; // Ambil 2 angka acak dari BBFS
            if (!in_array($pair, $two_d_pairs)) { // Pastikan pasangan angka unik
                $two_d_pairs[] = $pair;
            }
        }
        $two_d = implode('*', $two_d_pairs); // Gabungkan pasangan angka dengan pemisah "*"
    
        // Generate HTML hanya dalam format tabel
        $prediction_html = '
        <div class="ptw-prediction">
                <h3>' . esc_html($judul) . '</h3>
            <table class="ptw-prediction-table">
                <tr><th>BBFS</th><td>' . esc_html($bbfs) . '</td></tr>
                <tr><th>Angka Main</th><td>' . esc_html($angka_main) . '</td></tr>
                <tr><th>CB</th><td>' . esc_html($cb) . '</td></tr>
                <tr><th>CM</th><td>' . esc_html($cm) . '</td></tr>
                <tr><th>Shio</th><td>' . esc_html($shio) . '</td></tr>
                <tr><th colspan="2">TOP 2D</th></tr>
                <tr><td colspan="2">' . esc_html($two_d) . '</td></tr>
            </table>
        </div>
        ';
    
        return [
            'status' => 'success',
            'message' => 'Prediksi berhasil dibuat',
            'prediction' => $prediction_html
        ];
    }
}