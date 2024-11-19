<?php
declare(strict_types=1);

namespace Reservatic;

class Reservatic{

    private $apiToken;
    private $certificate;
    private $certificatePassword;
    private $resUrl;

    public function __construct(string $resUrl, string $apiToken, string $certificate, string $certificatePassword)
    {
        $this->resUrl = $resUrl;
        $this->apiToken = $apiToken;
        $this->certificate = $certificate;
        $this->certificatePassword = $certificatePassword;
    }

    public function getCountries(): string
    {
        return $this->get('/v1/countries');
    }

    public function getInsuranceCompanies(): string
    {
        return $this->get('/v1/insurance_companies');
    }

    public function getPhoneCodes(): string
    {
        return $this->get('/v1/phone_codes');
    }

    public function getServices(int $page = null, int $per_page = null, string $q = '', string $name_cont = ''): string
    {
        $params = [
            'page' => $page,
            'per_page' => $per_page,
            'q' => $q,
            'name_cont' => $name_cont
        ];

        $q = http_build_query($params);

        return $this->get('/v1/services?'.$q);
    }

    public function getService(int $id): string
    {
        return $this->get('/v1/services/'.$id);
    }

    public function getOperations(int $service_id, int $page = null, int $per_page = null): string
    {
        $params = [
            'service_id' => $service_id,
            'page' => $page,
            'per_page' => $per_page
        ];

        $q = http_build_query($params);

        return $this->get('/v1/operations?'.$q);
    }

    public function getHolidays(int $service_id, string $from, string $to, int $user_service_id = null, int $page = null, int $per_page = null): string
    {
        $params = [
            'service_id' => $service_id,
            'from' => $from,
            'to' => $to,
            'user_service_id' => $user_service_id,
            'page' => $page,
            'per_page' => $per_page
        ];
        $q = http_build_query($params);


        return $this->get('/v1/holidays?'.$q);
    }

    public function getServiceYears(int $service_id, int $operation_id, int $place_id = null, int $user_service_id = null): string
    {
        $params = [
            'user_service_id' => $user_service_id,
            'place_id' => $place_id
        ];

        $q = http_build_query($params);

        return $this->get('/v1/services/'.$service_id.'/operations/'.$operation_id.'/years?'.$q);
    }

    public function getServiceMonths(int $service_id, int $operation_id, int $year, int $place_id = null, int $user_service_id = null): string
    {
        $params = [
            'year' => $year,
            'place_id' => $place_id,
            'user_service_id' => $user_service_id
        ];

        $q = http_build_query($params);

        return $this->get('/v1/services/'.$service_id.'/operations/'.$operation_id.'/months?'.$q);
    }

    public function getServiceDays(int $service_id, int $operation_id, int $year, int $month, int $place_id = null, int $user_service_id = null): string
    {
        $params = [
            'year' => $year,
            'month' => $month,
            'place_id' => $place_id,
            'user_service_id' => $user_service_id
        ];

        $q = http_build_query($params);

        return $this->get('/v1/services/'.$service_id.'/operations/'.$operation_id.'/days?'.$q);
    }

    public function getServiceHours(int $service_id, int $operation_id, string $day, int $place_id = null, int $user_service_id = null): string
    {
        $params = [
            'place_id' => $place_id,
            'user_service_id' => $user_service_id
        ];

        $day = date('Y-n-j',strtotime($day));

        $q = http_build_query($params);

        return $this->get('/v1/services/'.$service_id.'/operations/'.$operation_id.'/days/'.$day. '/hours?=' .$q);
    }

    public function getPlaces(int $service_id, int $operation_id = null, int $page = null, int $per_page = null): string
    {
        $params = [
            'service_id' => $service_id,
            'operation_id' => $operation_id,
            'page' => $page,
            'per_page' => $per_page
        ];

        $q = http_build_query($params);

        return $this->get('/v1/places?'. $q);
    }

    public function getReservations(string $from, string $to, string $status = 'kept', int $operation_id = null, int $place_id = null, int $user_service_id = null, int $user_id = null, int $external_app_id = null, string $order_by = 'starts_at', string $order_direction = 'asc', int $page = null, int $per_page = null): string
    {
        $params = [
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'operation_id' => $operation_id,
            'place_id' => $place_id,
            'user_service_id' => $user_service_id,
            'user_id' => $user_id,
            'external_app_id' => $external_app_id,
            'order_by' => $order_by,
            'order_direction' => $order_direction,
            'page' => $page,
            'per_page' => $per_page
        ];

        $q = http_build_query($params);

        return $this->get('/v1/reservations?'. $q);
    }

    public function getServiceClients(int $service_id, string $q = '', string $user_first_name_cont = '', string $user_last_name_cont = '', string $user_email_cont = '', int $page = null, int $per_page = null):string
    {
        $params = [
            'service_id' => $service_id,
            'q' => $q,
            'user_first_name_cont' => $user_first_name_cont,
            'user_last_name_cont' => $user_last_name_cont,
            'user_email_cont' => $user_email_cont,
            'page' => $page,
            'per_page' => $per_page
        ];

        $q = http_build_query($params);

        return $this->get('/v1/service_clients?'. $q);
    }
    public function get($url): bool|string
    {
        $ch = curl_init($this->resUrl . $url);

        $headers =  [
            'accept: application/json',
            'Api-Token: '.$this->apiToken
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSLCERT, $this->certificate);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'P12');
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->certificatePassword);

        $response = curl_exec($ch);

        if (!$response)
            return curl_error($ch);
        else
            return $response;
    }

    public function postHoliday($data)
    {
        return $this->post('/v1/holidays',$data);
    }

    public function postReservation($data)
    {
        return $this->post('/v1/reservations',$data);
    }

    public function post($url, $data): bool|string
    {
        $ch = curl_init($this->resUrl . $url);

        $headers = [
            'accept: application/json',
            'Api-Token: ' . $this->apiToken,
            'Content-Type: application/json',
            'Content-Type: application/json'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_SSLCERT, $this->certificate);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'P12');
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->certificatePassword);

        $response = curl_exec($ch);

        if (!$response) {
            return curl_error($ch);
        } else {
            return $response;
        }
    }

    public function deleteHoliday(int $service_id, int $holiday_id): string
    {
        return $this->delete('/v1/holidays/'.$holiday_id.'?service_id=' . $service_id);
    }

    public function deleteReservation(int $id): string
    {
        return $this->delete('/v1/reservations/' . $id);
    }

    public function delete($url): bool|string
    {
        $ch = curl_init($this->resUrl . $url);

        $headers = [
            'Accept: application/json',
            'Api-Token: '.$this->apiToken
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        curl_setopt($ch, CURLOPT_SSLCERT, $this->certificate);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'P12');
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->certificatePassword);

        $response = curl_exec($ch);

        if(!$response)
            return curl_error($ch);
        else
            return $response;
    }

    public function updateReservationTime(int $id, string $time): string
    {
        return $this->patch('/v1/reservations/'.$id, $time);
    }

    public function patch($url, $time): bool|string
    {
        $ch = curl_init($this->resUrl . $url);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Api-Token: '.$this->apiToken
        ];

        $json = json_encode(['reservation' => ['starts_at' => $this->formatTime($time)]]);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        curl_setopt($ch, CURLOPT_SSLCERT, $this->certificate);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'P12');
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->certificatePassword);

        $response = curl_exec($ch);

        if(!$response)
            return curl_error($ch);
        else
            return $response;
    }

    public function formatTime($time): string
    {
        return date('Y-m-d\TH:i:sP', strtotime($time));
    }
}