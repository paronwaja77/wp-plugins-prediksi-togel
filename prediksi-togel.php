<?php
/*
Plugin Name: Prediksi Togel
Description: Plugin untuk menghasilkan dan menampilkan prediksi.
Version: 1.0
Author: Paron Waja
Author URI: http://datahklotto.info/
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

// Definisikan konstanta path plugin
define('PTW_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PTW_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include file-file fungsi dengan validasi
$includes = [
    'includes/class-ptw-database.php',
    'includes/class-ptw-api.php',
//    'includes/class-ptw-shortcode.php',
    'includes/class-ptw-admin.php',
    'includes/class-ptw-about.php'
];
foreach ($includes as $file) {
    $filepath = PTW_PLUGIN_DIR . $file;
    if (file_exists($filepath) && is_readable($filepath)) {
        require_once $filepath;
    }
}

// Aktifkan tabel database saat plugin diaktifkan
// register_activation_hook(__FILE__, ['PTW_Database', 'create_table']);
// Hapus tabel database saat plugin di-uninstall
// register_uninstall_hook(__FILE__, ['PTW_Database', 'delete_table']);

// Inisialisasi API, Shortcode, dan Admin
add_action('rest_api_init', ['PTW_API', 'register_routes']);
add_shortcode('prediksi_togel', ['PTW_Shortcode', 'display_prediction']);
add_action('admin_menu', ['PTW_Admin', 'add_admin_menu']);
add_action('admin_menu', ['PTW_About', 'add_about_submenu']);
add_action('admin_init', ['PTW_Admin', 'register_settings']);

// Enqueue CSS dan JS untuk frontend
add_action('wp_enqueue_scripts', function () {
    $css_file = PTW_PLUGIN_DIR . 'assets/css/style.css';
    if (file_exists($css_file) && is_readable($css_file)) {
        wp_enqueue_style('ptw-style', PTW_PLUGIN_URL . 'assets/css/style.css', [], filemtime($css_file));
    }
    
    $js_file = PTW_PLUGIN_DIR . 'assets/js/script.js';
    if (file_exists($js_file) && is_readable($js_file)) {
        wp_enqueue_script('ptw-script', PTW_PLUGIN_URL . 'assets/js/script.js', ['jquery'], filemtime($js_file), true);
    }
});

// Enqueue CSS dan JS untuk admin area
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        $admin_css = PTW_PLUGIN_DIR . 'assets/css/admin-style.css';
        if (file_exists($admin_css) && is_readable($admin_css)) {
            wp_enqueue_style('ptw-admin-style', PTW_PLUGIN_URL . 'assets/css/admin-style.css', [], filemtime($admin_css));
        }

        $editor_js = PTW_PLUGIN_DIR . 'assets/js/editor.js';
        if (file_exists($editor_js) && is_readable($editor_js)) {
            wp_enqueue_script('ptw-editor-script', PTW_PLUGIN_URL . 'assets/js/editor.js', ['jquery'], filemtime($editor_js), true);
            $options = get_option('ptw_options', []);
            wp_localize_script('ptw-editor-script', 'wpApiSettings', [
                'nonce' => wp_create_nonce('wp_rest'),
                'apiUrl' => rest_url('ptw/v1/generate/'),
                'selectedPasaran' => $options['selected_pasaran'] ?? []
            ]);
        }
    }
});

// Tambahkan metabox di bawah editor jika fitur diaktifkan
add_action('add_meta_boxes', function () {
    $options = get_option('ptw_options', []);
    if (!empty($options['enable_feature'])) {
        $display_on = $options['display_on'] ?? ['post'];
        add_meta_box('ptw_generate_prediction', 'Generate Prediksi', 'ptw_generate_prediction_metabox', $display_on, 'normal', 'high');
    }
});

function ptw_generate_prediction_metabox($post) {
    $options = get_option('ptw_options', []);
    $selected_pasaran = $options['selected_pasaran'] ?? ['HK'];
    $default_date = date('Y-m-d');
    wp_nonce_field('ptw_generate_nonce', 'ptw_generate_nonce_field');
    ?>
    <div id="ptw-generate-prediction">
        <select id="ptw-pasaran-select">
            <option value="" disabled selected>Pilih Pasaran</option>
            <?php foreach ($selected_pasaran as $pasaran) {
                echo "<option value='$pasaran'>$pasaran</option>";
            } ?>
        </select>
        <input type="date" id="ptw-date-input" value="<?php echo esc_attr($default_date); ?>" placeholder="Pilih Tanggal">
        <button id="ptw-generate-button" class="button button-primary">Generate</button>
    </div>
    <div id="ptw-prediction-container">
    <div id="ptw-prediction-result">Prediksi akan muncul di sini setelah di-generate.</div>
        <div id="ptw-prediction-actions">
            <button class="ptw-copy-button button">Copy</button>
            <button class="ptw-insert-text-button button">Insert Teks</button>
            <button class="ptw-insert-html-button button">Insert Tabel HTML</button>
    </div>
    <span style="color:rgb(64, 164, 55);display: flex;justify-content: right;"><strong>Note:</strong> Untuk mereset prediksi, klik tombol "Generate" lagi.</span>
            </div>

    <?php
}
