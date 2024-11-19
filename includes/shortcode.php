<?php

if (!defined('ABSPATH')) exit;

ob_start();
function reservatic_form_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'service_id' => '',
        'font_color' => '#000000',
        'element_color' => '#000000',
        'button_text_color' => '#000000',
        'background_color' => '#000000',
        'border_radius' => '0',
        'show_logo' => 1,
        'show_app_link' => 1
    ), $atts);

    $service_id = intval($atts['service_id']);
    $font_color = sanitize_hex_color($atts['font_color']);
    $element_color = sanitize_hex_color($atts['element_color']);
    $button_text_color = sanitize_hex_color($atts['button_text_color']);
    $border_radius = intval($atts['border_radius']);
    $background_color = sanitize_hex_color($atts['background_color']);

    $api = get_reservatic_api();

    $service_detail = json_decode($api->getService(intval($atts['service_id'])), true);

    if (!$api || !$service_id) {
        return __('Prosím, nejprve nakonfigurujte nastavení pluginu.', 'reservatic');
    }

    $places = json_decode($api->getPlaces($service_id), true);

    $logo_url = plugins_url('../img/reservatic-logo.svg', __FILE__);
    ?>
    <style>
        :root {
            --font-color: <?php echo $font_color; ?>;
            --element-color: <?php echo $element_color; ?>;
            --button-text-color: <?php echo $button_text_color; ?>;
            --border-radius: <?php echo $border_radius; ?>px;
            --background-color: <?php echo $background_color; ?>;
        }
    </style>
    <div class="container">
        <div class="container">
            <a aria-label="<?php esc_attr_e('Logo, přejít na úvodní stránku', 'reservatic'); ?>" class="navbar-brand" href="/" style="pointer-events: none; <?php if(intval($atts['show_logo']) == 0):?>display: none; <?php endif; ?>">
                <img src="<?php echo esc_url(plugins_url('../img/logo.svg', __FILE__)); ?>" alt="<?php esc_attr_e('Logo', 'reservatic'); ?>" />
            </a>
        </div>
        <div class="text-center">
            <h1 style="color: <?php echo $font_color ?>"><?php echo $service_detail['name'] ?></h1>
        </div>
        <form style="color: <?php echo $font_color ?>" id="reservatic-form" method="post" action="">
            <div class="container">
                <input type="hidden" name="service-id" id="service-id" value="<?php echo $service_id ?>"/>
                <div class="row">
                    <div class="col-lg-8 col-xl-8 offset-lg-2" id="calendar-container">
                        <table class="table-services">
                            <thead>
                            <tr>
                                <th><?php _e('Vyberte službu', 'reservatic'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($places as $place): ?>
                                <tr>
                                    <td>
                                        <?php echo $place['name'] ?>
                                    </td>
                                    <td class="pull-right">
                                        <a href="#" class="calendar-option btn btn-primary"
                                           data-id="<?php echo $place['id'] ?>"><?php _e('Vybrat', 'reservatic'); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row" id="operation-select-container" style="display: none;">
                    <div class="col-lg-8 col-xl-5 offset-lg-2 offset-xl-3 mt-2_5">
                        <label class="form-label" for="operation"><?php _e('Vyberte úkon', 'reservatic'); ?></label>
                        <select class="form-select shadow" id="operation" name="operation">
                            <option value=""><?php _e('Vyberte úkon', 'reservatic'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row justify-content-center mt-2_5" id="table-container">
                    <div class="col-xxl-12 d-none d-md-block">
                        <table class="table">
                            <thead>
                            <tr>
                                <th><?php _e('Firma', 'reservatic'); ?></th>
                                <th><?php _e('Kategorie úkonu', 'reservatic'); ?></th>
                                <th><?php _e('Úkon', 'reservatic'); ?></th>
                                <th><?php _e('Cena úkonu', 'reservatic'); ?></th>
                                <th><?php _e('Délka úkonu', 'reservatic'); ?></th>
                            </tr>
                            </thead>
                            <tbody id="table-body">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row justify-content-center mt-2_5" id="date-container">
                    <div class="col-xxl-12">
                        <div aria-level="2" class="form-label" role="heading"><?php _e('Vyberte termín', 'reservatic'); ?></div>
                        <div class="row g-4">

                            <div class="col-6 col-md">
                                <div class="card-select" id="year-select-container">
                                    <div aria-level="3" class="title" role="heading"><?php _e('Rok', 'reservatic'); ?></div>
                                    <ul class="data-list" id="year-list"></ul>
                                </div>
                            </div>

                            <div class="col-6 col-md">
                                <div class="card-select" id="month-select-container">
                                    <div aria-level="3" class="title" role="heading"><?php _e('Měsíc', 'reservatic'); ?></div>
                                    <ul class="data-list" id="month-list"></ul>
                                </div>
                            </div>

                            <div class="col-6 col-md">
                                <div class="card-select" id="day-select-container">
                                    <div aria-level="3" class="title" role="heading"><?php _e('Den', 'reservatic'); ?></div>
                                    <ul class="data-list" id="day-list"></ul>
                                </div>
                            </div>

                            <div class="col-6 col-md">
                                <div class="card-select" id="hour-select-container">
                                    <div aria-level="3" class="title" role="heading"><?php _e('Čas', 'reservatic'); ?></div>
                                    <ul class="data-list" id="hour-list"></ul>
                                </div>
                            </div>
                        </div>

                        <div class="final-term">
                            <div aria-level="2" class="form-label" role="heading"><?php _e('Vybraný termín', 'reservatic'); ?></div>
                            <div class="selected-term"></div>
                        </div>
                    </div>
                    <div class="row mt-2_5 " id="reservation-container">
                        <input type="hidden" name="res[service_id]" id="service_id" value="<?php echo $service_id ?>"/>
                        <input type="hidden" name="res[operation_id]" id="operation_id" value=""/>
                        <input type="hidden" name="res[starts_at]" id="starts_at" value=""/>
                        <input type="hidden" name="res[place_id]" id="place_id" value=""/>
                        <input type="hidden" name="res[user_service_id]" id="user_service_id" value=""/>
                        <input type="hidden" name="operation_price" id="operation_price" value=""/>

                        <div class="col-xl-6">
                            <div id="reservation-form" class="row gy-3 user_info_row">
                                <div class="col-md-6">
                                    <label class="form-label" for="first-first_name"><?php _e('Jméno:', 'reservatic'); ?></label>
                                    <input class="form-control" name="res[first_name]" id="first_name"/>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="last_name"><?php _e('Příjmení:', 'reservatic'); ?></label>
                                    <input class="form-control" name="res[last_name]" id="last_name"/>
                                </div>
                            </div>

                        </div>

                        <div class="col-xl-6">
                            <div class="reservation-card reservation-card-blue reservation-card-primary-color">
                                <div class="row gy-5">
                                    <div class="col-lg-12">
                                        <div class="title"><?php _e('Vaše rezervace', 'reservatic'); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div><?php _e('Váš vybraný termín:', 'reservatic'); ?> <br><span class="value" id="reservation-date"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div><?php _e('Čas rezervace:', 'reservatic'); ?><br> <span class="value" id="reservation-time"></span></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div><?php _e('Počet osob:', 'reservatic'); ?> <br><span class="value" id="reservation-people">1</span></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div><?php _e('Celková cena za rezervaci:', 'reservatic'); ?> <br><span class="value" id="reservation-price"></span></div>
                                    </div>
                                    <div class="col-md-12 info">
                                        <?php _e('Po odeslání rezervace vám budou zaslány informace na váš e-mail.', 'reservatic'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 text-center">
                            <input class="btn btn btn-primary btn-size btn-flex ms-auto" type="submit"
                                   name="reservation-submit" value="<?php _e('Rezervovat', 'reservatic'); ?>"/>
                        </div>

                    </div>
                </div>

            </div>
        </form>
        <section style="display: none;" class="section-reservation-complete" id="reservation-success">
            <div class="container">
                <div class="row gy-5">
                    <div class="col-lg-6">
                        <div class="reservation-card reservation-card-first reservation-card-primary-color">
                            <div class="title"><?php _e('Potvrzení o vaší platné rezervaci', 'reservatic'); ?></div>
                            <div class="row gy-5">
                                <div class="col-md-6">
                                    <div class="type"><?php _e('Čas objednání:', 'reservatic'); ?></div>
                                    <div class="value" id="reservation-time-final"></div>
                                </div>

                                <div class="col-md-6">
                                    <div class="type"><?php _e('Délka úkonu:', 'reservatic'); ?></div>
                                    <div class="value" id="reservation-length"></div>
                                </div>

                                <div class="col-md-6">
                                    <div class="type"><?php _e('Úkon:', 'reservatic'); ?></div>
                                    <div class="value" id="reservation-name"></div>
                                </div>

                                <div class="col-md-6">
                                    <div class="type"><?php _e('Cena úkonu:', 'reservatic'); ?></div>
                                    <div class="value" id="final-reservation-price"></div>
                                </div>

                                <div class="col-md-6">
                                    <div class="type"><?php _e('Firma:', 'reservatic'); ?></div>
                                    <div class="value" id="reservation-service-name"></div>
                                </div>

                                <div class="col-md-6">
                                    <div class="type"><?php _e('Adresa:', 'reservatic'); ?></div>
                                    <div class="value" id="reservation-service-address"></div>
                                </div>
                            </div>
                            <div class="breakline">
                            </div>

                            <div class="row gy-5">
                                <div class="col-md-6">
                                    <div class="type"><?php _e('ID rezervace', 'reservatic'); ?>:</div>
                                    <div class="value" id="reservation-id"></div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6 d-flex justify-content-between flex-column mt-5">
                                    <div class="type"><?php _e('Klient:', 'reservatic'); ?></div>
                                    <div class="value" id="reservation-client-name"></div>
                                </div>

                                <div class="col-md-6 d-flex justify-content-between flex-column mt-5">
                                    <div class="type"><?php _e('Váš e-mail:', 'reservatic'); ?></div>
                                    <div class="value" id="reservation-client-email"></div>
                                </div>

                                <div class="col-md-6 d-flex justify-content-between flex-column mt-5 max-cancel-time">
                                    <div class="type"><?php _e('Rezervaci je možno zrušit do:', 'reservatic'); ?></div>
                                    <div class="value" id="reservation-cancel-time"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="text-end mt-5">
                    <a class="btn btn-primary btn-flex me-1 pull-left" href=""><?php _e('Založit novou rezervaci', 'reservatic'); ?></a>
                    <a id="print-pdf" class="btn btn-primary btn-flex me-1"><?php _e('Tisk do PDF', 'reservatic'); ?></a>
                    <a id="cancel-reservation" class="btn btn-red btn-flex"><?php _e('Zrušit rezervaci', 'reservatic'); ?></a>
                </div>
            </div>
        </section>
        <div class="text-center mt-2_5">
            <div class="apps" <?php if(intval($atts['show_app_link']) == 0):?> style="display: none;" <?php endif; ?>>
                <a target="_blank" class="btn btn-link" href="https://play.google.com/store/apps/details?id=com.reservatic.android"><img aria-label="Google Play" height="56.744" width="169.754" src="https://dev.reservatic.com/assets/public-v2/google-play-dark-1d00d2ec6b09a5c3cddd59f7911acaa4ab3828e385a4a8d40dc1746c65c063bb.svg">
                </a><a target="_blank" class="btn btn-link" href="https://apps.apple.com/app/reservatic-com/id1456245635"><img aria-label="App Store" height="56.744" width="169.754" src="https://dev.reservatic.com/assets/public-v2/app-store-dark-24bce9ecb3986bcb91092df7f1caaada2ade0152bc0e5b90197b89c5295010ad.svg">
                </a>
            </div>
            <p class="m-t">
                <?php _e('Vytvořeno pomocí rezervačního systému', 'reservatic'); ?>
                <a target="_blank" href="https://dev.reservatic.com/cs">Reservatic.com</a>
            </p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function submit_reservation_form()
{
    $api = get_reservatic_api();

    if (isset($_POST['formData'])) {
        $form_data = array();
        parse_str($_POST['formData'], $form_data);

        $data = array();

        foreach ($form_data['res'] as $index => $value) {
            $data[$index] = $value;
        }

        if (isset($_POST['reservationColumns'])) {
            $data['reservation_columns_attributes'] = $_POST['reservationColumns'];
        }

        $rez = json_decode($api->postReservation(['reservation' => $data]), true);

        if ($rez) {
            if ($rez['errors']) {
                $response = [
                    'success' => false,
                    'message' => 'Rezervaci se nepodařilo odeslat',
                    'reservation_data' => $rez,
                ];
            } else {
                $response = [
                    'success' => true,
                    'message' => 'Rezervace byla úspěšná',
                    'reservation_data' => $rez,
                    'service_data' => get_service_detail($form_data['service-id'])
                ];
            }

        } else {
            $response = [
                'success' => false,
                'message' => 'Rezervaci se nepodařilo odeslat'
            ];
        }

        wp_send_json($response);
    }
}

add_action('wp_ajax_submit_reservation_form', 'submit_reservation_form');
add_action('wp_ajax_nopriv_submit_reservation_form', 'submit_reservation_form');

function get_service_detail($id)
{
    $api = get_reservatic_api();

    return json_decode($api->getService($id), true);
}

function get_place_detail($service_id, $place_id)
{
    $api = get_reservatic_api();

    $places = json_decode($api->getPlaces($service_id), true);

    $place_detail = '';

    foreach ($places as $place) {
        if ($place['id'] == $place_id)
            $place_detail = $place;
    }

    return $place_detail;
}

function get_operation_detail($service_id, $operation_id)
{
    $api = get_reservatic_api();

    $operations = json_decode($api->getOperations($service_id), true);

    $operation_detail = '';

    foreach ($operations as $operation) {
        if ($operation['id'] == $operation_id)
            $operation_detail = $operation;
    }

    return $operation_detail;
}

function get_operations()
{
    $api = get_reservatic_api();

    if (!$api) {
        wp_send_json_error('Api configuration is missing');
        return;
    }

    $service_id = intval($_GET['service_id']);
    $operations = json_decode($api->getOperations($service_id), true);

    wp_send_json($operations);
}

function get_operation_summary()
{
    $api = get_reservatic_api();

    if (!$api) {
        wp_send_json_error('Api configuration is missing');
        return;
    }

    $service_id = intval($_GET['service_id']);
    $operation_id = intval($_GET['operation_id']);
    $calendar_id = intval($_GET['calendar_id']);

    $service = get_service_detail($service_id);
    $place = get_place_detail($service_id, $calendar_id);
    $operation = get_operation_detail($service_id, $operation_id);

    $operation_summary = ['data' => ['service_name' => $service['name'], 'place_name' => $place['name'], 'operation_name' => $operation['name'], 'operation_price' => $operation['price_with_vat_label'], 'operation_length' => $operation['minutes']]];

    wp_send_json($operation_summary);
}

function get_years()
{
    $api = get_reservatic_api();

    if (!$api) {
        wp_send_json_error('Api configuration is missing');
        return;
    }

    $service_id = intval($_GET['service_id']);
    $operation_id = intval($_GET['operation_id']);

    $years = json_decode($api->getServiceYears($service_id, $operation_id));

    wp_send_json($years);
}

function get_months()
{
    $api = get_reservatic_api();

    if (!$api) {
        wp_send_json_error('Api configuration is missing');
        return;
    }

    $service_id = intval($_GET['service_id']);
    $operation_id = intval($_GET['operation_id']);
    $year = intval($_GET['year']);

    $months = json_decode($api->getServiceMonths($service_id, $operation_id, $year));

    wp_send_json($months);
}

function get_days()
{
    $api = get_reservatic_api();

    if (!$api) {
        wp_send_json_error('Api configuration is missing');
        return;
    }

    $service_id = intval($_GET['service_id']);
    $operation_id = intval($_GET['operation_id']);
    $year = intval($_GET['year']);
    $month = intval($_GET['month']);

    $days = json_decode($api->getServiceDays($service_id, $operation_id, $year, $month));

    wp_send_json($days);
}

function get_hours()
{
    $api = get_reservatic_api();

    if (!$api) {
        wp_send_json_error('Api configuration is missing');
        return;
    }

    $service_id = intval($_GET['service_id']);
    $operation_id = intval($_GET['operation_id']);
    $day = $_GET['day'];

    $hours = json_decode($api->getServiceHours($service_id, $operation_id, $day), true);

    $required_fields = get_required_fields($service_id, $operation_id);

    $countries = array();
    $insurance_companies = array();
    $phone_codes = array();

    if (array_key_exists('country_id', $required_fields))
        $countries = json_decode($api->getCountries(), true);

    if (array_key_exists('insurance_company_id', $required_fields))
        $insurance_companies = json_decode($api->getInsuranceCompanies(), true);

    if (array_key_exists('phone_code', $required_fields))
        $phone_codes = json_decode($api->getPhoneCodes(), true);

    wp_send_json(['hours' => $hours, 'free_people' => $hours[0]['free_people'], 'required_fields' => $required_fields, 'operation_columns' => get_operation_columns($service_id, $operation_id), 'countries' => $countries, 'insurance_companies' => $insurance_companies, 'phone_codes' => $phone_codes]);
}

function delete_reservation()
{
    $api = get_reservatic_api();

    $reservation_id = intval($_POST['reservation_id']);

    wp_send_json(['response' => json_decode($api->deleteReservation($reservation_id))]);
}

add_action('wp_ajax_get_operations', 'get_operations');
add_action('wp_ajax_nopriv_get_operations', 'get_operations');

add_action('wp_ajax_get_operation_summary', 'get_operation_summary');
add_action('wp_ajax_nopriv_get_operation_summary', 'get_operation_summary');

add_action('wp_ajax_get_years', 'get_years');
add_action('wp_ajax_nopriv_get_years', 'get_years');

add_action('wp_ajax_get_months', 'get_months');
add_action('wp_ajax_nopriv_get_months', 'get_months');

add_action('wp_ajax_get_days', 'get_days');
add_action('wp_ajax_nopriv_get_days', 'get_days');

add_action('wp_ajax_get_hours', 'get_hours');
add_action('wp_ajax_nopriv_get_hours', 'get_hours');

add_action('wp_ajax_delete_reservation', 'delete_reservation');
add_action('wp_ajax_nopriv_delete_reservation', 'delete_reservation');

function format_time($time): string
{
    return date('Y-m-d\TH:i:sP', strtotime($time));
}

function get_required_fields($service_id, $operation_id)
{
    $names = [
        'email' => __('Email', 'reservatic'),
        'phone' => __('Telefon', 'reservatic'),
        'birth_cert_no' => __('Rodné číslo', 'reservatic'),
        'holder_id' => __('Číslo pojištěnce', 'reservatic'),
        'birth_certificate_number' => __('Rodné číslo', 'reservatic'),
        'date_of_birth' => __('Datum narození', 'reservatic'),
        'spz' => __('SPZ/RZ vozidla', 'reservatic'),
        'street' => __('Ulice a č.p.', 'reservatic'),
        'city' => __('Město', 'reservatic'),
        'zip' => __('PSČ', 'reservatic'),
        'address' => __('Adresa', 'reservatic'),
        'insurance_company' => __('Pojišťovna', 'reservatic')
    ];

    $api = get_reservatic_api();

    $operations = json_decode($api->getOperations($service_id), true);

    $required_fields = false;

    foreach ($operations as $operation) {
        if ($operation['id'] == $operation_id) {
            $tmp_required_fields = array();
            if (!$operation['anonymous_without_email'])
                $tmp_required_fields[] = 'email';
            $tmp_required_fields = array_merge($tmp_required_fields, $operation['required_fields']);
        }
    }

    if ($tmp_required_fields) {
        foreach ($tmp_required_fields as $index => $value) {
            if (isset($names[$value])) {
                if ($value == 'insurance_company') {
                    $required_fields['insurance_company_id'] = $names[$value];
                } elseif ($value == 'address') {
                    $required_fields['street'] = __('Ulice', 'reservatic');
                    $required_fields['city'] = __('Město', 'reservatic');
                    $required_fields['zip'] = __('PSČ', 'reservatic');
                    $required_fields['country_id'] = __('Země', 'reservatic');
                } elseif ($value == 'birth_cert_no')
                    $required_fields['birth_certificate_number'] = $names[$value];
                elseif ($value == 'phone') {
                    $required_fields['phone_code'] = __('Předvolba', 'reservatic');
                    $required_fields['phone'] = $names[$value];
                } else
                    $required_fields[$value] = $names[$value];
            }
        }
    }

    return $required_fields;
}

function get_operation_columns($service_id, $operation_id)
{
    $api = get_reservatic_api();

    $operations = json_decode($api->getOperations($service_id), true);

    $operation_columns = false;

    foreach ($operations as $operation) {
        if ($operation['id'] == $operation_id) {
            $operation_columns = $operation['operation_columns'];
        }
    }

    return $operation_columns;
}


