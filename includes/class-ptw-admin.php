<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class PTW_Admin {
    public static function init() {
        add_action('admin_menu', [self::class, 'add_admin_menu']);
        add_action('admin_init', [self::class, 'register_settings']);
    }

    public static function add_admin_menu() {
        add_menu_page(
            'Prediksi',
            'Prediksi',
            'manage_options',
            'ptw-settings',
            [self::class, 'render_settings_page'],
            'dashicons-superhero',
            75
        );
    }

    public static function register_settings() {
        register_setting('ptw_settings_group', 'ptw_options', [
            'sanitize_callback' => [self::class, 'sanitize_options']
        ]);

        add_settings_section(
            'ptw_main_section',
            'Pengaturan Umum',
            [self::class, 'render_main_section_text'],
            'ptw-settings'
        );

        add_settings_field(
            'ptw_enable_feature',
            'Aktifkan Fitur Prediksi',
            [self::class, 'render_enable_feature_field'],
            'ptw-settings',
            'ptw_main_section'
        );

        add_settings_field(
            'ptw_selected_pasaran',
            'Pilih Pasaran',
            [self::class, 'render_pasaran_field'],
            'ptw-settings',
            'ptw_main_section'
        );

        add_settings_field(
            'ptw_post_type',
            'Pilih Tipe Postingan',
            [self::class, 'render_post_type_field'],
            'ptw-settings',
            'ptw_main_section'
        );
    }

    public static function sanitize_options($input) {
        $options = get_option('ptw_options', []);
        
        $options['enable_feature'] = isset($input['enable_feature']) ? 1 : 0;
        $options['selected_pasaran'] = isset($input['selected_pasaran']) && is_array($input['selected_pasaran']) 
            ? array_map('sanitize_text_field', $input['selected_pasaran']) 
            : [];
        $options['post_type'] = isset($input['post_type']) ? sanitize_text_field($input['post_type']) : 'post';

        return $options;
    }

    public static function render_main_section_text() {
        echo '<p>Atur preferensi utama untuk plugin Prediksi.</p>';
    }

    public static function render_enable_feature_field() {
        $options = get_option('ptw_options', []);
        $enabled = isset($options['enable_feature']) ? $options['enable_feature'] : 0;
        echo '<input type="checkbox" id="ptw_enable_feature" name="ptw_options[enable_feature]" value="1" ' . checked(1, $enabled, false) . ' />';
    }

    public static function render_pasaran_field() {
        $options = get_option('ptw_options', []);
        $selected_pasaran = isset($options['selected_pasaran']) ? (array) $options['selected_pasaran'] : [];
        $pasaran_list = ['HK', 'SGP', 'SDY', 'MACAU', 'JAPAN', 'TAIWAN', 'CAMBODIA', 'CHINA'];

        foreach ($pasaran_list as $pasaran) {
            echo '<label>
                <input type="checkbox" name="ptw_options[selected_pasaran][]" value="' . esc_attr($pasaran) . '" ' . (in_array($pasaran, $selected_pasaran) ? 'checked' : '') . '>
                ' . esc_html($pasaran) . '
            </label><br>';
        }
    }

    public static function render_post_type_field() {
        $options = get_option('ptw_options', []);
        $selected_type = isset($options['post_type']) ? $options['post_type'] : 'post';
        ?>
        <select name="ptw_options[post_type]">
            <option value="post" <?php selected($selected_type, 'post'); ?>>Post</option>
            <option value="page" <?php selected($selected_type, 'page'); ?>>Page</option>
        </select>
        <?php
    }

    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Pengaturan Prediksi</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('ptw_settings_group');
                do_settings_sections('ptw-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

// Inisialisasi class saat plugin dimuat
add_action('plugins_loaded', ['PTW_Admin', 'init']);
