<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function reservatic_forms_page() {
    ?>
    <div>
        <h2><?php _e('Reservatic formuláře', 'reservatic'); ?></h2>
        <button class="button button-primary" id="add-new-form" style="margin-bottom: 20px;"><?php _e('Vytvořit nový formulář', 'reservatic'); ?></button>
        <div id="new-form-container" style="display: none;">
            <form method="post" action="" style="display: flex; flex-direction: column; gap: 10px; max-width: 400px;">
                <label for="service_id"><?php _e('Vyberte službu:', 'reservatic'); ?></label>
                <select id="service_id" name="service_id">
                    <?php
                    $api = get_reservatic_api();
                    if ($api) {
                        $services = json_decode($api->getServices(), true);
                        foreach ($services as $service) {
                            echo '<option value="' . $service['id'] . '">' . $service['name'] . '</option>';
                        }
                    }
                    ?>
                </select>
                <label for="element_color"><?php _e('Barva prvků:', 'reservatic'); ?></label>
                <input type="color" id="element_color" name="element_color" value="#072e49">
                <label for="button_text_color"><?php _e('Barva textu tlačítek:', 'reservatic'); ?></label>
                <input type="color" id="button_text_color" name="button_text_color" value="#ffffff">
                <label for="border_radius"><?php _e('Zaoblení rohů (px):', 'reservatic'); ?></label>
                <input type="number" id="border_radius" name="border_radius" value="0" style="width: 100px;">
                <label for="background_color"><?php _e('Barva pozadí:', 'reservatic'); ?></label>
                <input type="color" id="background_color" name="background_color" value="#f4f6f9">
                <label for="font_color"><?php _e('Barva textu:', 'reservatic'); ?></label>
                <input type="color" id="font_color" name="font_color" value="#072e49">
                <div style="display: flex; align-items: center;">
                    <input type="checkbox" id="show_logo" name="show_logo" value="1">
                    <label for="show_logo" style="margin-left: 5px;"><?php _e('Zobrazit logo Reservatic', 'reservatic'); ?></label>
                </div>
                <div style="display: flex; align-items: center;">
                    <input type="checkbox" id="show_app_link" name="show_app_link" value="1">
                    <label for="show_app_link" style="margin-left: 5px;"><?php _e('Zobrazit GooglePlay a AppStore ikony', 'reservatic'); ?></label>
                </div>
                <input type="submit" name="generate_form" value="<?php _e('Generovat formulář', 'reservatic'); ?>" style="width: auto;">
            </form>
        </div>

        <?php
        if (isset($_POST['generate_form']) && isset($_POST['service_id'])) {
            $service_id = intval($_POST['service_id']);
            $font_color = sanitize_hex_color($_POST['font_color']);
            $element_color = sanitize_hex_color($_POST['element_color']);
            $button_text_color = sanitize_hex_color($_POST['button_text_color']);
            $border_radius = intval($_POST['border_radius']);
            $background_color = sanitize_hex_color($_POST['background_color']);
            $show_logo = isset($_POST['show_logo']) ? 1 : 0;
            $show_app_link = isset($_POST['show_app_link']) ? 1 : 0;
            $form_count = get_option('reservatic_form_count', 0) + 1;
            update_option('reservatic_form_count', $form_count);

            // Uložíme service_id, font_color, element_color, button_text_color, border_radius, background_color, show_logo a show_app_link pro každý formulář
            $forms_data = get_option('reservatic_forms_data', array());
            $forms_data[$form_count] = array(
                'service_id' => $service_id,
                'font_color' => $font_color,
                'element_color' => $element_color,
                'button_text_color' => $button_text_color,
                'border_radius' => $border_radius,
                'background_color' => $background_color,
                'show_logo' => $show_logo,
                'show_app_link' => $show_app_link
            );
            update_option('reservatic_forms_data', $forms_data);

            echo '<h3>' . sprintf(__('Shortcode for Service ID %d:', 'reservatic'), $service_id) . '</h3>';
            echo '<code>' . htmlspecialchars('[reservatic_form_' . $form_count . ']') . '</code>';
        }

        // Zobrazíme existující formuláře
        $form_count = get_option('reservatic_form_count', 0);
        $forms_data = get_option('reservatic_forms_data', array());
        if ($form_count > 0) {
            echo '<h3>' . __('Existující formuláře:', 'reservatic') . '</h3>';
            echo '<div class="form-list">';
            for ($i = 1; $i <= $form_count; $i++) {
                if (isset($forms_data[$i])) {
                    echo '<div class="form-item">';
                    echo '<div class="form-shortcode">' . htmlspecialchars('[reservatic_form_' . $i . ']') . '</div>';
                    echo '<div class="form-actions">';
                    echo ' <a class="button button-primary" href="' . admin_url('admin.php?page=reservatic-edit-form&form_id=' . $i) . '">' . __('Editovat', 'reservatic') . '</a>';
                    echo ' <a class="button button-secondary" href="' . admin_url('admin.php?page=reservatic-forms&delete_form=' . $i) . '">' . __('Smazat', 'reservatic') . '</a>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            echo '</div>';
        }

        // Smazání formuláře
        if (isset($_GET['delete_form'])) {
            $delete_form_id = intval($_GET['delete_form']);
            unset($forms_data[$delete_form_id]);
            update_option('reservatic_forms_data', $forms_data);
            echo '<p>' . sprintf(__('Formulář %d smazán.', 'reservatic'), $delete_form_id) . '</p>';
            ?>
            <script>
                // Remove the delete_form parameter from the URL and refresh the page
                if (history.pushState) {
                    var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?page=reservatic-forms";
                    window.history.pushState({path: newUrl}, '', newUrl);
                }
                window.location.reload();
            </script>
            <?php
        }
        ?>
    </div>
    <style>
        .form-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .form-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .form-shortcode {
            font-family: monospace;
            font-size: 1.1em;
        }
        .form-actions .button {
            margin-left: 10px;
        }
    </style>
    <script>
        document.getElementById('add-new-form').addEventListener('click', function() {
            document.getElementById('new-form-container').style.display = 'block';
        });
    </script>
    <?php
}

function reservatic_edit_form_page() {
    if (!isset($_GET['form_id'])) {
        echo '<p>' . __('Invalid form ID.', 'reservatic') . '</p>';
        return;
    }

    $form_id = intval($_GET['form_id']);
    $forms_data = get_option('reservatic_forms_data', array());

    if (!isset($forms_data[$form_id])) {
        echo '<p>' . __('Form not found.', 'reservatic') . '</p>';
        return;
    }

    $form_data = $forms_data[$form_id];

    ?>
    <div>
        <h2><?php echo sprintf(__('Editovat formulář %d', 'reservatic'), $form_id); ?></h2>
        <form id="edit-form" method="post" action="" style="display: flex; flex-direction: column; gap: 10px; max-width: 400px;">
            <input type="hidden" name="form_id" value="<?php echo $form_id; ?>" />
            <label for="service_id"><?php _e('Vyberte službu:', 'reservatic'); ?></label>
            <select id="service_id" name="service_id">
                <?php
                $api = get_reservatic_api();
                if ($api) {
                    $services = json_decode($api->getServices(), true);
                    foreach ($services as $service) {
                        $selected = $service['id'] == $form_data['service_id'] ? 'selected' : '';
                        echo '<option value="' . $service['id'] . '" ' . $selected . '>' . $service['name'] . '</option>';
                    }
                }
                ?>
            </select>
            <label for="element_color"><?php _e('Barva prvků:', 'reservatic'); ?></label>
            <input type="color" id="element_color" name="element_color" value="<?php echo $form_data['element_color']; ?>">
            <label for="button_text_color"><?php _e('Barva textu tlačítek:', 'reservatic'); ?></label>
            <input type="color" id="button_text_color" name="button_text_color" value="<?php echo $form_data['button_text_color']; ?>">
            <label for="border_radius"><?php _e('Zaoblení rohů (px):', 'reservatic'); ?></label>
            <input type="number" id="border_radius" name="border_radius" value="<?php echo $form_data['border_radius']; ?>" style="width: 100px;">
            <label for="background_color"><?php _e('Barva pozadí:', 'reservatic'); ?></label>
            <input type="color" id="background_color" name="background_color" value="<?php echo $form_data['background_color']; ?>">
            <label for="font_color"><?php _e('Barva textu:', 'reservatic'); ?></label>
            <input type="color" id="font_color" name="font_color" value="<?php echo $form_data['font_color']; ?>">
            <div style="display: flex; align-items: center;">
                <input type="checkbox" id="show_logo" name="show_logo" value="1" <?php echo $form_data['show_logo'] ? 'checked' : ''; ?>>
                <label for="show_logo" style="margin-left: 5px;"><?php _e('Zobrazit logo Reservatic', 'reservatic'); ?></label>
            </div>
            <div style="display: flex; align-items: center;">
                <input type="checkbox" id="show_app_link" name="show_app_link" value="1" <?php echo $form_data['show_app_link'] ? 'checked' : ''; ?>>
                <label for="show_app_link" style="margin-left: 5px;"><?php _e('Zobrazit GooglePlay a AppStore ikony', 'reservatic'); ?></label>
            </div>
            <input type="submit" name="update_form" value="<?php _e('Aktualizovat formulář', 'reservatic'); ?>" style="width: auto;">
        </form>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#edit-form').on('submit', function(e) {
                e.preventDefault(); // Zabráníme standardnímu odeslání formuláře

                var formData = $(this).serialize(); // Serializujeme data formuláře

                console.log(formData);
                $.ajax({
                    type: 'POST',
                    url: ajaxurl, // URL pro AJAX požadavek
                    data: {
                        action: 'update_reservatic_form',
                        form_data: formData
                    },
                    success: function(response) {
                        if (response.success) {
                            // Po úspěšném uložení dat přesměrujeme uživatele
                            window.location.href = '<?php echo admin_url('admin.php?page=reservatic-forms'); ?>';
                        } else {
                            alert('<?php _e('Error updating form.', 'reservatic'); ?>');
                        }
                    }
                });
            });
        });
    </script>
    <?php
}

