<?php

class Sysbot {

    public function __construct() {
        $this->domain = 'http://sysbot.pw';
    }

    private function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        }
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        }
        if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        }
        if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        }
        if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        }
        if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        }
        $ipaddress = explode(",", $ipaddress);
        if (preg_match("/::1/", $ipaddress[0])) {
            $ipaddress[0] = '8.8.8.8';
        }
        return $ipaddress[0];
    }

    private function httpGet($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        return $response;
    }

    public function redirect($shortCode) {
        $domain = $this->domain;
        $ipAddress = $this->get_client_ip();
        $userAgent = urlencode($_SERVER['HTTP_USER_AGENT']);
        $url = "$domain/skuy?ipAddress=$ipAddress&shortCode=$shortCode&userAgent=$userAgent";
        $respons = $this->httpGet($url);

        return $respons;
    }

    public function json($respons) {
        return json_decode($respons);
    }

    public function showError($code) {
        $template = file_get_contents('template/error_page.html');
        switch ($code) {
        case 403:
            $template = str_replace("{error_code}", "403", $template);
            $template = str_replace("{error_text}", "Forbidden", $template);
            $template = str_replace("{message}", "You dont have authorization to view this page.", $template);
            echo $template;
            die(header('HTTP/1.0 403 Forbidden'));
        case 404:
            $template = str_replace("{error_code}", "404", $template);
            $template = str_replace("{error_text}", "Not Found", $template);
            $template = str_replace("{message}", "The requested was not found on this server.", $template);
            echo $template;
            die(header('HTTP/1.0 404 Not Found'));
        }
    }
}
