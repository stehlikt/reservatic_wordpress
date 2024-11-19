<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function reservatic_register_settings() {
    add_option('reservatic_res_url', '');
    add_option('reservatic_api_token', '');
    add_option('reservatic_certificate', '');
    add_option('reservatic_certificate_password', '');
    register_setting('reservatic_options_group', 'reservatic_res_url');
    register_setting('reservatic_options_group', 'reservatic_api_token');
    register_setting('reservatic_options_group', 'reservatic_certificate');
    register_setting('reservatic_options_group', 'reservatic_certificate_password');
}
add_action('admin_init', 'reservatic_register_settings');

function reservatic_options_page() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_admin_referer('reservatic_options_update', 'reservatic_options_nonce')) {
        if (isset($_POST['reservatic_res_url'])) {
            update_option('reservatic_res_url', sanitize_text_field($_POST['reservatic_res_url']));
        }
        if (isset($_POST['reservatic_api_token'])) {
            update_option('reservatic_api_token', sanitize_text_field($_POST['reservatic_api_token']));
        }
        if (isset($_POST['reservatic_certificate_password'])) {
            update_option('reservatic_certificate_password', sanitize_text_field($_POST['reservatic_certificate_password']));
        }
        if (isset($_FILES['reservatic_certificate'])) {
            $uploaded_file = $_FILES['reservatic_certificate'];
            if ($uploaded_file['error'] == UPLOAD_ERR_OK) {
                $upload_dir = wp_upload_dir();
                $upload_path = $upload_dir['basedir'] . '/reservatic/';
                if (!file_exists($upload_path)) {
                    mkdir($upload_path, 0755, true);
                }
                $file_path = $upload_path . basename($uploaded_file['name']);
                if (move_uploaded_file($uploaded_file['tmp_name'], $file_path)) {
                    update_option('reservatic_certificate', $file_path);
                }
            }
        }
    }

    $certificate_path = get_option('reservatic_certificate');
    ?>
    <div>
        <h2><?php _e('Reservatic Plugin', 'r'); ?></h2>
        <form method="post" action="" enctype="multipart/form-data">
            <?php settings_fields('reservatic_options_group'); ?>
            <?php wp_nonce_field('reservatic_options_update', 'reservatic_options_nonce'); ?>
            <table>
                <tr valign="top">
                    <th scope="row"><label for="reservatic_res_url"><?php _e('Res URL', 'r'); ?></label></th>
                    <td><input type="text" id="reservatic_res_url" name="reservatic_res_url" value="<?php echo esc_attr(get_option('reservatic_res_url')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="reservatic_api_token"><?php _e('API Token', 'r'); ?></label></th>
                    <td><input type="text" id="reservatic_api_token" name="reservatic_api_token" value="<?php echo esc_attr(get_option('reservatic_api_token')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="reservatic_certificate"><?php _e('Certificate', 'r'); ?></label></th>
                    <td>
                        <input type="file" id="reservatic_certificate" name="reservatic_certificate" />
                        <?php if ($certificate_path): ?>
                            <p><?php _e('Current file:', 'r'); ?> <?php echo esc_html(basename($certificate_path)); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="reservatic_certificate_password"><?php _e('Certificate Password', 'r'); ?></label></th>
                    <td><input type="password" id="reservatic_certificate_password" name="reservatic_certificate_password" value="<?php echo esc_attr(get_option('reservatic_certificate_password')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
