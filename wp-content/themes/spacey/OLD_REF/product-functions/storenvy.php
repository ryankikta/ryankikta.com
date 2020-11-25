<?php

// storenvy php class

class StorenvyClient
{

    private $token;
    private $api_key;
    private $secret;
    private $last_response_headers = null;

    public function __construct($shop_domain, $token, $client_id, $client_secret)
    {
        $this->name = "ShopifyClient";
        $this->shop_domain = $shop_domain;
        $this->token = $token;
        $this->api_key = $client_id;
        $this->secret = $client_secret;
    }

    public function getAuthorizeUrl($scope, $redirect_url = '')
    {
        $url = "http://{$this->shop_domain}/admin/oauth/authorize?client_id={$this->api_key}&scope=" . urlencode($scope);
        if ($redirect_url != '') {
            $url .= "&redirect_uri=" . urlencode($redirect_url);
        }
        return $url;
    }

    // Once the User has authorized the app, call this with the code to get the access token
    public function getAccessToken($code)
    {
        // POST to  POST https://SHOP_NAME.myshopify.com/admin/oauth/access_token
        $url = "https://api.storenvy.com/oauth/token";
        $payload = "client_id={$this->api_key}&client_secret={$this->secret}&code=$code&grant_type=authorization_code&redirecy_uri=https://ryankikta.com/storenvy";
        $response = $this->curlHttpApiRequest('POST', $url, '', $payload, array());
        $response = json_decode($response, true);
        return $response;
    }


    private function curlHttpApiRequest($method, $url, $query = '', $payload = '', $request_headers = array())
    {
        $url = $this->curlAppendQuery($url, $query);
        $ch = curl_init($url);
        $this->curlSetopts($ch, $method, $payload, $request_headers);
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        list($message_headers, $message_body) = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
        $this->last_response_headers = $this->curlParseHeaders($message_headers);

        return $message_body;
    }

    private function curlAppendQuery($url, $query)
    {
        if (empty($query)) return $url;
        if (is_array($query)) return "$url?" . http_build_query($query);
        else return "$url?$query";
    }

    private function curlSetopts($ch, $method, $payload, $request_headers)
    {
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'HAC');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($request_headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        if ($method != 'GET' && !empty($payload)) {
            if (is_array($payload)) $payload = http_build_query($payload);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
    }

    private function curlParseHeaders($message_headers)
    {
        $header_lines = preg_split("/\r\n|\n|\r/", $message_headers);
        $headers = array();
        list(, $headers['http_status_code'], $headers['http_status_message']) = explode(' ', trim(array_shift($header_lines)), 3);
        foreach ($header_lines as $header_line) {
            list($name, $value) = explode(':', $header_line, 2);
            $name = strtolower($name);
            $headers[$name] = trim($value);
        }

        return $headers;
    }

}

?>