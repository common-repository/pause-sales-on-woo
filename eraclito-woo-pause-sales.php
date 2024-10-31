<?php
/**
 * Plugin Name: Pause Sales for Woocommerce
 * Description: Plugin per mettere in pausa le vendite su WooCommerce e visualizzare un messaggio personalizzato.
 * Version: 1.2
 * Author: Eraclito
 * Author:       Eraclito - Alessio Rosi 
 * Author URI:   https://www.eraclito.it
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  eraclito-woo-pause-sales
 * Domain Path:  /languages
 */

// Evita l'accesso diretto
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Aggiungi il menu di amministrazione
add_action('admin_menu', 'eraclito_wc_pause_add_admin_menu');
function eraclito_wc_pause_add_admin_menu() {
    add_menu_page(
        'WooCommerce Pause Sales', 
        'WC Pause Sales', 
        'manage_options', 
        'eraclito-wc-pause-sales', 
        'eraclito_wc_pause_settings_page',
        'dashicons-chart-pie'
    );
}

// Crea la pagina delle impostazioni del plugin
function eraclito_wc_pause_settings_page() {
    ?>
    <div class="wrap">
        <h1>WooCommerce Pause Sales</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('eraclito_wc_pause_settings_group');
            do_settings_sections('eraclito-wc-pause-sales');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Inizializza le impostazioni del plugin
add_action('admin_init', 'eraclito_wc_pause_settings_init');
function eraclito_wc_pause_settings_init() {
    register_setting('eraclito_wc_pause_settings_group', 'eraclito_wc_pause_enabled');
    register_setting('eraclito_wc_pause_settings_group', 'eraclito_wc_pause_message');
    register_setting('eraclito_wc_pause_settings_group', 'eraclito_wc_pause_message_position');
    register_setting('eraclito_wc_pause_settings_group', 'eraclito_wc_pause_message_bg_color');
    register_setting('eraclito_wc_pause_settings_group', 'eraclito_wc_pause_message_text_color');

    add_settings_section(
        'eraclito_wc_pause_settings_section', 
        'Impostazioni di Pausa Vendite', 
        'eraclito_wc_pause_settings_section_callback', 
        'eraclito-wc-pause-sales'
    );

    add_settings_field(
        'eraclito_wc_pause_enabled', 
        'Metti in Pausa le Vendite', 
        'eraclito_wc_pause_enabled_render', 
        'eraclito-wc-pause-sales', 
        'eraclito_wc_pause_settings_section'
    );

    add_settings_field(
        'eraclito_wc_pause_message', 
        'Messaggio di Pausa', 
        'eraclito_wc_pause_message_render', 
        'eraclito-wc-pause-sales', 
        'eraclito_wc_pause_settings_section'
    );

    add_settings_field(
        'eraclito_wc_pause_message_position', 
        'Posizione del Messaggio', 
        'eraclito_wc_pause_message_position_render', 
        'eraclito-wc-pause-sales', 
        'eraclito_wc_pause_settings_section'
    );

    add_settings_field(
        'eraclito_wc_pause_message_bg_color', 
        'Colore Sfondo del Messaggio', 
        'eraclito_wc_pause_message_bg_color_render', 
        'eraclito-wc-pause-sales', 
        'eraclito_wc_pause_settings_section'
    );

    add_settings_field(
        'eraclito_wc_pause_message_text_color', 
        'Colore Testo del Messaggio', 
        'eraclito_wc_pause_message_text_color_render', 
        'eraclito-wc-pause-sales', 
        'eraclito_wc_pause_settings_section'
    );
}

function eraclito_wc_pause_settings_section_callback() {
    echo 'Configura le impostazioni per mettere in pausa le vendite su WooCommerce.';
}

function eraclito_wc_pause_enabled_render() {
    $value = get_option('eraclito_wc_pause_enabled');
    ?>
    <input type="checkbox" name="eraclito_wc_pause_enabled" value="1" <?php checked(1, $value, true); ?> />
    <?php
}

function eraclito_wc_pause_message_render() {
    $value = get_option('eraclito_wc_pause_message');
    ?>
    <textarea name="eraclito_wc_pause_message" rows="5" cols="50"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function eraclito_wc_pause_message_position_render() {
    $value = get_option('eraclito_wc_pause_message_position', 'top');
    ?>
    <select name="eraclito_wc_pause_message_position">
        <option value="top" <?php selected($value, 'top'); ?>>Top</option>
        <option value="bottom" <?php selected($value, 'bottom'); ?>>Bottom</option>
    </select>
    <?php
}

function eraclito_wc_pause_message_bg_color_render() {
    $value = get_option('eraclito_wc_pause_message_bg_color', '#ff0000');
    ?>
    <input type="text" name="eraclito_wc_pause_message_bg_color" value="<?php echo esc_attr($value); ?>" class="my-color-field" />
    <?php
}

function eraclito_wc_pause_message_text_color_render() {
    $value = get_option('eraclito_wc_pause_message_text_color', '#ffffff');
    ?>
    <input type="text" name="eraclito_wc_pause_message_text_color" value="<?php echo esc_attr($value); ?>" class="my-color-field" />
    <?php
}


// Applica il filtro per mettere in pausa le vendite
add_filter('woocommerce_is_purchasable', 'eraclito_wc_pause_sales');
function eraclito_wc_pause_sales($is_purchasable) {
    if (get_option('eraclito_wc_pause_enabled')) {
        return false;
    }
    return $is_purchasable;
}

// Mostra il messaggio di pausa su tutte le pagine
add_action('wp_footer', 'eraclito_wc_pause_message_display');
function eraclito_wc_pause_message_display() {
    if (get_option('eraclito_wc_pause_enabled')) {
        $message = get_option('eraclito_wc_pause_message');
        $bg_color = get_option('eraclito_wc_pause_message_bg_color', '#ff0000');
        $text_color = get_option('eraclito_wc_pause_message_text_color', '#ffffff');
        $position = get_option('eraclito_wc_pause_message_position', 'top');
        
        if ($message) {
            echo '<div class="wc-pause-message" style="background: ' . esc_attr($bg_color) . '; color: ' . esc_attr($text_color) . '; padding: 10px; text-align: center; position: fixed; width: 100%; z-index: 9999; ' . ($position == 'top' ? 'top: 0;' : 'bottom: 0;') . '">' . esc_html($message) . '</div>';
        }
    }
}

// Aggiungi il supporto per il selettore di colore
add_action('admin_enqueue_scripts', 'eraclito_wc_pause_enqueue_color_picker');
function eraclito_wc_pause_enqueue_color_picker($hook_suffix) {
    // Assicurati di caricare solo nella pagina delle impostazioni del plugin
    if ('toplevel_page_eraclito-wc-pause-sales' !== $hook_suffix) {
        return;
    }

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('eraclito-wc-pause-color-picker', plugins_url('js/color-picker.js', __FILE__), array('wp-color-picker'), false, true);
}
