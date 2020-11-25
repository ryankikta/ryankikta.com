<?php

class BG_CLIENT
{

    /*
    * HTTP REquest Object
    */
    public $current_link;

    /*
    * User shop id
    */
    public $has_error = false;

    /*
    * user store token
    */
    private $http_request;

    /*
    * RyanKikta Application client id
    */
    private $shop_id;
    private $auth_token;
    private $auth_client = "gck7knt3cv6ugxbe1j9es2d9g7wdk6z";
    private $base_url = "";
    private $errors = array();
    private $raw_json;
    private $data;
    private $next_link;
    private $previous_link;
    private $total;
    private $count;
    private $per_page;
    private $method;
    private $call_url;

    public function __construct($shop_id, $shop_token, $client_token = "", $http_request = NULL)
    {

        // HTTP request Object
        if ($http_request) {

            $this->http_request = $http_request;

        } else {

            if (class_exists('HTTP_Request2')) {

                $this->http_request = new HTTP_Request2();
            } else {

                if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/HTTP/Request2.php')) {

                    require_once($_SERVER["DOCUMENT_ROOT"] . '/HTTP/Request2.php');
                    $this->http_request = new HTTP_Request2();

                }
            }
        }

        // set headers and config
        $this->http_request->setHeader("Accept", "application/json");
        $this->http_request->setHeader("Content-Type", "application/json");
        $this->http_request->setHeader("X-Auth-Token", $shop_token);
        $this->http_request->setHeader("X-Auth-Client", $this->auth_client);
        $this->http_request->setConfig(array(
            'ssl_verify_peer' => FALSE,
            'ssl_verify_host' => FALSE));

        //prepare api url for V3
        $this->shop_id = $shop_id;
        $this->base_url = "https://api.bigcommerce.com/stores/" . $this->shop_id . "/v3";

    }

    /**
     * Get A List of all products
     */
    public function get_products($params = array())
    {

        $path = $this->base_url . '/catalog/products';
        return $this->get($path, $params);


    }

    public function get($url, $params = array())
    {

        return $this->call($url, HTTP_Request2::METHOD_GET, array(), $params);

    }

    public function call($path, $method, $body = array(), $params = array())
    {


        $this->http_request->setUrl($path);
        $this->http_request->setMethod($method);
        $this->call_url = $path;
        if (is_array($params) and !empty($params))
            $this->http_request->setBody(json_encode($body));
        //parse returned data for object and errors
        $return = $this->http_request->send()->getBody();

        $this->raw_json = $return;
        return $this->parse_response($return);


    }

    private function parse_response($json)
    {

        $this->has_error = 0;
        $this->errors = array();
        $this->data = '';
        $data = json_decode($json, true);

        if (is_array($data) and isset($data['data'])) {
            $this->data = $data['data'];
            // pagination links
            $this->total = 0;
            $this->count = 0;
            $this->per_page = 0;
            //$this->per_page = 0;
            $this->next_link = "";
            $this->previous_link = "";
            $this->current_link = "";

            if (isset($data['meta']['pagination'])) {
                $this->total = $data['meta']['pagination']['total'];
                $this->count = $data['meta']['pagination']['count'];
                $this->per_page = $data['meta']['pagination']['per_page'];
                $this->total_pages = $data['meta']['pagination']['total'];
                if (isset($data['meta']['pagination']['links']['next']))
                    $this->next_link = $data['meta']['pagination']['links']['next'];
                if (isset($data['meta']['pagination']['links']['previous']))
                    $this->previous_link = $data['meta']['pagination']['links']['previous'];
                if (isset($data['meta']['pagination']['links']['current']))
                    $this->current_link = $data['meta']['pagination']['links']['current'];
            }
        } else if (is_array($data) and isset($data['status']) and ($data['status'] != 200 and $data['status'] != 201)) {
            $this->has_error = 1;
            foreach ($data['errors'] as $error) {
                $this->errors[] = $error;
            }

        } elseif (!is_array($data)) {
            $this->errors[] = $json;
            $this->has_error = 1;


        }

        if (!$this->has_error)
            return $this->data;

    }

// Orders functions 

    public function get_product($product_id, $params = array())
    {

        $path = $this->base_url . '/catalog/products/' . $product_id;
        return $this->get($path, $params);

    }

    public function create_product($body, $params = array())
    {

        $path = $this->base_url . '/catalog/products';
        return $this->post($path, $body);

    }

    public function post($url, $body, $params = array())
    {

        return $this->call($url, HTTP_Request2::METHOD_POST, $body, $params);


    }

    public function update_product($product_id, $body, $params = array())
    {

        $path = $this->base_url . '/catalog/products/' . $product_id;
        return $this->post($path, $body);

    }

    public function get_orders($params = array())
    {
        $this->base_url = "https://api.bigcommerce.com/stores/" . $this->shop_id . "/v2";
        $path = $this->base_url . '/orders';
        return $this->get($path, $params);


    }

    public function get_order($order_id, $params = array())
    {

        $path = $this->base_url . '/orders/' . $product_id;
        return $this->get($path, $params);

    }

    public function get_response()
    {

        return $this->data;

    }

    public function get_errors()
    {

        return $this->errors;

    }

    public function has_errors()
    {

        return $this->has_error;

    }

    public function delete($url, $params = array())
    {

        return $this->call($url, HTTP_Request2::METHOD_DELETE, array(), $params);


    }

    public function put($url, $bpdy, $params = array())
    {

        return $this->call($url, HTTP_Request2::METHOD_PUT, $body, $params);

    }

}

?>