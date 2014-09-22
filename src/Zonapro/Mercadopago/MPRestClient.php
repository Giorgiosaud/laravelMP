<?php
/**
 * User: jorgelsaud
 * Date: 21/09/14
 * Time: 16:23
 */

namespace Zonapro\Mercadopago;

/**
 * MercadoPago cURL RestClient
 */
class MPRestClient {

    const API_BASE_URL = "https://api.mercadolibre.com";

    private static function get_connect($uri, $method, $content_type) {
        $connect = curl_init(self::API_BASE_URL . $uri);

        curl_setopt($connect, CURLOPT_USERAGENT, "MercadoPago PHP SDK v" . MP::version);
        curl_setopt($connect, CURLOPT_CAINFO, $GLOBALS["LIB_LOCATION"] . "/cacert.pem");
        curl_setopt($connect, CURLOPT_SSLVERSION, 3);
        curl_setopt($connect, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connect, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($connect, CURLOPT_HTTPHEADER, array("Accept: application/json", "Content-Type: " . $content_type));

        return $connect;
    }

    private static function set_data(&$connect, $data, $content_type) {
        if ($content_type == "application/json") {
            if (gettype($data) == "string") {
                json_decode($data, true);
            } else {
                $data = json_encode($data);
            }

            if(function_exists('json_last_error')) {
                $json_error = json_last_error();
                if ($json_error != JSON_ERROR_NONE) {
                    throw new Exception("JSON Error [{$json_error}] - Data: {$data}");
                }
            }
        }

        curl_setopt($connect, CURLOPT_POSTFIELDS, $data);
    }

    private static function exec($method, $uri, $data, $content_type) {
        $connect = self::get_connect($uri, $method, $content_type);
        if ($data) {
            self::set_data($connect, $data, $content_type);
        }

        $api_result = curl_exec($connect);
        $api_http_code = curl_getinfo($connect, CURLINFO_HTTP_CODE);

        $response = array(
            "status" => $api_http_code,
            "response" => json_decode($api_result, true)
        );

        if ($response['status'] >= 400) {
            throw new Exception ($response['response']['message'], $response['status']);
        }

        curl_close($connect);

        return $response;
    }

    public static function get($uri, $content_type = "application/json") {
        return self::exec("GET", $uri, null, $content_type);
    }

    public static function post($uri, $data, $content_type = "application/json") {
        return self::exec("POST", $uri, $data, $content_type);
    }

    public static function put($uri, $data, $content_type = "application/json") {
        return self::exec("PUT", $uri, $data, $content_type);
    }

}