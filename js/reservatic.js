jQuery(document).ready(function ($) {
    $('#calendar-container').on('click', '.calendar-option', function (e) {
        e.preventDefault();
        var calendarId = $(this).data('id');
        var operationSelectContainer = $('#operation-select-container');
        var operationSelect = $('#operation');
        var serviceId = $('#service-id').val();

        $('#calendar-container .calendar-option').removeClass('active');
        $(this).addClass('active');

        if (calendarId) {
            $.ajax({
                url: ajax_object.ajaxurl,
                method: 'GET',
                data: {
                    action: 'get_operations',
                    calendar_id: calendarId,
                    service_id: serviceId
                },
                success: function (data) {
                    operationSelect.empty();
                    operationSelect.append($('<option>', {
                        value: '',
                        text: ajax_object.translations.select_operation // Překlad
                    }));
                    $.each(data, function (index, operation) {
                        operationSelect.append($('<option>', {
                            value: operation.id,
                            text: operation.name + ' (' + operation.price_with_vat_label + ', ' + operation.minutes + 'min.)'
                        }));
                    });
                    operationSelectContainer.show();
                }
            });
        } else {
            operationSelectContainer.hide();
        }
    });

    $('#operation').on('change', function () {
        var operationId = $(this).val();
        var yearSelectContainer = $('#year-select-container');
        var monthSelectContainer = $('#month-select-container');
        var daySelectContainer = $('#day-select-container');
        var hourSelectContainer = $('#hour-select-container');
        var yearList = $('#year-list');
        var monthList = $('#month-list');
        var dayList = $('#day-list');
        var serviceId = $('#service-id').val();
        var hourList = $('#hour-list');
        var calendarId = $('#calendar-container .calendar-option.active').data('id');
        var dateContainer = $('#date-container');

        cleanReservationForm();

        yearList.empty();
        monthList.empty();
        dayList.empty();
        hourList.empty();
        yearSelectContainer.hide();
        monthSelectContainer.hide();
        daySelectContainer.hide();
        hourSelectContainer.hide();

        if (operationId) {
            $.ajax({
                url: ajax_object.ajaxurl,
                method: 'GET',
                data: {
                    action: 'get_operation_summary',
                    operation_id: operationId,
                    service_id: serviceId,
                    calendar_id: calendarId
                },
                success: function (summaryData) {

                    var tbody = $('#table-body')

                    tbody.empty();

                    var tr = $('<tr>');

                    $.each(summaryData.data, function (index, item) {
                        if (item == 'Free')
                            var td = $('<td>').text(ajax_object.translations.free); // Překlad
                        else if (index == 'operation_length')
                            var td = $('<td>').text(item + ' min.');
                        else
                            var td = $('<td>').text(item);
                        if (index == 'operation_price') {
                            $('#operation_price').val(item);
                        }
                        tr.append(td);
                    });

                    tbody.append(tr);
                    $('#table-container').show();

                    var yearRequest = $.ajax({
                        url: ajax_object.ajaxurl,
                        method: 'GET',
                        data: {
                            action: 'get_years',
                            operation_id: operationId,
                            service_id: serviceId
                        }
                    });

                    var monthRequest = yearRequest.then(function (years) {
                        yearList.empty();
                        $.each(years, function (index, year) {
                            var li = $('<li>').append($('<a>', {
                                href: '#',
                                class: 'year-option data-link',
                                'data-year': year.year,
                                text: year.year
                            }));
                            if (index === 0) {
                                li.find('a').addClass('active');
                            }
                            yearList.append(li);
                        });

                        return $.ajax({
                            url: ajax_object.ajaxurl,
                            method: 'GET',
                            data: {
                                action: 'get_months',
                                operation_id: operationId,
                                service_id: serviceId,
                                year: years[0].year
                            }
                        });
                    });

                    var dayRequest = monthRequest.then(function (months) {
                        monthList.empty();
                        $.each(months, function (index, month) {
                            var li = $('<li class="item">').append($('<a>', {
                                href: '#',
                                class: 'month-option data-link',
                                'data-month': month.month,
                                text: month.month
                            }));
                            if (index === 0) {
                                li.find('a').addClass('active');
                            }
                            monthList.append(li);
                        });

                        return $.ajax({
                            url: ajax_object.ajaxurl,
                            method: 'GET',
                            data: {
                                action: 'get_days',
                                operation_id: operationId,
                                service_id: serviceId,
                                year: $('#year-list .year-option.active').data('year'),
                                month: months[0].month
                            }
                        });
                    });

                    dayRequest.then(function (days) {
                        dayList.empty();
                        $.each(days, function (index, day) {
                            dayList.append($('<li class="item">').append($('<a>', {
                                href: '#',
                                class: 'day-option data-link',
                                'data-day': day.date,
                                text: getSpecificDate(day.date)
                            })));
                        });

                        $('#calendar-container').hide();
                        dateContainer.show();
                        yearSelectContainer.show();
                        monthSelectContainer.show();
                        daySelectContainer.show();
                    });
                }
            });
        } else {
            yearSelectContainer.hide();
            monthSelectContainer.hide();
            daySelectContainer.hide();
        }
    });

    $('#year-list').on('click', '.year-option', function (e) {
        e.preventDefault();
        $('#year-list .year-option').removeClass('active');
        $(this).addClass('active');
        var operationId = $('#operation').val();
        var year = $(this).data('year');
        var monthSelectContainer = $('#month-select-container');
        var daySelectContainer = $('#day-select-container');
        var monthList = $('#month-list');
        var dayList = $('#day-list');
        var serviceId = $('#service-id').val();

        $('#hour-select-container').hide();
        $('#hour-list').empty();
        daySelectContainer.hide();
        dayList.empty();
        monthSelectContainer.hide();
        monthList.empty();

        if (operationId) {
            $.ajax({
                url: ajax_object.ajaxurl,
                method: 'GET',
                data: {
                    action: 'get_months',
                    operation_id: operationId,
                    service_id: serviceId,
                    year: year
                },
                success: function (data) {
                    monthList.empty();
                    $.each(data, function (index, month) {
                        var li = $('<li class="item">').append($('<a>', {
                            href: '#',
                            class: 'month-option data-link',
                            'data-month': month.month,
                            text: month.month
                        }));
                        if (index === 0) {
                            li.find('a').addClass('active');
                        }
                        monthList.append(li);
                    });
                    monthSelectContainer.show();
                }
            });
        } else {
            monthSelectContainer.hide();
        }
    });

    $('#month-list').on('click', '.month-option', function (e) {
        e.preventDefault();
        $('#month-list .month-option').removeClass('active');
        $(this).addClass('active');
        var operationId = $('#operation').val();
        var year = $('#year-list .year-option.active').data('year');
        var month = $(this).data('month');
        var daySelectContainer = $('#day-select-container');
        var dayList = $('#day-list');
        var serviceId = $('#service-id').val();

        $('#hour-select-container').hide();
        $('#hour-list').empty();

        if (operationId) {
            $.ajax({
                url: ajax_object.ajaxurl,
                method: 'GET',
                data: {
                    action: 'get_days',
                    operation_id: operationId,
                    service_id: serviceId,
                    year: year,
                    month: month
                },
                success: function (data) {
                    dayList.empty();
                    $.each(data, function (index, day) {
                        dayList.append($('<li class="item">').append($('<a>', {
                            href: '#',
                            class: 'day-option data-link',
                            'data-day': day.date,
                            text: getSpecificDate(day.date)
                        })));
                    });
                    daySelectContainer.show();
                }
            });
        } else {
            daySelectContainer.hide();
        }
    });

    $('#day-list').on('click', '.day-option', function (e) {
        e.preventDefault();
        $('#day-list .day-option').removeClass('active');
        $(this).addClass('active');
        var operationId = $('#operation').val();
        var day = $(this).data('day');
        var hourSelectContainer = $('#hour-select-container');
        var hourList = $('#hour-list');
        var serviceId = $('#service-id').val();

        cleanReservationForm();

        if (operationId) {
            $('#operation_id').val(operationId);
            $('#place_id').val($('#calendar-container .calendar-option.active').data('id'));

            $.ajax({
                url: ajax_object.ajaxurl,
                method: 'GET',
                data: {
                    action: 'get_hours',
                    operation_id: operationId,
                    service_id: serviceId,
                    day: day
                },
                success: function (data) {
                    hourList.empty();
                    $.each(data.hours, function (index, hour) {
                        hourList.append($('<li>').append($('<a>', {
                            href: '#',
                            class: 'hour-option data-link',
                            'data-hour': JSON.stringify(hour),
                            text: hour.label
                        })));
                    });
                    hourSelectContainer.show();

                    var container = $('#reservation-form');

                    if (data.free_people > 1) {
                        var spanTitle = $('<span>').text(ajax_object.translations.free_spots + ' ' + data.free_people); // Překlad

                        var label = $('<label>').attr('for', 'res_people').text(ajax_object.translations.number_of_people).addClass('me-2'); // Překlad

                        var input = $('<input>').attr({
                            type: 'number',
                            name: 'res[people]',
                            id: 'res_people',
                            required: true,
                            min: 1,
                            max: data.free_people,
                            step: 1,
                            value: 1
                        }).addClass('form-control w-auto');

                        var wrapper = $('<div>').addClass('d-flex align-items-center mb-3');
                        wrapper.append(label).append(input);

                        container.prepend(wrapper).prepend(spanTitle);
                    }

                    $.each(data.required_fields, function (index, value) {

                        if (index != 'phone_code') {
                            if (index == 'email')
                                var div = $('<div class="col-md-12">')
                            else
                                var div = $('<div class="col-md-6">')

                            var label = $('<label class="form-label">').attr('for', index).text('*' + value);

                            if (index == 'country_id') {
                                var input = $('<select class="select">').attr({
                                    id: index,
                                    name: 'res[' + index + ']'
                                });

                                var optionElement = $('<option>').attr('value', '').text(' ');
                                input.append(optionElement);

                                $.each(data.countries, function (countryId, option) {
                                    var optionElement = $('<option>').attr('value', option.id).text(option.name);
                                    input.append(optionElement);
                                });
                            } else if (index == 'insurance_company_id') {
                                var input = $('<select class="select">').attr({
                                    id: index,
                                    name: 'res[' + index + ']',
                                    required: true
                                });

                                var optionElement = $('<option>').attr('value', '').text(' ');
                                input.append(optionElement);

                                $.each(data.insurance_companies, function (companyId, option) {
                                    var optionElement = $('<option>').attr('value', option.id).text(option.name);
                                    input.append(optionElement);
                                });
                            } else if (index == 'phone') {
                                var phone_code = $('<select>').attr({
                                    id: 'phone_code',
                                    class: 'phone_codes',
                                    name: 'res[phone_code]',
                                    required: true
                                }).addClass('form-select');

                                $.each(data.phone_codes, function (phoneId, option) {
                                    var optionElement = $('<option>').attr('value', option.value).text(option.value);
                                    phone_code.append(optionElement);
                                });

                                var input = $('<input>').attr({
                                    type: 'text',
                                    id: index,
                                    name: 'res[' + index + ']',
                                    required: true
                                }).addClass('form-control');

                                var phoneWrapper = $('<div>').addClass('d-flex align-items-center');
                                phoneWrapper.append(phone_code).append(input);

                                div.append(label).append(phoneWrapper);
                            } else if (index == 'date_of_birth') {
                                var input = $('<input>').attr({
                                    type: 'date',
                                    id: index,
                                    name: 'res[' + index + ']',
                                    required: true
                                });
                            } else {
                                var input = $('<input>').attr({
                                    type: 'text',
                                    id: index,
                                    name: 'res[' + index + ']',
                                    required: true
                                });
                            }

                            if (index != 'phone') {
                                input.addClass('form-control');
                                div.append(label).append(input);
                            }

                            container.append(div);
                        }

                    });
                    $.each(data.operation_columns, function (index, value) {

                        var div = $('<div class="col-md-6">');

                        if (value.column_type == 'string') {
                            var label = $('<label class="form-label">').attr('for', index).text(value.name);

                            var input = $('<input>').attr({
                                type: 'text',
                                class: 'form-control',
                                id: index,
                                name: 'reservation_columns_attributes[' + index + '][value]'
                            }).data('column', {
                                name: value.name,
                                column_type: value.column_type,
                                is_required: value.is_required
                            });

                            div.append(label).append(input);
                            container.append(div).append('<br>');
                        } else if (value.column_type == 'options') {
                            var label = $('<label class="form-label">').attr('for', index).text(value.name);

                            var select = $('<select>').attr({
                                id: index,
                                class: 'form-control',
                                name: 'reservation_columns_attributes[' + index + '][value]'
                            }).data('column', {
                                name: value.name,
                                column_type: value.column_type,
                                is_required: value.is_required
                            });

                            $.each(value.options, function (optIndex, option) {
                                var optionElement = $('<option>').attr('value', option).text(option);
                                select.append(optionElement);
                            });

                            div.append(label).append(select)
                            container.append(div).append('<br>');
                        } else if (value.column_type == 'boolean') {
                            var label = $('<label>').attr('for', index).text(value.name);

                            var checkbox = $('<input>').attr({
                                type: 'checkbox',
                                class: 'boolean optional form-check-input',
                                id: index,
                                name: 'reservation_columns_attributes[' + index + '][value]',
                                value: 'true'
                            }).data('column', {
                                name: value.name,
                                column_type: value.column_type,
                                is_required: value.is_required
                            });

                            div.append(checkbox).append(label)
                            container.append(div).append('<br>');
                        }
                    });
                }
            });
        } else {
            daySelectContainer.hide();
        }
    });

    $('#hour-list').on('click', '.hour-option', function (e) {
        e.preventDefault();
        var dateInfo = $(this).data('hour');
        if (dateInfo) {
            //$('#reservation-container').show();
            $('#reservation-container').css('display', 'flex');
            $('#starts_at').val(dateInfo.starts_at);
            $('#user_service_id').val(dateInfo.user_service_id);

            var dateTime = formatDateTime(dateInfo.starts_at)

            var parts = dateTime.split(', ');

            var date = parts[0];
            var time = parts[1];

            $('#reservation-date').text(date);
            $('#reservation-time').text(time);
            if($('#operation_price').val() == 'Free')
                $('#reservation-price').text(ajax_object.translations.free);
            else
                $('#reservation-price').text($('#operation_price').val());
            $('.selected-term').text(getFinalTerm(dateInfo.starts_at));
            $('.final-term').show();
        }
    });

    $(document).on('input', 'input[name="res[people]"]', function () {
        $('#reservation-people').text($(this).val());
    });


    $('#reservatic-form').on('submit', function (e) {
        e.preventDefault();

        var formData = $(this).serialize();

        var reservationColumns = [];

        $('input[name^="reservation_columns_attributes"], select[name^="reservation_columns_attributes"]').each(function () {
            var columnData = $(this).data('column');
            columnData.value = $(this).val();
            if ($(this).attr('type') === 'checkbox') {
                columnData.value = $(this).is(':checked') ? 'true' : 'false';
            }
            reservationColumns.push(columnData);
        });

        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'submit_reservation_form',
                formData: formData,
                reservationColumns: reservationColumns
            },
            success: function (response) {

                if (response.success) {
                    var reservationData = response.reservation_data
                    var serviceData = response.service_data;

                    $('#reservatic-form').hide();
                    $('#reservation-success').show();

                    $('#reservation-time-final').text(formatDateTime(reservationData.starts_at));
                    $('#reservation-length').text(getMinutesDifference(reservationData.starts_at, reservationData.ends_at) + ' minut');
                    $('#reservation-name').text(reservationData.operation_name);
                    if (reservationData.operation_price != '0')
                        $('#final-reservation-price').text(reservationData.operation_price);
                    else
                        $('#final-reservation-price').text(ajax_object.translations.free); // Překlad
                    $('#reservation-service-name').text(serviceData.name);
                    $('#reservation-service-address').text(serviceData.address);
                    $('#reservation-id').text(reservationData.id);
                    $('#cancel-reservation').attr('data-id', reservationData.id);
                    $('#reservation-client-name').text(reservationData.first_name + ' ' + reservationData.last_name);
                    $('#reservation-client-email').text(reservationData.email);

                    if (reservationData.max_delete_at != null && reservationData.can_cancel == true) {
                        $('#reservation-cancel-time').text(formatDateTime(reservationData.max_delete_at))
                        console.log(reservationData.max_delete_at);
                    } else {
                        $('.max-cancel-time').hide();
                    }

                    return false;
                } else {
                    alert(ajax_object.translations.reservation_error); // Překlad
                    console.log(response.reservation_data);
                }

            },
            error: function (error) {
                alert(ajax_object.translations.error); // Překlad
            }
        })
    });

    $('#print-pdf').on('click', function (e) {
        var divToPrint = document.getElementsByClassName('reservation-card-first')[0];
        var anotherWindow = window.open('', 'Print-Window');
        anotherWindow.document.open();
        anotherWindow.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');
        anotherWindow.document.close();
        setTimeout(function () {
            anotherWindow.close();
        }, 10);
    });

    $('#cancel-reservation').on('click', function () {
        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'delete_reservation',
                reservation_id: $(this).attr("data-id")
            },
            success: function (response) {
                console.log(response)
                console.log(ajax_object.translations.reservation_deleted); // Překlad
                location.reload();
            },
            error: function (error) {
                alert(ajax_object.translations.error); // Překlad
            }
        })
    });

    function formatDateTime(dateTime) {
        var date = new Date(dateTime);

        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var year = date.getFullYear();
        var hours = ("0" + date.getHours()).slice(-2);
        var minutes = ("0" + date.getMinutes()).slice(-2);

        return day + "." + month + "." + year + ", " + hours + ":" + minutes;
    }

    function getMinutesDifference(dateTime1, dateTime2) {
        var date1 = new Date(dateTime1);
        var date2 = new Date(dateTime2);

        var differenceInMilliseconds = Math.abs(date2 - date1);

        var differenceInMinutes = Math.floor(differenceInMilliseconds / 1000 / 60);

        return differenceInMinutes;
    }

    function getSpecificDate(date) {
        var date = new Date(date);
        var days = ajax_object.translations.days; // Překlad

        var dayName = days[date.getDay()];

        return dayName + ', ' + date.getDate();
    }

    function getFinalTerm(date) {
        var date = new Date(date);

        var days = ajax_object.translations.days; // Překlad
        var months = ajax_object.translations.months; // Překlad

        var dayName = days[date.getDay()];
        var day = date.getDate();
        var monthName = months[date.getMonth()];
        var year = date.getFullYear();
        var hours = date.getHours();
        var minutes = date.getMinutes();

        minutes = minutes < 10 ? '0' + minutes : minutes;

        var formattedDate = dayName + ', ' + day + '. ' + monthName + ' ' + year + ', ' + hours + ':' + minutes;

        return formattedDate;
    }

    function cleanReservationForm() {
        $('#reservation-container').hide();
        $('.final-term').hide();
        $('#reservation-form').html(`
    <div class="col-md-6">
        <label class="form-label" for="first-first_name">` + ajax_object.translations.first_name + `:</label> <!-- Překlad -->
        <input class="form-control" name="res[first_name]" id="first_name"/>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="last_name">` + ajax_object.translations.last_name + `:</label> <!-- Překlad -->
        <input class="form-control" name="res[last_name]" id="last_name"/>
    </div>
    `);
    };
});