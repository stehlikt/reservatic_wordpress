<?php
/*
Plugin Name: Reservatic Plugin
Description: Plugin pro integraci systému Reservatic.
Version: 1.0
Author: Railsformers
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include Composer autoload
require_once __DIR__ . '/vendor/autoload.php';

// Include helper functions
require_once __DIR__ . '/includes/api.php';

// Include admin settings
require_once __DIR__ . '/includes/admin-settings.php';

// Include form management
require_once __DIR__ . '/includes/form-management.php';

// Load plugin text domain for translations
function reservatic_load_textdomain() {
    load_plugin_textdomain('reservatic', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'reservatic_load_textdomain');

// Enqueue jQuery and custom script
function reservatic_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('reservatic', plugins_url('/js/reservatic.js', __FILE__), array('jquery'), null, true);

    wp_register_style('reservatic', plugins_url('/css/reservatic.css', __FILE__));
    wp_enqueue_style('reservatic');

    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), null, 'all');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), null, true);
    wp_enqueue_script('jqueryPdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js', array(), null, true);

    wp_localize_script('reservatic', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'translations' => array(
            'select_operation' => __('Vyberte úkon', 'reservatic'),
            'free' => __('Zdarma', 'reservatic'),
            'free_spots' => __('Počet volných míst', 'reservatic'),
            'number_of_people' => __('Počet objednaných osob', 'reservatic'),
            'reservation_error' => __('Chyba v rezervaci.', 'reservatic'),
            'error' => __('Chyba', 'reservatic'),
            'reservation_deleted' => __('Rezervace úspěšně vymazána', 'reservatic'),
            'first_name' => __('Jméno', 'reservatic'),
            'last_name' => __('Příjmení', 'reservatic'),
            'days' => array(
                __('neděle', 'reservatic'),
                __('pondělí', 'reservatic'),
                __('úterý', 'reservatic'),
                __('středa', 'reservatic'),
                __('čtvrtek', 'reservatic'),
                __('pátek', 'reservatic'),
                __('sobota', 'reservatic')
            ),
            'months' => array(
                __('ledna', 'reservatic'),
                __('února', 'reservatic'),
                __('března', 'reservatic'),
                __('dubna', 'reservatic'),
                __('května', 'reservatic'),
                __('června', 'reservatic'),
                __('července', 'reservatic'),
                __('srpna', 'reservatic'),
                __('září', 'reservatic'),
                __('října', 'reservatic'),
                __('listopadu', 'reservatic'),
                __('prosince', 'reservatic')
            )
        )
    ));
}
add_action('wp_enqueue_scripts', 'reservatic_enqueue_scripts');

require_once __DIR__ . '/includes/shortcode.php';

// Register dynamic shortcodes
function reservatic_register_dynamic_shortcodes() {
    $forms_data = get_option('reservatic_forms_data', array());
    foreach ($forms_data as $form_id => $form_data) {
        add_shortcode('reservatic_form_' . $form_id, function($atts) use ($form_data) {
            $atts['service_id'] = $form_data['service_id'];
            $atts['font_color'] = $form_data['font_color'];
            $atts['element_color'] = $form_data['element_color'];
            $atts['button_text_color'] = $form_data['button_text_color'];
            $atts['border_radius'] = $form_data['border_radius'];
            $atts['background_color'] = $form_data['background_color'];
            $atts['show_logo'] = $form_data['show_logo'];
            $atts['show_app_link'] = $form_data['show_app_link'];
            return reservatic_form_shortcode($atts);
        });
    }
}
add_action('init', 'reservatic_register_dynamic_shortcodes');

// Register admin menu
function reservatic_register_admin_menu() {
    add_menu_page(__('Reservatic Plugin', 'reservatic'), __('Reservatic Plugin', 'reservatic'), 'manage_options', 'reservatic-plugin', 'reservatic_options_page', 'dashicons-admin-generic');
    add_submenu_page('reservatic-plugin', __('Nastavení', 'reservatic'), __('Nastavení', 'reservatic'), 'manage_options', 'reservatic-plugin', 'reservatic_options_page');
    add_submenu_page('reservatic-plugin', __('Formuláře', 'reservatic'), __('Formuláře', 'reservatic'), 'manage_options', 'reservatic-forms', 'reservatic_forms_page');
    add_submenu_page(null, __('Editovat Formulář', 'reservatic'), __('Editovat Formulář', 'reservatic'), 'manage_options', 'reservatic-edit-form', 'reservatic_edit_form_page');
}
add_action('admin_menu', 'reservatic_register_admin_menu');