add_action('wp_ajax_update_reservatic_form', 'update_reservatic_form');
function update_reservatic_form() {
    if (!isset($_POST['form_data'])) {
        wp_send_json_error();
    }

    parse_str($_POST['form_data'], $form_data);

    $forms = get_option('reservatic_forms_data', array());

    $form_id = intval($form_data['form_id']);

    $service_id = intval($form_data['service_id']);
    $font_color = sanitize_hex_color($form_data['font_color']);
    $element_color = sanitize_hex_color($form_data['element_color']);
    $button_text_color = sanitize_hex_color($form_data['button_text_color']);
    $border_radius = intval($form_data['border_radius']);
    $background_color = sanitize_hex_color($form_data['background_color']);
    $show_logo = isset($form_data['show_logo']) ? 1 : 0;
    $show_app_link = isset($form_data['show_app_link']) ? 1 : 0;

    $forms[$form_id] = array(
        'service_id' => $service_id,
        'font_color' => $font_color,
        'element_color' => $element_color,
        'button_text_color' => $button_text_color,
        'border_radius' => $border_radius,
        'background_color' => $background_color,
        'show_logo' => $show_logo,
        'show_app_link' => $show_app_link
    );

    update_option('reservatic_forms_data', $forms);

    wp_send_json_success();
}
?>
