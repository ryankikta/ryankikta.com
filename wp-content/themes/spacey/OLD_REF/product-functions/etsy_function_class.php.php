<?php
require_once("../wp-load.php");

function replace_between($str, $needle_start, $needle_end, $replacement)
{
    $pos = strpos($str, $needle_start);
    $start = $pos === false ? 0 : $pos + strlen($needle_start);

    $pos = strpos($str, $needle_end, $start);
    $end = $pos === false ? strlen($str) : $pos;

    return substr_replace($str, $replacement, $start, $end - $start);
}


function get_content_from_url($post = array())
{

    $ch = curl_init(site_url() . "/etsy-remote-install");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $return = curl_exec($ch);
    $data = object_to_array(json_decode($return));
    curl_close($ch);
    return $data;
}


/*
 * OAUTH CLASS
 *
 */


class oauth_client_class
{

    var $error = '';
    var $debug = false;
    var $debug_http = false;
    var $exit = false;
    var $debug_output = '';
    var $debug_prefix = 'OAuth client: ';
    var $server = '';
    var $configuration_file = 'oauth_configuration.json';
    var $request_token_url = '';
    var $dialog_url = '';
    var $pin_dialog_url = '';
    var $offline_dialog_url = '';
    var $pin = '';
    var $append_state_to_redirect_uri = '';
    var $access_token_url = '';
    var $oauth_version = '2.0';
    var $url_parameters = false;
    var $authorization_header = true;
    var $token_request_method = 'GET';
    var $signature_method = 'HMAC-SHA1';
    var $redirect_uri = '';
    var $client_id = '';
    var $client_secret = '';
    var $api_key = '';
    var $get_token_with_api_key = false;
    var $scope = '';
    var $offline = false;
    var $access_token = '';
    var $access_token_secret = '';
    var $access_token_expiry = '';
    var $access_token_type = '';
    var $default_access_token_type = '';
    var $access_token_parameter = '';
    var $access_token_response;
    var $store_access_token_response = false;
    var $access_token_authentication = '';
    var $refresh_token = '';
    var $access_token_error = '';
    var $authorization_error = '';
    var $response_status = 0;
    var $oauth_username = '';
    var $oauth_password = '';
    var $grant_type = "authorization_code";
    var $http_arguments = array();
    var $oauth_user_agent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:35.0) Gecko/20100101 Firefox/35.0';
    var $response_time = 0;
    var $raw_error = "";
    var $data = null;
    var $authorization_header_content = "";

    function getAuthorization()
    {
        return $this->authorization_header_content;
    }

    function getData()
    {
        return $this->data;
    }

    function setData($data)
    {
        $this->data = $data;
    }

    function getRawError()
    {
        return $this->raw_error;
    }

    function setRawError($error)
    {
        $this->raw_error = $error;
    }

    Function GetStoredState(&$state)
    {
        if (!function_exists('session_start'))
            return $this->SetError('Session variables are not accessible in this PHP environment');
        if (session_id() === ''
            && !session_start())
            return ($this->SetPHPError('it was not possible to start the PHP session', $php_errormsg));
        if (IsSet($_SESSION['OAUTH_STATE']))
            $state = $_SESSION['OAUTH_STATE'];
        else
            $state = $_SESSION['OAUTH_STATE'] = time() . '-' . substr(md5(rand() . time()), 0, 6);
        return (true);
    }

    Function SetError($error)
    {
        $this->error = $error;
        if ($this->debug)
            $this->OutputDebug('Error: ' . $error);
        return (false);
    }

    Function OutputDebug($message)
    {
        if ($this->debug) {
            $message = $this->debug_prefix . $message;
            $this->debug_output .= $message . "\n";;
            error_log($message);
        }
        return (true);
    }

    Function SetPHPError($error, &$php_error_message)
    {
        if (IsSet($php_error_message)
            && strlen($php_error_message))
            $error .= ": " . $php_error_message;
        return ($this->SetError($error));
    }

    Function GetRequestState(&$state)
    {
        $check = (strlen($this->append_state_to_redirect_uri) ? $this->append_state_to_redirect_uri : 'state');
        $state = (IsSet($_GET[$check]) ? $_GET[$check] : null);
        return (true);
    }

    Function GetRequestCode(&$code)
    {
        $code = (IsSet($_GET['code']) ? $_GET['code'] : null);
        return (true);
    }

    Function GetRequestError(&$error)
    {
        return ((IsSet($_GET['error']) ? $_GET['error'] : null));

    }

    Function ResetAccessToken()
    {
        if (!$this->GetAccessTokenURL($access_token_url))
            return false;
        if ($this->debug)
            $this->OutputDebug('Resetting the access token status for OAuth server located at ' . $access_token_url);
        if (!function_exists('session_start'))
            return $this->SetError('Session variables are not accessible in this PHP environment');
        if (session_id() === ''
            && !session_start())
            return ($this->SetPHPError('it was not possible to start the PHP session', $php_errormsg));
        Unset($_SESSION['OAUTH_ACCESS_TOKEN'][$access_token_url]);
        UnSet($_SESSION['OAUTH_STATE']);
        return true;
    }

    Function GetAccessTokenURL(&$access_token_url)
    {
        $access_token_url = str_replace('{API_KEY}', $this->api_key, $this->access_token_url);
        return (true);
    }

    Function ProcessToken2($code, $refresh)
    {
        if (!$this->GetRedirectURI($redirect_uri))
            return false;
        $authentication = $this->access_token_authentication;
        if (strlen($this->oauth_username)) {
            $values = array(
                'grant_type' => 'password',
                'username' => $this->oauth_username,
                'password' => $this->oauth_password,
                'redirect_uri' => $redirect_uri
            );
            $authentication = 'Basic';
        } elseif ($this->redirect_uri === 'oob'
            && strlen($this->pin)) {
            $values = array(
                'grant_type' => 'pin',
                'pin' => $this->pin,
                'scope' => $this->scope,
            );
        } elseif ($refresh) {
            $values = array(
                'refresh_token' => $this->refresh_token,
                'grant_type' => 'refresh_token',
                'scope' => $this->scope,
            );
        } else {
            switch ($this->grant_type) {
                case 'password':
                    return $this->SetError('it was not specified the username for obtaining a password based OAuth 2 authorization');
                case 'authorization_code':
                    $values = array(
                        'code' => $code,
                        'redirect_uri' => $redirect_uri,
                        'grant_type' => 'authorization_code'
                    );
                    break;
                case 'client_credentials':
                    $values = array(
                        'grant_type' => 'client_credentials'
                    );
                    $authentication = 'Basic';
                    break;
                default:
                    return $this->SetError($this->grant_type . ' is not yet a supported OAuth 2 grant type');
            }
        }
        $options = array(
            'Resource' => 'OAuth ' . ($refresh ? 'refresh' : 'access') . ' token',
            'ConvertObjects' => true
        );
        switch (strtolower($authentication)) {
            case 'basic':
                $options['AccessTokenAuthentication'] = $authentication;
                break;
            case '':
                $values['client_id'] = $this->client_id;
                $values['client_secret'] = ($this->get_token_with_api_key ? $this->api_key : $this->client_secret);
                break;
            default:
                return ($this->SetError($authentication . ' is not a supported authentication mechanism to retrieve an access token'));
        }
        if (!$this->GetAccessTokenURL($access_token_url))
            return false;
        if (!$this->SendAPIRequest($access_token_url, 'POST', $values, null, $options, $response))
            return false;
        if (strlen($this->access_token_error)) {
            $this->authorization_error = $this->access_token_error;
            return true;
        }
        if (!IsSet($response['access_token'])) {
            if (IsSet($response['error'])) {
                $this->authorization_error = 'it was not possible to retrieve the access token: it was returned the error: ' . $response['error'];
                return true;
            }
            return ($this->SetError('OAuth server did not return the access token'));
        }
        $access_token = array(
            'value' => ($this->access_token = $response['access_token']),
            'authorized' => true,
        );
        if ($this->store_access_token_response)
            $access_token['response'] = $this->access_token_response = $response;
        if ($this->debug)
            $this->OutputDebug('Access token: ' . $this->access_token);
        if (IsSet($response['expires_in'])
            && $response['expires_in'] == 0) {
            if ($this->debug)
                $this->OutputDebug('Ignoring access token expiry set to 0');
            $this->access_token_expiry = '';
        } elseif (IsSet($response['expires'])
            || IsSet($response['expires_in'])) {
            $expires = (IsSet($response['expires_in']) ? $response['expires_in'] : $response['expires'] - ($response['expires'] > $this->response_time ? $this->response_time : 0));
            if (strval($expires) !== strval(intval($expires))
                || $expires <= 0)
                return ($this->SetError('OAuth server did not return a supported type of access token expiry time'));
            $this->access_token_expiry = gmstrftime('%Y-%m-%d %H:%M:%S', $this->response_time + $expires);
            if ($this->debug)
                $this->OutputDebug('Access token expiry: ' . $this->access_token_expiry . ' UTC');
            $access_token['expiry'] = $this->access_token_expiry;
        } else
            $this->access_token_expiry = '';
        if (IsSet($response['token_type'])) {
            $this->access_token_type = $response['token_type'];
            if (strlen($this->access_token_type)
                && $this->debug)
                $this->OutputDebug('Access token type: ' . $this->access_token_type);
            $access_token['type'] = $this->access_token_type;
        } else {
            $this->access_token_type = $this->default_access_token_type;
            if (strlen($this->access_token_type)
                && $this->debug)
                $this->OutputDebug('Assumed the default for OAuth access token type which is ' . $this->access_token_type);
        }
        if (IsSet($response['refresh_token'])) {
            $this->refresh_token = $response['refresh_token'];
            if ($this->debug)
                $this->OutputDebug('Refresh token: ' . $this->refresh_token);
            $access_token['refresh'] = $this->refresh_token;
        } elseif (strlen($this->refresh_token)) {
            if ($this->debug)
                $this->OutputDebug('Reusing previous refresh token: ' . $this->refresh_token);
            $access_token['refresh'] = $this->refresh_token;
        }
        return $this->StoreAccessToken($access_token);
    }

    Function GetRedirectURI(&$redirect_uri)
    {
        if (strlen($this->redirect_uri))
            $redirect_uri = $this->redirect_uri;
        else
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return true;
    }

    Function SendAPIRequest($url, $method, $parameters, $oauth, $options, &$response)
    {

        $this->response_status = 0;
        $http = new http_class;
        $http->debug = ($this->debug && $this->debug_http);
        $http->log_debug = true;
        $http->sasl_authenticate = 0;
        $http->user_agent = $this->oauth_user_agent;
        $http->redirection_limit = (IsSet($options['FollowRedirection']) ? intval($options['FollowRedirection']) : 0);
        $http->follow_redirect = ($http->redirection_limit != 0);
        if ($this->debug)
            $this->OutputDebug('Accessing the ' . $options['Resource'] . ' at ' . $url);
        $post_files = array();
        $method = strtoupper($method);
        $authorization = '';
        $request_content_type = (IsSet($options['RequestContentType']) ? strtolower(trim(strtok($options['RequestContentType'], ';'))) : (($method === 'POST' || IsSet($oauth)) ? 'application/x-www-form-urlencoded' : ''));
        $files = (IsSet($options['Files']) ? $options['Files'] : array());
        if (count($files)) {
            foreach ($files as $name => $value) {

                if (!IsSet($parameters[$name]))
                    return ($this->SetError('it was specified an file parameters named ' . $name));
                $file = array();
                switch (IsSet($value['Type']) ? $value['Type'] : 'FileName') {
                    case 'FileName':
                        $file['FileName'] = $parameters[$name];
                        break;
                    case 'Data':
                        $file['Data'] = $parameters[$name];
                        break;
                    default:
                        return ($this->SetError($value['Type'] . ' is not a valid type for file ' . $name));
                }
                $file['Content-Type'] = (IsSet($value['ContentType']) ? $value['ContentType'] : 'automatic/name');
                $post_files[$name] = $file;
            }
            UnSet($parameters[$name]);
            if ($method !== 'POST') {
                $this->OutputDebug('For uploading files the method should be POST not ' . $method);
                $method = 'POST';
            }
            if ($request_content_type !== 'multipart/form-data') {
                if (IsSet($options['RequestContentType']))
                    return ($this->SetError('the request content type for uploading files should be multipart/form-data'));
                $request_content_type = 'multipart/form-data';
            }
        }
        if (IsSet($oauth)) {
            if (!$this->Sign($url, $method, $parameters, $oauth, $request_content_type, count($files) !== 0, IsSet($options['PostValuesInURI']) && $options['PostValuesInURI'], $authorization, $post_values))
                return false;
        } else {
            $post_values = $parameters;
            if (count($parameters)) {
                switch ($request_content_type) {
                    case 'application/x-www-form-urlencoded':
                    case 'multipart/form-data':
                    case 'application/json':
                        break;
                    default:
                        $first = (strpos($url, '?') === false);
                        foreach ($parameters as $name => $value) {
                            if (GetType($value) === 'array') {
                                foreach ($value as $index => $value) {
                                    $url .= ($first ? '?' : '&') . $name . '=' . UrlEncode($value);
                                    $first = false;
                                }
                            } else {
                                $url .= ($first ? '?' : '&') . $name . '=' . UrlEncode($value);
                                $first = false;
                            }
                        }
                }
            }
        }
        if (strlen($authorization) === 0
            && !strcasecmp($this->access_token_type, 'Bearer'))
            $authorization = 'Bearer ' . $this->access_token;
        if (strlen($error = $http->GetRequestArguments($url, $arguments)))
            return ($this->SetError('it was not possible to open the ' . $options['Resource'] . ' URL: ' . $error));
        $arguments = array_merge($this->http_arguments, $arguments);
        if (strlen($error = $http->Open($arguments)))
            return ($this->SetError('it was not possible to open the ' . $options['Resource'] . ' URL: ' . $error));
        if (count($post_files))
            $arguments['PostFiles'] = $post_files;
        $arguments['RequestMethod'] = $method;
        switch ($request_content_type) {
            case 'application/x-www-form-urlencoded':
            case 'multipart/form-data':
                if (IsSet($options['RequestBody']))
                    return ($this->SetError('the request body is defined automatically from the parameters'));
                $arguments['PostValues'] = $post_values;
                break;
            case 'application/json':
                $arguments['Headers']['Content-Type'] = $options['RequestContentType'];
                $arguments['Body'] = (IsSet($options['RequestBody']) ? $options['RequestBody'] : json_encode($parameters));
                break;
            default:
                if (!IsSet($options['RequestBody'])) {
                    if (IsSet($options['RequestContentType']))
                        return ($this->SetError('it was not specified the body value of the of the API call request'));
                    break;
                }
                $arguments['Headers']['Content-Type'] = $options['RequestContentType'];
                $arguments['Body'] = $options['RequestBody'];
                break;
        }
        $arguments['Headers']['Accept'] = (IsSet($options['Accept']) ? $options['Accept'] : '*/*');
        switch ($authentication = (IsSet($options['AccessTokenAuthentication']) ? strtolower($options['AccessTokenAuthentication']) : '')) {
            case 'basic':
                $arguments['Headers']['Authorization'] = 'Basic ' . base64_encode($this->client_id . ':' . ($this->get_token_with_api_key ? $this->api_key : $this->client_secret));
                break;
            case '':
                if (strlen($authorization))
                    $arguments['Headers']['Authorization'] = $authorization;
                break;
            default:
                return ($this->SetError($authentication . ' is not a supported authentication mechanism to retrieve an access token'));
        }
        if (IsSet($options['RequestHeaders']))
            $arguments['Headers'] = array_merge($arguments['Headers'], $options['RequestHeaders']);
        if (strlen($error = $http->SendRequest($arguments))
            || strlen($error = $http->ReadReplyHeaders($headers))) {
            $this->setRawError($error);
            $http->Close();
            return ($this->SetError('it was not possible to retrieve the ' . $options['Resource'] . ': ' . $error));
        }
        $error = $http->ReadWholeReplyBody($data);

        $http->Close();
        if (strlen($error)) {
            $this->setRawError($error);
            return ($this->SetError('it was not possible to access the ' . $options['Resource'] . ': ' . $error));
        }
        $this->response_status = intval($http->response_status);
        $content_type = (IsSet($options['ResponseContentType']) ? $options['ResponseContentType'] : (IsSet($headers['content-type']) ? strtolower(trim(strtok($headers['content-type'], ';'))) : 'unspecified'));
        $content_type = preg_replace('/^(.+\\/).+\\+(.+)$/', '\\1\\2', $content_type);
        $this->response_time = (IsSet($headers['date']) ? strtotime($headers['date']) : time());
        switch ($content_type) {
            case 'text/javascript':
            case 'application/json':
                if (!function_exists('json_decode'))
                    return ($this->SetError('the JSON extension is not available in this PHP setup'));
                $object = json_decode($data);
                switch (GetType($object)) {
                    case 'object':
                        if (!IsSet($options['ConvertObjects'])
                            || !$options['ConvertObjects'])
                            $response = $object;
                        else {
                            $response = array();
                            foreach ($object as $property => $value)
                                $response[$property] = $value;
                        }
                        break;
                    case 'array':
                        $response = $object;
                        $this->setData($response);
                        break;
                    default:
                        if (!IsSet($object))
                            return ($this->SetError('it was not returned a valid JSON definition of the ' . $options['Resource'] . ' values'));
                        $response = $object;
                        $this->setData($response);
                        break;
                }
                break;
            case 'application/x-www-form-urlencoded':
            case 'text/plain':
            case 'text/html':
                parse_str($data, $response);
                $this->setData($response);
                break;
            case 'text/xml':
                if (IsSet($options['DecodeXMLResponse'])) {
                    switch (strtolower($options['DecodeXMLResponse'])) {
                        case 'simplexml':
                            if ($this->debug)
                                $this->OutputDebug('Decoding XML response with simplexml');
                            try {
                                $response = @new SimpleXMLElement($data);
                                $this->setData($response);
                            } catch (Exception $exception) {
                                return $this->SetError('Could not parse XML response: ' . $exception->getMessage());
                            }
                            break 2;
                        default:
                            return $this->SetError($options['DecodeXML'] . ' is not a supported method to decode XML responses');
                    }
                }
            default:
                $response = $data;
                $this->setData($response);
                break;
        }
        if ($this->response_status >= 200
            && $this->response_status < 300)
            $this->access_token_error = '';
        else {
            $this->setRawError($data);
            $this->access_token_error = 'it was not possible to access the ' . $options['Resource'] . ': it was returned an unexpected response status ' . $http->response_status . ' Response: ' . $data;
            if ($this->debug)
                $this->OutputDebug('Could not retrieve the OAuth access token. Error: ' . $this->access_token_error);
            if (IsSet($options['FailOnAccessError'])
                && $options['FailOnAccessError']) {
                $this->error = $this->access_token_error;
                return false;
            }
        }

        return true;
    }

    Function Sign(&$url, $method, $parameters, $oauth, $request_content_type, $has_files, $post_values_in_uri, &$authorization, &$post_values)
    {
        $values = array(
            'oauth_consumer_key' => $this->client_id,
            'oauth_nonce' => md5(uniqid(rand(), true)),
            'oauth_signature_method' => $this->signature_method,
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
        );
        if ($has_files)
            $value_parameters = array();
        else {
            if (($this->url_parameters
                    || $method !== 'POST')
                && $request_content_type === 'application/x-www-form-urlencoded'
                && count($parameters)) {
                $first = (strpos($url, '?') === false);
                foreach ($parameters as $parameter => $value) {
                    $url .= ($first ? '?' : '&') . UrlEncode($parameter) . '=' . UrlEncode($value);
                    $first = false;
                }
                $parameters = array();
            }
            $value_parameters = (($request_content_type !== 'application/x-www-form-urlencoded') ? array() : $parameters);
        }
        $header_values = ($method === 'GET' ? array_merge($values, $oauth, $value_parameters) : array_merge($values, $oauth));
        $values = array_merge($values, $oauth, $value_parameters);
        $key = $this->Encode($this->client_secret) . '&' . $this->Encode($this->access_token_secret);
        switch ($this->signature_method) {
            case 'PLAINTEXT':
                $values['oauth_signature'] = $key;
                break;
            case 'HMAC-SHA1':
                $uri = strtok($url, '?');
                $sign = $method . '&' . $this->Encode($uri) . '&';
                $first = true;
                $sign_values = $values;
                $u = parse_url($url);
                if (IsSet($u['query'])) {
                    parse_str($u['query'], $q);
                    foreach ($q as $parameter => $value)
                        $sign_values[$parameter] = $value;
                }
                KSort($sign_values);
                foreach ($sign_values as $parameter => $value) {
                    $sign .= $this->Encode(($first ? '' : '&') . $parameter . '=' . $this->Encode($value));
                    $first = false;
                }
                $header_values['oauth_signature'] = $values['oauth_signature'] = base64_encode($this->HMAC('sha1', $sign, $key));
                break;
            default:
                return $this->SetError($this->signature_method . ' signature method is not yet supported');
        }
        if ($this->authorization_header) {
            $authorization = 'OAuth';
            $first = true;
            foreach ($header_values as $parameter => $value) {
                $authorization .= ($first ? ' ' : ',') . $parameter . '="' . $this->Encode($value) . '"';
                $first = false;
            }
            $post_values = $parameters;
        } else {
            if ($method !== 'POST'
                || $post_values_in_uri) {
                $first = (strcspn($url, '?') == strlen($url));
                foreach ($values as $parameter => $value) {
                    $url .= ($first ? '?' : '&') . $parameter . '=' . $this->Encode($value);
                    $first = false;
                }
                $post_values = array();
            } else
                $post_values = $values;
        }

        $this->setAuthorization($authorization);
        return true;
    }

    Function Encode($value)
    {
        return (is_array($value) ? $this->EncodeArray($value) : str_replace('%7E', '~', str_replace('+', ' ', RawURLEncode($value))));
    }

    Function EncodeArray($array)
    {
        foreach ($array as $key => $value)
            $array[$key] = $this->Encode($value);
        return $array;
    }

    Function HMAC($function, $data, $key)
    {
        switch ($function) {
            case 'sha1':
                $pack = 'H40';
                break;
            default:
                if ($this->debug)
                    $this->OutputDebug($function . ' is not a supported an HMAC hash type');
                return ('');
        }
        if (strlen($key) > 64)
            $key = pack($pack, $function($key));
        if (strlen($key) < 64)
            $key = str_pad($key, 64, "\0");
        return (pack($pack, $function((str_repeat("\x5c", 64) ^ $key) . pack($pack, $function((str_repeat("\x36", 64) ^ $key) . $data)))));
    }

    function setAuthorization($authorization)
    {
        $this->authorization_header_content = $authorization;
    }

    Function StoreAccessToken($access_token)
    {
        if (!function_exists('session_start'))
            return $this->SetError('Session variables are not accessible in this PHP environment');
        if (session_id() === ''
            && !session_start())
            return ($this->SetPHPError('it was not possible to start the PHP session', $php_errormsg));
        if (!$this->GetAccessTokenURL($access_token_url))
            return false;
        $_SESSION['OAUTH_ACCESS_TOKEN'][$access_token_url] = $access_token;
        return true;
    }

    Function CallAPI($url, $method, $parameters, $options, &$response)
    {

        if (!IsSet($options['Resource']))
            $options['Resource'] = 'API call';
        if (!IsSet($options['ConvertObjects']))
            $options['ConvertObjects'] = false;
        if (strlen($this->access_token) === 0) {
            if (!$this->RetrieveToken($valid))
                return false;
            if (!$valid)
                return $this->SetError('the access token is not set to a valid value');
        }
        switch (intval($this->oauth_version)) {
            case 1:
                if (strlen($this->access_token_expiry) && strcmp($this->access_token_expiry, gmstrftime('%Y-%m-%d %H:%M:%S')) <= 0) {
                    if (strlen($this->refresh_token) === 0)
                        return ($this->SetError('the access token expired and no refresh token is available'));
                    if ($this->debug)
                        $this->OutputDebug('Refreshing the OAuth access token expired on ' . $this->access_token_expiry);
                    $oauth = array(
                        'oauth_token' => $this->access_token,
                        'oauth_session_handle' => $this->refresh_token
                    );
                    if (!$this->ProcessToken1($oauth, $access_token))
                        return false;
                    if (IsSet($options['FailOnAccessError']) && $options['FailOnAccessError'] && strlen($this->authorization_error)) {
                        $this->error = $this->authorization_error;
                        return false;
                    }
                    if (!IsSet($access_token['authorized'])
                        || !$access_token['authorized'])
                        return ($this->SetError('failed to obtain a renewed the expired access token'));
                    $this->access_token = $access_token['value'];
                    $this->access_token_secret = $access_token['secret'];
                    if (IsSet($access_token['refresh']))
                        $this->refresh_token = $access_token['refresh'];
                }
                $oauth = array(
                    (strlen($this->access_token_parameter) ? $this->access_token_parameter : 'oauth_token') => ((IsSet($options['2Legged']) && $options['2Legged']) ? '' : $this->access_token)
                );
                break;

            default:
                return ($this->SetError($this->oauth_version . ' is not a supported version of the OAuth protocol'));
        }

        return ($this->SendAPIRequest($url, $method, $parameters, $oauth, $options, $response));

    }

    Function RetrieveToken(&$valid)
    {
        $valid = false;
        if (!$this->GetAccessToken($access_token))
            return false;
        if (IsSet($access_token['value'])) {
            $this->access_token_expiry = '';
            $expired = (IsSet($access_token['expiry']) && strcmp($this->access_token_expiry = $access_token['expiry'], gmstrftime('%Y-%m-%d %H:%M:%S')) < 0);
            if ($expired) {
                if ($this->debug)
                    $this->OutputDebug('The OAuth access token expired on ' . $this->access_token_expiry . ' UTC');
            }
            $this->access_token = $access_token['value'];
            if (!$expired
                && $this->debug)
                $this->OutputDebug('The OAuth access token ' . $this->access_token . ' is valid');
            if (IsSet($access_token['type'])) {
                $this->access_token_type = $access_token['type'];
                if (strlen($this->access_token_type)
                    && !$expired
                    && $this->debug)
                    $this->OutputDebug('The OAuth access token is of type ' . $this->access_token_type);
            } else {
                $this->access_token_type = $this->default_access_token_type;
                if (strlen($this->access_token_type)
                    && !$expired
                    && $this->debug)
                    $this->OutputDebug('Assumed the default for OAuth access token type which is ' . $this->access_token_type);
            }
            if (IsSet($access_token['secret'])) {
                $this->access_token_secret = $access_token['secret'];
                if ($this->debug
                    && !$expired
                    && strlen($this->access_token_secret))
                    $this->OutputDebug('The OAuth access token secret is ' . $this->access_token_secret);
            }
            if (IsSet($access_token['refresh']))
                $this->refresh_token = $access_token['refresh'];
            else
                $this->refresh_token = '';
            $this->access_token_response = (($this->store_access_token_response && IsSet($access_token['response'])) ? $access_token['response'] : null);
            $valid = true;
        }
        return true;
    }

    Function GetAccessToken(&$access_token)
    {
        if (!function_exists('session_start'))
            return $this->SetError('Session variables are not accessible in this PHP environment');
        if (session_id() === ''
            && !session_start())
            return ($this->SetPHPError('it was not possible to start the PHP session', $php_errormsg));
        if (!$this->GetAccessTokenURL($access_token_url))
            return false;
        if (IsSet($_SESSION['OAUTH_ACCESS_TOKEN'][$access_token_url]))
            $access_token = $_SESSION['OAUTH_ACCESS_TOKEN'][$access_token_url];
        else
            $access_token = array();
        return true;
    }

    Function ProcessToken1($oauth, &$access_token)
    {
        if (!$this->GetAccessTokenURL($url))
            return false;
        $options = array('Resource' => 'OAuth access token');
        $method = strtoupper($this->token_request_method);
        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                $options['PostValuesInURI'] = true;
                break;
            default:
                $this->error = $method . ' is not a supported method to request tokens';
                return false;
        }
        if (!$this->SendAPIRequest($url, $method, array(), $oauth, $options, $response))
            return false;
        if (strlen($this->access_token_error)) {
            $this->authorization_error = $this->access_token_error;
            return true;
        }
        if (!IsSet($response['oauth_token'])
            || !IsSet($response['oauth_token_secret'])) {
            $this->authorization_error = 'it was not returned the access token and secret';
            return true;
        }
        $access_token = array(
            'value' => $response['oauth_token'],
            'secret' => $response['oauth_token_secret'],
            'authorized' => true
        );
        if (IsSet($response['oauth_expires_in'])
            && $response['oauth_expires_in'] == 0) {
            if ($this->debug)
                $this->OutputDebug('Ignoring access token expiry set to 0');
            $this->access_token_expiry = '';
        } elseif (IsSet($response['oauth_expires_in'])) {
            $expires = $response['oauth_expires_in'];
            if (strval($expires) !== strval(intval($expires))
                || $expires <= 0)
                return ($this->SetError('OAuth server did not return a supported type of access token expiry time'));
            $this->access_token_expiry = gmstrftime('%Y-%m-%d %H:%M:%S', $this->response_time + $expires);
            if ($this->debug)
                $this->OutputDebug('Access token expiry: ' . $this->access_token_expiry . ' UTC');
            $access_token['expiry'] = $this->access_token_expiry;
        } else
            $this->access_token_expiry = '';
        if (IsSet($response['oauth_session_handle'])) {
            $access_token['refresh'] = $response['oauth_session_handle'];
            if ($this->debug)
                $this->OutputDebug('Refresh token: ' . $access_token['refresh']);
        }
        return $this->StoreAccessToken($access_token);
    }

    Function Initialize()
    {
        if (strlen($this->server) === 0)
            return true;
        $this->oauth_version =
        $this->dialog_url =
        $this->pin_dialog_url =
        $this->access_token_url =
        $this->request_token_url =
        $this->append_state_to_redirect_uri = '';
        $this->authorization_header = true;
        $this->url_parameters = false;
        $this->token_request_method = 'GET';
        $this->signature_method = 'HMAC-SHA1';
        $this->access_token_authentication = '';
        $this->access_token_parameter = '';
        $this->default_access_token_type = '';
        $this->store_access_token_response = false;
        $this->oauth_version = "1.0a";
        $this->request_token_url = "https://openapi.etsy.com/v2/oauth/request_token";
        $this->dialog_url = "automatic";
        $this->access_token_url = "https://openapi.etsy.com/v2/oauth/access_token";

        return (true);
    }

    Function Process($type = 1)
    {

        switch (intval($this->oauth_version)) {
            case 1:
                $one_a = ($this->oauth_version === '1.0a');
                if ($this->debug)
                    $this->OutputDebug('Checking the OAuth token authorization state');
                if (!$this->GetAccessToken($access_token))
                    return false;

                if (IsSet($access_token['expiry']))
                    $this->access_token_expiry = $access_token['expiry'];
                if (IsSet($access_token['authorized'])
                    && IsSet($access_token['value']) && $type == 1) {
                    $expired = (IsSet($access_token['expiry']) && strcmp($access_token['expiry'], gmstrftime('%Y-%m-%d %H:%M:%S')) <= 0);
                    if (!$access_token['authorized']
                        || $expired) {
                        if ($this->debug) {
                            if ($expired)
                                $this->OutputDebug('The OAuth token expired on ' . $access_token['expiry'] . 'UTC');
                            else
                                $this->OutputDebug('The OAuth token is not yet authorized');
                        }
                        if ($one_a
                            && $this->redirect_uri === 'oob'
                            && strlen($this->pin)) {
                            if ($this->debug)
                                $this->OutputDebug('Checking the pin');
                            $this->access_token_secret = $access_token['secret'];
                            $oauth = array(
                                'oauth_token' => $access_token['value'],
                                'oauth_verifier' => $this->pin
                            );
                            if (!$this->ProcessToken1($oauth, $access_token))
                                return false;
                            if ($this->debug)
                                $this->OutputDebug('The OAuth token was authorized');
                        } else {
                            if ($this->debug)
                                $this->OutputDebug('Checking the OAuth token and verifier');
                            if (!$this->GetRequestToken($token, $verifier))
                                return false;
                            if (!IsSet($token)
                                || ($one_a
                                    && !IsSet($verifier))) {
                                if (!$this->GetRequestDenied($denied))
                                    return false;
                                if (IsSet($denied)
                                    && $denied === $access_token['value']) {
                                    if ($this->debug)
                                        $this->OutputDebug('The authorization request was denied');
                                    $this->authorization_error = 'the request was denied';
                                    return true;
                                } else {
                                    if ($this->debug)
                                        $this->OutputDebug('Reset the OAuth token state because token and verifier are not both set');
                                    $access_token = array();
                                }
                            } elseif ($token !== $access_token['value']) {
                                if ($this->debug)
                                    $this->OutputDebug('Reset the OAuth token state because token does not match what as previously retrieved');
                                $access_token = array();
                            } else {
                                $this->access_token_secret = $access_token['secret'];
                                $oauth = array(
                                    'oauth_token' => $token,
                                );
                                if ($one_a)
                                    $oauth['oauth_verifier'] = $verifier;
                                if (!$this->ProcessToken1($oauth, $access_token))
                                    return false;
                                if ($this->debug)
                                    $this->OutputDebug('The OAuth token was authorized');
                            }
                        }
                    } elseif ($this->debug)
                        $this->OutputDebug('The OAuth token was already authorized');
                    if (IsSet($access_token['authorized'])
                        && $access_token['authorized']) {
                        $this->access_token = $access_token['value'];
                        $this->access_token_secret = $access_token['secret'];
                        if (IsSet($access_token['refresh']))
                            $this->refresh_token = $access_token['refresh'];
                        return true;
                    }
                } else {
                    if ($this->debug)
                        $this->OutputDebug('The OAuth access token is not set');
                    $access_token = array();
                }
                if (!IsSet($access_token['authorized'])) {
                    if ($this->debug)
                        $this->OutputDebug('Requesting the unauthorized OAuth token');
                    if (!$this->GetRequestTokenURL($url))
                        return false;
                    $url = str_replace('{SCOPE}', UrlEncode($this->scope), $url);
                    if (!$this->GetRedirectURI($redirect_uri))
                        return false;
                    $oauth = array(
                        'oauth_callback' => $redirect_uri,
                    );
                    $options = array(
                        'Resource' => 'OAuth request token',
                        'FailOnAccessError' => true
                    );
                    $method = strtoupper($this->token_request_method);
                    switch ($method) {
                        case 'GET':
                            break;
                        case 'POST':
                            $options['PostValuesInURI'] = true;
                            break;
                        default:
                            $this->error = $method . ' is not a supported method to request tokens';
                            break;
                    }
                    if (!$this->SendAPIRequest($url, $method, array(), $oauth, $options, $response))
                        return false;
                    if (strlen($this->access_token_error)) {
                        $this->authorization_error = $this->access_token_error;
                        return true;
                    }
                    if (!IsSet($response['oauth_token'])
                        || !IsSet($response['oauth_token_secret'])) {
                        $this->authorization_error = 'it was not returned the requested token';
                        return true;
                    }
                    $access_token = array(
                        'value' => $response['oauth_token'],
                        'secret' => $response['oauth_token_secret'],
                        'authorized' => false
                    );
                    if (IsSet($response['login_url']))
                        $access_token['login_url'] = $response['login_url'];
                    if (!$this->StoreAccessToken($access_token))
                        return false;
                }
                if (!$this->GetDialogURL($url))
                    return false;
                if ($url === 'automatic') {
                    if (!IsSet($access_token['login_url']))
                        return ($this->SetError('The request token response did not automatically the login dialog URL as expected'));
                    if ($this->debug)
                        $this->OutputDebug('Dialog URL obtained automatically from the request token response: ' . $url);
                    $url = $access_token['login_url'];
                } else
                    $url .= (strpos($url, '?') === false ? '?' : '&') . 'oauth_token=' . $access_token['value'];
                if (!$one_a) {
                    if (!$this->GetRedirectURI($redirect_uri))
                        return false;
                    $url .= '&oauth_callback=' . UrlEncode($redirect_uri);
                }
                if ($this->debug)
                    $this->OutputDebug('Redirecting to OAuth authorize page ' . $url);
                $this->Redirect($url);
                $this->exit = true;
                return true;


            default:
                return ($this->SetError($this->oauth_version . ' is not a supported version of the OAuth protocol'));
        }
        return (true);
    }

    Function GetRequestToken(&$token, &$verifier)
    {
        $token = (IsSet($_GET['oauth_token']) ? $_GET['oauth_token'] : null);
        $verifier = (IsSet($_GET['oauth_verifier']) ? $_GET['oauth_verifier'] : null);
        return (true);
    }

    Function GetRequestDenied(&$denied)
    {
        $denied = (IsSet($_GET['denied']) ? $_GET['denied'] : null);
        return (true);
    }

    Function GetRequestTokenURL(&$request_token_url)
    {
        $request_token_url = $this->request_token_url;
        return (true);
    }

    Function GetDialogURL(&$url, $redirect_uri = '', $state = '')
    {
        $url = (($this->offline && strlen($this->offline_dialog_url)) ? $this->offline_dialog_url : (($redirect_uri === 'oob' && strlen($this->pin_dialog_url)) ? $this->pin_dialog_url : $this->dialog_url));
        if (strlen($url) === 0)
            return $this->SetError('the dialog URL ' . ($this->offline ? 'for offline access ' : '') . 'is not defined for this server');
        $url = str_replace(
            '{REDIRECT_URI}', UrlEncode($redirect_uri), str_replace(
            '{STATE}', UrlEncode($state), str_replace(
            '{CLIENT_ID}', UrlEncode($this->client_id), str_replace(
            '{API_KEY}', UrlEncode($this->api_key), str_replace(
            '{SCOPE}', UrlEncode($this->scope),
            $url)))));
        return (true);
    }

    Function Redirect($url)
    {
        Header('HTTP/1.0 302 OAuth Redirection');
        Header('Location: ' . $url);
    }

    Function Finalize($success)
    {
        return ($success);
    }

    Function Output()
    {
        if (strlen($this->authorization_error)
            || strlen($this->access_token_error)
            || strlen($this->access_token)) {
            ?>
            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
            <html>
            <head>
                <title>OAuth client result</title>
            </head>
            <body>
            <h1>OAuth client result</h1>
            <?php
            if (strlen($this->authorization_error)) {
                ?>
                <p>It was not possible to authorize the application.<?php
                    if ($this->debug) {
                        ?>
                        <br>Authorization error: <?php echo HtmlSpecialChars($this->authorization_error);
                    }
                    ?></p>
                <?php
            } elseif (strlen($this->access_token_error)) {
                ?>
                <p>It was not possible to use the application access token.
                    <?php
                    if ($this->debug) {
                        ?>
                        <br>Error: <?php echo HtmlSpecialChars($this->access_token_error);
                    }
                    ?></p>
                <?php
            } elseif (strlen($this->access_token)) {
                ?>
                <p>The application authorization was obtained successfully.
                    <?php
                    if ($this->debug) {
                        ?>
                        <br>Access token: <?php echo HtmlSpecialChars($this->access_token);
                        if (IsSet($this->access_token_secret)) {
                            ?>
                            <br>Access token secret: <?php echo HtmlSpecialChars($this->access_token_secret);
                        }
                    }
                    ?></p>
                <?php
                if (strlen($this->access_token_expiry)) {
                    ?>
                    <p>Access token expiry: <?php echo $this->access_token_expiry; ?> UTC</p>
                    <?php
                }
            }
            ?>
            </body>
            </html>
            <?php
        }
    }
    /*
{metadocument}
		</do>
	</function>
{/metadocument}
*/

}

;

/***********************
 * HTTP CLASS
 */
define('HTTP_CLIENT_ERROR_UNSPECIFIED_ERROR', -1);
define('HTTP_CLIENT_ERROR_NO_ERROR', 0);
define('HTTP_CLIENT_ERROR_INVALID_SERVER_ADDRESS', 1);
define('HTTP_CLIENT_ERROR_CANNOT_CONNECT', 2);
define('HTTP_CLIENT_ERROR_COMMUNICATION_FAILURE', 3);
define('HTTP_CLIENT_ERROR_CANNOT_ACCESS_LOCAL_FILE', 4);
define('HTTP_CLIENT_ERROR_PROTOCOL_FAILURE', 5);
define('HTTP_CLIENT_ERROR_INVALID_PARAMETERS', 6);

class http_class
{
    var $host_name = "";
    var $host_port = 0;
    var $proxy_host_name = "";
    var $proxy_host_port = 80;
    var $socks_host_name = '';
    var $socks_host_port = 1080;
    var $socks_version = '5';

    var $protocol = "http";
    var $request_method = "GET";
    var $user_agent = 'httpclient (http://www.phpclasses.org/httpclient $Revision: 1.92 $)';
    var $accept = '';
    var $authentication_mechanism = "";
    var $user;
    var $password;
    var $realm;
    var $workstation;
    var $proxy_authentication_mechanism = "";
    var $proxy_user;
    var $proxy_password;
    var $proxy_realm;
    var $proxy_workstation;
    var $request_uri = "";
    var $request = "";
    var $request_headers = array();
    var $request_user;
    var $request_password;
    var $request_realm;
    var $request_workstation;
    var $proxy_request_user;
    var $proxy_request_password;
    var $proxy_request_realm;
    var $proxy_request_workstation;
    var $request_body = "";
    var $request_arguments = array();
    var $protocol_version = "1.1";
    var $timeout = 0;
    var $data_timeout = 0;
    var $debug = 0;
    var $log_debug = 0;
    var $debug_response_body = 1;
    var $html_debug = 0;
    var $support_cookies = 1;
    var $cookies = array();
    var $error = "";
    var $error_code = HTTP_CLIENT_ERROR_NO_ERROR;
    var $exclude_address = "";
    var $follow_redirect = 0;
    var $redirection_limit = 5;
    var $response_status = "";
    var $response_message = "";
    var $file_buffer_length = 8000;
    var $force_multipart_form_post = 0;
    var $prefer_curl = 0;
    var $keep_alive = 1;
    var $sasl_authenticate = 1;

    /* private variables - DO NOT ACCESS */

    var $state = "Disconnected";
    var $use_curl = 0;
    var $connection = 0;
    var $content_length = 0;
    var $response = "";
    var $read_response = 0;
    var $read_length = 0;
    var $request_host = "";
    var $next_token = "";
    var $redirection_level = 0;
    var $chunked = 0;
    var $remaining_chunk = 0;
    var $last_chunk_read = 0;
    var $months = array(
        "Jan" => "01",
        "Feb" => "02",
        "Mar" => "03",
        "Apr" => "04",
        "May" => "05",
        "Jun" => "06",
        "Jul" => "07",
        "Aug" => "08",
        "Sep" => "09",
        "Oct" => "10",
        "Nov" => "11",
        "Dec" => "12");
    var $session = '';
    var $connection_close = 0;
    var $force_close = 0;
    var $connected_host = '';
    var $connected_port = -1;
    var $connected_ssl = 0;

    /* Private methods - DO NOT CALL */

    Function SendRequestBody($data, $end_of_data)
    {
        if (strlen($this->error))
            return ($this->error);
        switch ($this->state) {
            case "Disconnected":
                return ($this->SetError("connection was not yet established", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            case "Connected":
            case "ConnectedToProxy":
                return ($this->SetError("request was not sent", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            case "SendingRequestBody":
                break;
            case "RequestSent":
                return ($this->SetError("request body was already sent", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            default:
                return ($this->SetError("can not send the request body in the current connection state", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        }
        $length = strlen($data);
        if ($length) {
            $size = dechex($length) . "\r\n";
            if (!$this->PutData($size)
                || !$this->PutData($data))
                return ($this->error);
        }
        if ($end_of_data) {
            $size = "0\r\n";
            if (!$this->PutData($size))
                return ($this->error);
            $this->state = "RequestSent";
        }
        return ("");
    }

    Function PutData($data)
    {
        if (strlen($data)) {
            if ($this->debug)
                $this->OutputDebug('C ' . $data);
            if (!fputs($this->connection, $data)) {
                $this->SetDataAccessError("it was not possible to send data to the HTTP server");
                return (0);
            }
        }
        return (1);
    }

    Function Redirect(&$headers)
    {
        if ($this->follow_redirect) {
            if (!IsSet($headers["location"])
                || (GetType($headers["location"]) != "array"
                    && strlen($location = $headers["location"]) == 0)
                || (GetType($headers["location"]) == "array"
                    && strlen($location = $headers["location"][0]) == 0))
                return ($this->SetError("it was received a redirect without location URL", HTTP_CLIENT_ERROR_PROTOCOL_FAILURE));
            if (strcmp($location[0], "/")) {
                if (!($location_arguments = @parse_url($location)))
                    return ($this->SetError("the server did not return a valid redirection location URL", HTTP_CLIENT_ERROR_PROTOCOL_FAILURE));
                if (!IsSet($location_arguments["scheme"]))
                    $location = ((GetType($end = strrpos($this->request_uri, "/")) == "integer" && $end > 1) ? substr($this->request_uri, 0, $end) : "") . "/" . $location;
            }
            if (!strcmp($location[0], "/"))
                $location = $this->protocol . "://" . $this->host_name . ($this->host_port ? ":" . $this->host_port : "") . $location;
            $error = $this->GetRequestArguments($location, $arguments);
            if (strlen($error))
                return ($this->SetError("could not process redirect url: " . $error, HTTP_CLIENT_ERROR_PROTOCOL_FAILURE));
            $arguments["RequestMethod"] = "GET";
            if (strlen($error = $this->Close()) == 0
                && strlen($error = $this->Open($arguments)) == 0
                && strlen($error = $this->SendRequest($arguments)) == 0) {
                $this->redirection_level++;
                if ($this->redirection_level > $this->redirection_limit) {
                    $error = "it was exceeded the limit of request redirections";
                    $this->error_code = HTTP_CLIENT_ERROR_PROTOCOL_FAILURE;
                } else
                    $error = $this->ReadReplyHeaders($headers);
                $this->redirection_level--;
            }
            if (strlen($error))
                return ($this->SetError($error, $this->error_code));
        }
        return ("");
    }

    Function GetRequestArguments($url, &$arguments)
    {
        $this->error = '';
        $this->error_code = HTTP_CLIENT_ERROR_NO_ERROR;
        $arguments = array();
        $url = str_replace(' ', '%20', $url);
        $parameters = @parse_url($url);
        if (!$parameters)
            return ($this->SetError("it was not specified a valid URL", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        if (!IsSet($parameters["scheme"]))
            return ($this->SetError("it was not specified the protocol type argument", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        switch (strtolower($parameters["scheme"])) {
            case "http":
            case "https":
                $arguments["Protocol"] = $parameters["scheme"];
                break;
            default:
                return ($parameters["scheme"] . " connection scheme is not yet supported");
        }
        if (!IsSet($parameters["host"]))
            return ($this->SetError("it was not specified the connection host argument", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        $arguments["HostName"] = $parameters["host"];
        $arguments["Headers"] = array("Host" => $parameters["host"] . (IsSet($parameters["port"]) ? ":" . $parameters["port"] : ""));
        if (IsSet($parameters["user"])) {
            $arguments["AuthUser"] = UrlDecode($parameters["user"]);
            if (!IsSet($parameters["pass"]))
                $arguments["AuthPassword"] = "";
        }
        if (IsSet($parameters["pass"])) {
            if (!IsSet($parameters["user"]))
                $arguments["AuthUser"] = "";
            $arguments["AuthPassword"] = UrlDecode($parameters["pass"]);
        }
        if (IsSet($parameters["port"])) {
            if (strcmp($parameters["port"], strval(intval($parameters["port"]))))
                return ($this->SetError("it was not specified a valid connection host argument", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            $arguments["HostPort"] = intval($parameters["port"]);
        } else
            $arguments["HostPort"] = 0;
        $arguments["RequestURI"] = (IsSet($parameters["path"]) ? $parameters["path"] : "/") . (IsSet($parameters["query"]) ? "?" . $parameters["query"] : "");
        if (strlen($this->user_agent))
            $arguments["Headers"]["User-Agent"] = $this->user_agent;
        if (strlen($this->accept))
            $arguments["Headers"]["Accept"] = $this->accept;
        return ("");
    }

    Function Close($force = 0)
    {
        if ($this->state == "Disconnected")
            return ("1 already disconnected");
        if (!$this->force_close
            && $this->keep_alive
            && !$force
            && $this->state == 'ResponseReceived') {
            if ($this->debug)
                $this->OutputDebug('Keeping the connection alive to ' . $this->connected_host);
            $this->state = 'Connected';
            return ('');
        }
        return ($this->Disconnect());
    }

    Function Disconnect()
    {
        if ($this->debug)
            $this->OutputDebug("Disconnected from " . $this->connected_host);
        if ($this->use_curl) {
            curl_close($this->connection);
            $this->response = "";
        } else
            fclose($this->connection);
        $this->state = "Disconnected";
        return ("");
    }

    Function Open($arguments)
    {
        if (strlen($this->error))
            return ($this->error);
        $error_code = HTTP_CLIENT_ERROR_UNSPECIFIED_ERROR;
        if (IsSet($arguments["HostName"]))
            $this->host_name = $arguments["HostName"];
        if (IsSet($arguments["HostPort"]))
            $this->host_port = $arguments["HostPort"];
        if (IsSet($arguments["ProxyHostName"]))
            $this->proxy_host_name = $arguments["ProxyHostName"];
        if (IsSet($arguments["ProxyHostPort"]))
            $this->proxy_host_port = $arguments["ProxyHostPort"];
        if (IsSet($arguments["SOCKSHostName"]))
            $this->socks_host_name = $arguments["SOCKSHostName"];
        if (IsSet($arguments["SOCKSHostPort"]))
            $this->socks_host_port = $arguments["SOCKSHostPort"];
        if (IsSet($arguments["SOCKSVersion"]))
            $this->socks_version = $arguments["SOCKSVersion"];
        if (IsSet($arguments["Protocol"]))
            $this->protocol = $arguments["Protocol"];
        switch (strtolower($this->protocol)) {
            case "http":
                $default_port = 80;
                break;
            case "https":
                $default_port = 443;
                break;
            default:
                return ($this->SetError("it was not specified a valid connection protocol", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        }
        if (strlen($this->proxy_host_name) == 0) {
            if (strlen($this->host_name) == 0)
                return ($this->SetError("it was not specified a valid hostname", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            $host_name = $this->host_name;
            $host_port = ($this->host_port ? $this->host_port : $default_port);
            $server_type = 'HTTP';
        } else {
            $host_name = $this->proxy_host_name;
            $host_port = $this->proxy_host_port;
            $server_type = 'HTTP proxy';
        }
        $ssl = (strtolower($this->protocol) == "https" && strlen($this->proxy_host_name) == 0);
        if ($ssl
            && strlen($this->socks_host_name))
            return ($this->SetError('establishing SSL connections via a SOCKS server is not yet supported', HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        $this->use_curl = ($ssl && $this->prefer_curl && function_exists("curl_init"));
        switch ($this->state) {
            case 'Connected':
                if (!strcmp($host_name, $this->connected_host)
                    && intval($host_port) == $this->connected_port
                    && intval($ssl) == $this->connected_ssl) {
                    if ($this->debug)
                        $this->OutputDebug("Reusing connection to " . $this->connected_host);
                    return ('');
                }
                if (strlen($error = $this->Disconnect()))
                    return ($error);
            case "Disconnected":
                break;
            default:
                return ("1 already connected");
        }
        if ($this->debug)
            $this->OutputDebug("Connecting to " . $this->host_name);
        if ($this->use_curl) {
            $error = (($this->connection = curl_init($this->protocol . "://" . $this->host_name . ($host_port == $default_port ? "" : ":" . strval($host_port)) . "/")) ? "" : "Could not initialize a CURL session");
            if (strlen($error) == 0) {
                if (IsSet($arguments["SSLCertificateFile"]))
                    curl_setopt($this->connection, CURLOPT_SSLCERT, $arguments["SSLCertificateFile"]);
                if (IsSet($arguments["SSLCertificatePassword"]))
                    curl_setopt($this->connection, CURLOPT_SSLCERTPASSWD, $arguments["SSLCertificatePassword"]);
                if (IsSet($arguments["SSLKeyFile"]))
                    curl_setopt($this->connection, CURLOPT_SSLKEY, $arguments["SSLKeyFile"]);
                if (IsSet($arguments["SSLKeyPassword"]))
                    curl_setopt($this->connection, CURLOPT_SSLKEYPASSWD, $arguments["SSLKeyPassword"]);
            }
            $this->state = "Connected";
        } else {
            $error = "";
            if (strlen($this->proxy_host_name)
                && (IsSet($arguments["SSLCertificateFile"])
                    || IsSet($arguments["SSLCertificateFile"])))
                $error = "establishing SSL connections using certificates or private keys via non-SSL proxies is not supported";
            else {
                if ($ssl) {
                    if (IsSet($arguments["SSLCertificateFile"]))
                        $error = "establishing SSL connections using certificates is only supported when the cURL extension is enabled";
                    elseif (IsSet($arguments["SSLKeyFile"]))
                        $error = "establishing SSL connections using a private key is only supported when the cURL extension is enabled";
                    else {
                        $version = explode(".", function_exists("phpversion") ? phpversion() : "3.0.7");
                        $php_version = intval($version[0]) * 1000000 + intval($version[1]) * 1000 + intval($version[2]);
                        if ($php_version < 4003000)
                            $error = "establishing SSL connections requires at least PHP version 4.3.0 or having the cURL extension enabled";
                        elseif (!function_exists("extension_loaded")
                            || !extension_loaded("openssl"))
                            $error = "establishing SSL connections requires the OpenSSL extension enabled";
                    }
                }
                if (strlen($error) == 0) {
                    $error = $this->Connect($host_name, $host_port, $ssl, $server_type);
                    $error_code = $this->error_code;
                }
            }
        }
        if (strlen($error))
            return ($this->SetError($error, $error_code));
        $this->session = md5(uniqid(""));
        $this->connected_host = $host_name;
        $this->connected_port = intval($host_port);
        $this->connected_ssl = intval($ssl);
        return ("");
    }

    Function Connect($host_name, $host_port, $ssl, $server_type = 'HTTP')
    {
        $domain = $host_name;
        $port = $host_port;
        if (strlen($error = $this->Resolve($domain, $ip, $server_type)))
            return ($error);
        if (strlen($this->socks_host_name)) {
            switch ($this->socks_version) {
                case '4':
                    $version = 4;
                    break;
                case '5':
                    $version = 5;
                    break;
                default:
                    return ('it was not specified a supported SOCKS protocol version');
                    break;
            }
            $host_ip = $ip;
            $port = $this->socks_host_port;
            $host_server_type = $server_type;
            $server_type = 'SOCKS';
            if (strlen($error = $this->Resolve($this->socks_host_name, $ip, $server_type)))
                return ($error);
        }
        if ($this->debug)
            $this->OutputDebug('Connecting to ' . $server_type . ' server IP ' . $ip . ' port ' . $port . '...');
        if ($ssl)
            $ip = "ssl://" . $host_name;
        if (($this->connection = ($this->timeout ? @fsockopen($ip, $port, $errno, $error, $this->timeout) : @fsockopen($ip, $port, $errno))) == 0) {
            $error_code = HTTP_CLIENT_ERROR_CANNOT_CONNECT;
            switch ($errno) {
                case -3:
                    return ($this->SetError("socket could not be created", $error_code));
                case -4:
                    return ($this->SetError("dns lookup on hostname \"" . $host_name . "\" failed", $error_code));
                case -5:
                    return ($this->SetError("connection refused or timed out", $error_code));
                case -6:
                    return ($this->SetError("fdopen() call failed", $error_code));
                case -7:
                    return ($this->SetError("setvbuf() call failed", $error_code));
                default:
                    return ($this->SetPHPError($errno . " could not connect to the host \"" . $host_name . "\"", $php_errormsg, $error_code));
            }
        } else {
            if ($this->data_timeout
                && function_exists("socket_set_timeout"))
                socket_set_timeout($this->connection, $this->data_timeout, 0);
            if (strlen($this->socks_host_name)) {
                if ($this->debug)
                    $this->OutputDebug('Connected to the SOCKS server ' . $this->socks_host_name);
                $send_error = 'it was not possible to send data to the SOCKS server';
                $receive_error = 'it was not possible to receive data from the SOCKS server';
                switch ($version) {
                    case 4:
                        $command = 1;
                        $user = '';
                        if (!fputs($this->connection, chr($version) . chr($command) . pack('nN', $host_port, ip2long($host_ip)) . $user . Chr(0)))
                            $error = $this->SetDataAccessError($send_error);
                        else {
                            $response = fgets($this->connection, 9);
                            if (strlen($response) != 8)
                                $error = $this->SetDataAccessError($receive_error);
                            else {
                                $socks_errors = array(
                                    "\x5a" => '',
                                    "\x5b" => 'request rejected',
                                    "\x5c" => 'request failed because client is not running identd (or not reachable from the server)',
                                    "\x5d" => 'request failed because client\'s identd could not confirm the user ID string in the request',
                                );
                                $error_code = $response[1];
                                $error = (IsSet($socks_errors[$error_code]) ? $socks_errors[$error_code] : 'unknown');
                                if (strlen($error))
                                    $error = 'SOCKS error: ' . $error;
                            }
                        }
                        break;
                    case 5:
                        if ($this->debug)
                            $this->OutputDebug('Negotiating the authentication method ...');
                        $methods = 1;
                        $method = 0;
                        if (!fputs($this->connection, chr($version) . chr($methods) . chr($method)))
                            $error = $this->SetDataAccessError($send_error);
                        else {
                            $response = fgets($this->connection, 3);
                            if (strlen($response) != 2)
                                $error = $this->SetDataAccessError($receive_error);
                            elseif (Ord($response[1]) != $method)
                                $error = 'the SOCKS server requires an authentication method that is not yet supported';
                            else {
                                if ($this->debug)
                                    $this->OutputDebug('Connecting to ' . $host_server_type . ' server IP ' . $host_ip . ' port ' . $host_port . '...');
                                $command = 1;
                                $address_type = 1;
                                if (!fputs($this->connection, chr($version) . chr($command) . "\x00" . chr($address_type) . pack('Nn', ip2long($host_ip), $host_port)))
                                    $error = $this->SetDataAccessError($send_error);
                                else {
                                    $response = fgets($this->connection, 11);
                                    if (strlen($response) != 10)
                                        $error = $this->SetDataAccessError($receive_error);
                                    else {
                                        $socks_errors = array(
                                            "\x00" => '',
                                            "\x01" => 'general SOCKS server failure',
                                            "\x02" => 'connection not allowed by ruleset',
                                            "\x03" => 'Network unreachable',
                                            "\x04" => 'Host unreachable',
                                            "\x05" => 'Connection refused',
                                            "\x06" => 'TTL expired',
                                            "\x07" => 'Command not supported',
                                            "\x08" => 'Address type not supported'
                                        );
                                        $error_code = $response[1];
                                        $error = (IsSet($socks_errors[$error_code]) ? $socks_errors[$error_code] : 'unknown');
                                        if (strlen($error))
                                            $error = 'SOCKS error: ' . $error;
                                    }
                                }
                            }
                        }
                        break;
                    default:
                        $error = 'support for SOCKS protocol version ' . $this->socks_version . ' is not yet implemented';
                        break;
                }
                if (strlen($error)) {
                    fclose($this->connection);
                    return ($error);
                }
            }
            if ($this->debug)
                $this->OutputDebug("Connected to $host_name");
            if (strlen($this->proxy_host_name)
                && !strcmp(strtolower($this->protocol), 'https')) {
                if (function_exists('stream_socket_enable_crypto')
                    && in_array('ssl', stream_get_transports()))
                    $this->state = "ConnectedToProxy";
                else {
                    $this->OutputDebug("It is not possible to start SSL after connecting to the proxy server. If the proxy refuses to forward the SSL request, you may need to upgrade to PHP 5.1 or later with OpenSSL support enabled.");
                    $this->state = "Connected";
                }
            } else
                $this->state = "Connected";
            return ("");
        }
    }

    Function Resolve($domain, &$ip, $server_type)
    {
        if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $domain))
            $ip = $domain;
        else {
            if ($this->debug)
                $this->OutputDebug('Resolving ' . $server_type . ' server domain "' . $domain . '"...');
            if (!strcmp($ip = @gethostbyname($domain), $domain))
                $ip = "";
        }
        if (strlen($ip) == 0
            || (strlen($this->exclude_address)
                && !strcmp(@gethostbyname($this->exclude_address), $ip)))
            return ($this->SetError("could not resolve the host domain \"" . $domain . "\"", HTTP_CLIENT_ERROR_INVALID_SERVER_ADDRESS));
        return ('');
    }

    Function SetPHPError($error, &$php_error_message, $error_code = HTTP_CLIENT_ERROR_UNSPECIFIED_ERROR)
    {
        if (IsSet($php_error_message)
            && strlen($php_error_message))
            $error .= ": " . $php_error_message;
        return ($this->SetError($error, $error_code));
    }

    Function SendRequest($arguments)
    {
        if (strlen($this->error))
            return ($this->error);
        if (IsSet($arguments["ProxyUser"]))
            $this->proxy_request_user = $arguments["ProxyUser"];
        elseif (IsSet($this->proxy_user))
            $this->proxy_request_user = $this->proxy_user;
        if (IsSet($arguments["ProxyPassword"]))
            $this->proxy_request_password = $arguments["ProxyPassword"];
        elseif (IsSet($this->proxy_password))
            $this->proxy_request_password = $this->proxy_password;
        if (IsSet($arguments["ProxyRealm"]))
            $this->proxy_request_realm = $arguments["ProxyRealm"];
        elseif (IsSet($this->proxy_realm))
            $this->proxy_request_realm = $this->proxy_realm;
        if (IsSet($arguments["ProxyWorkstation"]))
            $this->proxy_request_workstation = $arguments["ProxyWorkstation"];
        elseif (IsSet($this->proxy_workstation))
            $this->proxy_request_workstation = $this->proxy_workstation;
        switch ($this->state) {
            case "Disconnected":
                return ($this->SetError("connection was not yet established", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            case "Connected":
                $connect = 0;
                break;
            case "ConnectedToProxy":
                if (strlen($error = $this->ConnectFromProxy($arguments, $headers)))
                    return ($error);
                $connect = 1;
                break;
            default:
                return ($this->SetError("can not send request in the current connection state", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        }
        if (IsSet($arguments["RequestMethod"]))
            $this->request_method = $arguments["RequestMethod"];
        if (IsSet($arguments["User-Agent"]))
            $this->user_agent = $arguments["User-Agent"];
        if (!IsSet($arguments["Headers"]["User-Agent"])
            && strlen($this->user_agent))
            $arguments["Headers"]["User-Agent"] = $this->user_agent;
        if (IsSet($arguments["KeepAlive"]))
            $this->keep_alive = intval($arguments["KeepAlive"]);
        if (!IsSet($arguments["Headers"]["Connection"])
            && $this->keep_alive)
            $arguments["Headers"]["Connection"] = 'Keep-Alive';
        if (IsSet($arguments["Accept"]))
            $this->user_agent = $arguments["Accept"];
        if (!IsSet($arguments["Headers"]["Accept"])
            && strlen($this->accept))
            $arguments["Headers"]["Accept"] = $this->accept;
        if (strlen($this->request_method) == 0)
            return ($this->SetError("it was not specified a valid request method", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        if (IsSet($arguments["RequestURI"]))
            $this->request_uri = $arguments["RequestURI"];
        if (strlen($this->request_uri) == 0
            || substr($this->request_uri, 0, 1) != "/")
            return ($this->SetError("it was not specified a valid request URI", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        $this->request_arguments = $arguments;
        $this->request_headers = (IsSet($arguments["Headers"]) ? $arguments["Headers"] : array());
        $body_length = 0;
        $this->request_body = "";
        $get_body = 1;
        if ($this->request_method == "POST"
            || $this->request_method == "PUT") {
            if (IsSet($arguments['StreamRequest'])) {
                $get_body = 0;
                $this->request_headers["Transfer-Encoding"] = "chunked";
            } elseif (IsSet($arguments["PostFiles"])
                || ($this->force_multipart_form_post
                    && IsSet($arguments["PostValues"]))) {
                $boundary = "--" . md5(uniqid(time()));
                $this->request_headers["Content-Type"] = "multipart/form-data; boundary=" . $boundary . (IsSet($arguments["CharSet"]) ? "; charset=" . $arguments["CharSet"] : "");
                $post_parts = array();
                if (IsSet($arguments["PostValues"])) {
                    $values = $arguments["PostValues"];
                    if (GetType($values) != "array")
                        return ($this->SetError("it was not specified a valid POST method values array", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
                    for (Reset($values), $value = 0; $value < count($values); Next($values), $value++) {
                        $input = Key($values);
                        $headers = "--" . $boundary . "\r\nContent-Disposition: form-data; name=\"" . $input . "\"\r\n\r\n";
                        $data = $values[$input];
                        $post_parts[] = array("HEADERS" => $headers, "DATA" => $data);
                        $body_length += strlen($headers) + strlen($data) + strlen("\r\n");
                    }
                }
                $body_length += strlen("--" . $boundary . "--\r\n");
                $files = (IsSet($arguments["PostFiles"]) ? $arguments["PostFiles"] : array());
                Reset($files);
                $end = (GetType($input = Key($files)) != "string");
                for (; !$end;) {
                    if (strlen($error = $this->GetFileDefinition($files[$input], $definition)))
                        return ("3 " . $error);
                    $headers = "--" . $boundary . "\r\nContent-Disposition: form-data; name=\"" . $input . "\"; filename=\"" . $definition["NAME"] . "\"\r\nContent-Type: " . $definition["Content-Type"] . "\r\n\r\n";
                    $part = count($post_parts);
                    $post_parts[$part] = array("HEADERS" => $headers);
                    if (IsSet($definition["FILENAME"])) {
                        $post_parts[$part]["FILENAME"] = $definition["FILENAME"];
                        $data = "";
                    } else
                        $data = $definition["DATA"];
                    $post_parts[$part]["DATA"] = $data;
                    $body_length += strlen($headers) + $definition["Content-Length"] + strlen("\r\n");
                    Next($files);
                    $end = (GetType($input = Key($files)) != "string");
                }
                $get_body = 0;
            } elseif (IsSet($arguments["PostValues"])) {
                $values = $arguments["PostValues"];
                if (GetType($values) != "array")
                    return ($this->SetError("it was not specified a valid POST method values array", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
                for (Reset($values), $value = 0; $value < count($values); Next($values), $value++) {
                    $k = Key($values);
                    if (GetType($values[$k]) == "array") {
                        for ($v = 0; $v < count($values[$k]); $v++) {
                            if ($value + $v > 0)
                                $this->request_body .= "&";
                            $this->request_body .= UrlEncode($k) . "=" . UrlEncode($values[$k][$v]);
                        }
                    } else {
                        if ($value > 0)
                            $this->request_body .= "&";
                        $this->request_body .= UrlEncode($k) . "=" . UrlEncode($values[$k]);
                    }
                }
                $this->request_headers["Content-Type"] = "application/x-www-form-urlencoded" . (IsSet($arguments["CharSet"]) ? "; charset=" . $arguments["CharSet"] : "");
                $get_body = 0;
            }
        }
        if ($get_body
            && (IsSet($arguments["Body"])
                || IsSet($arguments["BodyStream"]))) {
            if (IsSet($arguments["Body"]))
                $this->request_body = $arguments["Body"];
            else {
                $stream = $arguments["BodyStream"];
                $this->request_body = "";
                for ($part = 0; $part < count($stream); $part++) {
                    if (IsSet($stream[$part]["Data"]))
                        $this->request_body .= $stream[$part]["Data"];
                    elseif (IsSet($stream[$part]["File"])) {
                        if (!($file = @fopen($stream[$part]["File"], "rb")))
                            return ($this->SetPHPError("could not open upload file " . $stream[$part]["File"], $php_errormsg, HTTP_CLIENT_ERROR_CANNOT_ACCESS_LOCAL_FILE));
                        while (!feof($file)) {
                            if (GetType($block = @fread($file, $this->file_buffer_length)) != "string") {
                                $error = $this->SetPHPError("could not read body stream file " . $stream[$part]["File"], $php_errormsg, HTTP_CLIENT_ERROR_CANNOT_ACCESS_LOCAL_FILE);
                                fclose($file);
                                return ($error);
                            }
                            $this->request_body .= $block;
                        }
                        fclose($file);
                    } else
                        return ("5 it was not specified a valid file or data body stream element at position " . $part);
                }
            }
            if (!IsSet($this->request_headers["Content-Type"]))
                $this->request_headers["Content-Type"] = "application/octet-stream" . (IsSet($arguments["CharSet"]) ? "; charset=" . $arguments["CharSet"] : "");
        }
        if (IsSet($arguments["AuthUser"]))
            $this->request_user = $arguments["AuthUser"];
        elseif (IsSet($this->user))
            $this->request_user = $this->user;
        if (IsSet($arguments["AuthPassword"]))
            $this->request_password = $arguments["AuthPassword"];
        elseif (IsSet($this->password))
            $this->request_password = $this->password;
        if (IsSet($arguments["AuthRealm"]))
            $this->request_realm = $arguments["AuthRealm"];
        elseif (IsSet($this->realm))
            $this->request_realm = $this->realm;
        if (IsSet($arguments["AuthWorkstation"]))
            $this->request_workstation = $arguments["AuthWorkstation"];
        elseif (IsSet($this->workstation))
            $this->request_workstation = $this->workstation;
        if (strlen($this->proxy_host_name) == 0
            || $connect)
            $request_uri = $this->request_uri;
        else {
            switch (strtolower($this->protocol)) {
                case "http":
                    $default_port = 80;
                    break;
                case "https":
                    $default_port = 443;
                    break;
            }
            $request_uri = strtolower($this->protocol) . "://" . $this->host_name . (($this->host_port == 0 || $this->host_port == $default_port) ? "" : ":" . $this->host_port) . $this->request_uri;
        }
        if ($this->use_curl) {
            $version = (GetType($v = curl_version()) == "array" ? (IsSet($v["version"]) ? $v["version"] : "0.0.0") : (preg_match("/^libcurl\\/([0-9]+\\.[0-9]+\\.[0-9]+)/", $v, $m) ? $m[1] : "0.0.0"));
            $curl_version = 100000 * intval($this->Tokenize($version, ".")) + 1000 * intval($this->Tokenize(".")) + intval($this->Tokenize(""));
            $protocol_version = ($curl_version < 713002 ? "1.0" : $this->protocol_version);
        } else
            $protocol_version = $this->protocol_version;
        $this->request = $this->request_method . " " . $request_uri . " HTTP/" . $protocol_version;
        if ($body_length
            || ($body_length = strlen($this->request_body))
            || !strcmp($this->request_method, 'POST'))
            $this->request_headers["Content-Length"] = $body_length;
        for ($headers = array(), $host_set = 0, Reset($this->request_headers), $header = 0; $header < count($this->request_headers); Next($this->request_headers), $header++) {
            $header_name = Key($this->request_headers);
            $header_value = $this->request_headers[$header_name];
            if (GetType($header_value) == "array") {
                for (Reset($header_value), $value = 0; $value < count($header_value); Next($header_value), $value++)
                    $headers[] = $header_name . ": " . $header_value[Key($header_value)];
            } else
                $headers[] = $header_name . ": " . $header_value;
            if (strtolower(Key($this->request_headers)) == "host") {
                $this->request_host = strtolower($header_value);
                $host_set = 1;
            }
        }
        if (!$host_set) {
            $headers[] = "Host: " . $this->host_name;
            $this->request_host = strtolower($this->host_name);
        }
        if (count($this->cookies)) {
            $cookies = array();
            $this->PickCookies($cookies, 0);
            if (strtolower($this->protocol) == "https")
                $this->PickCookies($cookies, 1);
            if (count($cookies)) {
                $h = count($headers);
                $headers[$h] = "Cookie:";
                for (Reset($cookies), $cookie = 0; $cookie < count($cookies); Next($cookies), $cookie++) {
                    $cookie_name = Key($cookies);
                    $headers[$h] .= " " . $cookie_name . "=" . $cookies[$cookie_name]["value"] . ";";
                }
            }
        }
        $next_state = "RequestSent";
        if ($this->use_curl) {
            if (IsSet($arguments['StreamRequest']))
                return ($this->SetError("Streaming request data is not supported when using Curl", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            if ($body_length
                && strlen($this->request_body) == 0) {


                for ($request_body = "", $success = 1, $part = 0; $part < count($post_parts); $part++) {
                    $request_body .= $post_parts[$part]["HEADERS"] . $post_parts[$part]["DATA"];
                    if (IsSet($post_parts[$part]["FILENAME"])) {
                        if (!($file = @fopen($post_parts[$part]["FILENAME"], "rb"))) {
                            $this->SetPHPError("could not open upload file " . $post_parts[$part]["FILENAME"], $php_errormsg, HTTP_CLIENT_ERROR_CANNOT_ACCESS_LOCAL_FILE);
                            $success = 0;
                            break;
                        }
                        while (!feof($file)) {
                            if (GetType($block = @fread($file, $this->file_buffer_length)) != "string") {
                                $this->SetPHPError("could not read upload file", $php_errormsg, HTTP_CLIENT_ERROR_CANNOT_ACCESS_LOCAL_FILE);
                                $success = 0;
                                break;
                            }
                            $request_body .= $block;
                        }
                        fclose($file);
                        if (!$success)
                            break;
                    }
                    $request_body .= "\r\n";
                }
                $request_body .= "--" . $boundary . "--\r\n";
            } else {


                $request_body = $this->request_body;
            }
            curl_setopt($this->connection, CURLOPT_HEADER, 1);
            curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, 1);
            if ($this->timeout)
                curl_setopt($this->connection, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($this->connection, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($this->connection, CURLOPT_SSL_VERIFYHOST, 0);
            $request = $this->request . "\r\n" . implode("\r\n", $headers) . "\r\n\r\n" . $request_body;
            curl_setopt($this->connection, CURLOPT_CUSTOMREQUEST, $request);
            if ($this->debug)
                $this->OutputDebug("C " . $request);
            if (!($success = (strlen($this->response = curl_exec($this->connection)) != 0))) {
                $error = curl_error($this->connection);
                $this->SetError("Could not execute the request" . (strlen($error) ? ": " . $error : ""), HTTP_CLIENT_ERROR_PROTOCOL_FAILURE);
            }
        } else {

            if (($success = $this->PutLine($this->request))) {
                for ($header = 0; $header < count($headers); $header++) {
                    if (!$success = $this->PutLine($headers[$header]))
                        break;
                }
                if ($success
                    && ($success = $this->PutLine(""))) {
                    if (IsSet($arguments['StreamRequest']))
                        $next_state = "SendingRequestBody";
                    elseif ($body_length) {
                        if (strlen($this->request_body))
                            $success = $this->PutData($this->request_body);
                        else {

                            for ($part = 0; $part < count($post_parts); $part++) {
                                if (!($success = $this->PutData($post_parts[$part]["HEADERS"]))
                                    || !($success = $this->PutData($post_parts[$part]["DATA"])))
                                    break;

                                if (IsSet($post_parts[$part]["FILENAME"])) {

                                    if (!($file = fopen($post_parts[$part]["FILENAME"], "rb"))) {

                                        $this->SetPHPError("could not open upload file " . $post_parts[$part]["FILENAME"], $php_errormsg, HTTP_CLIENT_ERROR_CANNOT_ACCESS_LOCAL_FILE);
                                        $success = 0;
                                        break;
                                    }
                                    while (!feof($file)) {
                                        if (GetType($block = @fread($file, $this->file_buffer_length)) != "string") {
                                            $this->SetPHPError("could not read upload file", $php_errormsg, HTTP_CLIENT_ERROR_CANNOT_ACCESS_LOCAL_FILE);
                                            $success = 0;
                                            break;
                                        }
                                        if (!($success = $this->PutData($block)))
                                            break;
                                    }
                                    fclose($file);
                                    if (!$success)
                                        break;

                                }
                                if (!($success = $this->PutLine("")))
                                    break;
                            }
                            if ($success)
                                $success = $this->PutLine("--" . $boundary . "--");
                        }
                        if ($success)
                            $sucess = $this->FlushData();
                    }
                }
            }
        }
        if (!$success)
            return ($this->SetError("could not send the HTTP request: " . $this->error, $this->error_code));
        $this->state = $next_state;
        return ("");
    }

    Function ConnectFromProxy($arguments, &$headers)
    {
        if (!$this->PutLine('CONNECT ' . $this->host_name . ':' . ($this->host_port ? $this->host_port : 443) . ' HTTP/1.0')
            || (strlen($this->user_agent)
                && !$this->PutLine('User-Agent: ' . $this->user_agent))
            || (strlen($this->accept)
                && !$this->PutLine('Accept: ' . $this->accept))
            || (IsSet($arguments['Headers']['Proxy-Authorization'])
                && !$this->PutLine('Proxy-Authorization: ' . $arguments['Headers']['Proxy-Authorization']))
            || !$this->PutLine('')) {
            $this->Disconnect();
            return ($this->error);
        }
        $this->state = "ConnectSent";
        if (strlen($error = $this->ReadReplyHeadersResponse($headers)))
            return ($error);
        $proxy_authorization = "";
        while (!strcmp($this->response_status, "100")) {
            $this->state = "ConnectSent";
            if (strlen($error = $this->ReadReplyHeadersResponse($headers)))
                return ($error);
        }
        switch ($this->response_status) {
            case "200":
                if (!@stream_socket_enable_crypto($this->connection, 1, STREAM_CRYPTO_METHOD_SSLv23_CLIENT)) {
                    $this->SetPHPError('it was not possible to start a SSL encrypted connection via this proxy', $php_errormsg, HTTP_CLIENT_ERROR_COMMUNICATION_FAILURE);
                    $this->Disconnect();
                    return ($this->error);
                }
                $this->state = "Connected";
                break;
            case "407":
                if (strlen($error = $this->Authenticate($headers, -1, $proxy_authorization, $this->proxy_request_user, $this->proxy_request_password, $this->proxy_request_realm, $this->proxy_request_workstation)))
                    return ($error);
                break;
            default:
                return ($this->SetError("unable to send request via proxy", HTTP_CLIENT_ERROR_PROTOCOL_FAILURE));
        }
        return ("");
    }

    Function PutLine($line)
    {
        if ($this->debug)
            $this->OutputDebug("C $line");
        if (!fputs($this->connection, $line . "\r\n")) {
            $this->SetDataAccessError("it was not possible to send a line to the HTTP server");
            return (0);
        }
        return (1);
    }

    Function ReadReplyHeadersResponse(&$headers)
    {
        $headers = array();
        if (strlen($this->error))
            return ($this->error);
        switch ($this->state) {
            case "Disconnected":
                return ($this->SetError("connection was not yet established", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            case "Connected":
                return ($this->SetError("request was not sent", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            case "ConnectedToProxy":
                return ($this->SetError("connection from the remote server from the proxy was not yet established", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            case "SendingRequestBody":
                return ($this->SetError("request body data was not completely sent", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            case "ConnectSent":
                $connect = 1;
                break;
            case "RequestSent":
                $connect = 0;
                break;
            default:
                return ($this->SetError("can not get request headers in the current connection state", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        }
        $this->content_length = $this->read_length = $this->read_response = $this->remaining_chunk = 0;
        $this->content_length_set = $this->chunked = $this->last_chunk_read = $chunked = 0;
        $this->force_close = $this->connection_close = 0;
        for ($this->response_status = ""; ;) {
            $line = $this->GetLine();
            if (GetType($line) != "string")
                return ($this->SetError("could not read request reply: " . $this->error, $this->error_code));
            if (strlen($this->response_status) == 0) {
                if (!preg_match($match = "/^http\\/[0-9]+\\.[0-9]+[ \t]+([0-9]+)[ \t]*(.*)\$/i", $line, $matches))
                    return ($this->SetError("it was received an unexpected HTTP response status", HTTP_CLIENT_ERROR_PROTOCOL_FAILURE));
                $this->response_status = $matches[1];
                $this->response_message = $matches[2];
                if ($this->response_status == 204) {
                    $this->content_length = 0;
                    $this->content_length_set = 1;
                }
            }
            if ($line == "") {
                if (strlen($this->response_status) == 0)
                    return ($this->SetError("it was not received HTTP response status", HTTP_CLIENT_ERROR_PROTOCOL_FAILURE));
                $this->state = ($connect ? "GotConnectHeaders" : "GotReplyHeaders");
                break;
            }
            $header_name = strtolower($this->Tokenize($line, ":"));
            $header_value = Trim(Chop($this->Tokenize("\r\n")));
            if (IsSet($headers[$header_name])) {
                if (GetType($headers[$header_name]) == "string")
                    $headers[$header_name] = array($headers[$header_name]);
                $headers[$header_name][] = $header_value;
            } else
                $headers[$header_name] = $header_value;
            if (!$connect) {
                switch ($header_name) {
                    case "content-length":
                        $this->content_length = intval($headers[$header_name]);
                        $this->content_length_set = 1;
                        break;
                    case "transfer-encoding":
                        $encoding = $this->Tokenize($header_value, "; \t");
                        if (!$this->use_curl
                            && !strcmp($encoding, "chunked"))
                            $chunked = 1;
                        break;
                    case "set-cookie":
                        if ($this->support_cookies) {
                            if (GetType($headers[$header_name]) == "array")
                                $cookie_headers = $headers[$header_name];
                            else
                                $cookie_headers = array($headers[$header_name]);
                            for ($cookie = 0; $cookie < count($cookie_headers); $cookie++) {
                                $cookie_name = trim($this->Tokenize($cookie_headers[$cookie], "="));
                                $cookie_value = $this->Tokenize(";");
                                $domain = $this->request_host;
                                $path = "/";
                                $expires = "";
                                $secure = 0;
                                while (($name = strtolower(trim(UrlDecode($this->Tokenize("="))))) != "") {
                                    $value = UrlDecode($this->Tokenize(";"));
                                    switch ($name) {
                                        case "domain":
                                            $domain = $value;
                                            break;
                                        case "path":
                                            $path = $value;
                                            break;
                                        case "expires":
                                            if (preg_match("/^((Mon|Monday|Tue|Tuesday|Wed|Wednesday|Thu|Thursday|Fri|Friday|Sat|Saturday|Sun|Sunday), )?([0-9]{2})\\-(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\\-([0-9]{2,4}) ([0-9]{2})\\:([0-9]{2})\\:([0-9]{2}) GMT\$/", $value, $matches)) {
                                                $year = intval($matches[5]);
                                                if ($year < 1900)
                                                    $year += ($year < 70 ? 2000 : 1900);
                                                $expires = "$year-" . $this->months[$matches[4]] . "-" . $matches[3] . " " . $matches[6] . ":" . $matches[7] . ":" . $matches[8];
                                            }
                                            break;
                                        case "secure":
                                            $secure = 1;
                                            break;
                                    }
                                }
                                if (strlen($this->SetCookie($cookie_name, $cookie_value, $expires, $path, $domain, $secure, 1)))
                                    $this->error = "";
                            }
                        }
                        break;
                    case "connection":
                        $this->force_close = $this->connection_close = !strcmp(strtolower($header_value), "close");
                        break;
                }
            }
        }
        $this->chunked = $chunked;
        if ($this->content_length_set)
            $this->connection_close = 0;
        return ("");
    }

    Function Tokenize($string, $separator = "")
    {
        if (!strcmp($separator, "")) {
            $separator = $string;
            $string = $this->next_token;
        }
        for ($character = 0; $character < strlen($separator); $character++) {
            if (GetType($position = strpos($string, $separator[$character])) == "integer")
                $found = (IsSet($found) ? min($found, $position) : $position);
        }
        if (IsSet($found)) {
            $this->next_token = substr($string, $found + 1);
            return (substr($string, 0, $found));
        } else {
            $this->next_token = "";
            return ($string);
        }
    }

    Function SetCookie($name, $value, $expires = "", $path = "/", $domain = "", $secure = 0, $verbatim = 0)
    {
        if (strlen($this->error))
            return ($this->error);
        if (strlen($name) == 0)
            return ($this->SetError("it was not specified a valid cookie name", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        if (strlen($path) == 0
            || strcmp($path[0], "/"))
            return ($this->SetError($path . " is not a valid path for setting cookie " . $name, HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        if ($domain == ""
            || !strpos($domain, ".", $domain[0] == "." ? 1 : 0))
            return ($this->SetError($domain . " is not a valid domain for setting cookie " . $name, HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        $domain = strtolower($domain);
        if (!strcmp($domain[0], "."))
            $domain = substr($domain, 1);
        if (!$verbatim) {
            $name = $this->CookieEncode($name, 1);
            $value = $this->CookieEncode($value, 0);
        }
        $secure = intval($secure);
        $this->cookies[$secure][$domain][$path][$name] = array(
            "name" => $name,
            "value" => $value,
            "domain" => $domain,
            "path" => $path,
            "expires" => $expires,
            "secure" => $secure
        );
        return ("");
    }

    /* Public methods */

    Function CookieEncode($value, $name)
    {
        return ($name ? str_replace("=", "%25", $value) : str_replace(";", "%3B", $value));
    }

    Function Authenticate(&$headers, $proxy, &$proxy_authorization, &$user, &$password, &$realm, &$workstation)
    {
        if ($proxy) {
            $authenticate_header = "proxy-authenticate";
            $authorization_header = "Proxy-Authorization";
            $authenticate_status = "407";
            $authentication_mechanism = $this->proxy_authentication_mechanism;
        } else {
            $authenticate_header = "www-authenticate";
            $authorization_header = "Authorization";
            $authenticate_status = "401";
            $authentication_mechanism = $this->authentication_mechanism;
        }
        if (IsSet($headers[$authenticate_header])
            && $this->sasl_authenticate) {
            if (function_exists("class_exists")
                && !class_exists("sasl_client_class"))
                return ($this->SetError("the SASL client class needs to be loaded to be able to authenticate" . ($proxy ? " with the proxy server" : "") . " and access this site", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            if (GetType($headers[$authenticate_header]) == "array")
                $authenticate = $headers[$authenticate_header];
            else
                $authenticate = array($headers[$authenticate_header]);
            for ($response = "", $mechanisms = array(), $m = 0; $m < count($authenticate); $m++) {
                $mechanism = $this->Tokenize($authenticate[$m], " ");
                $response = $this->Tokenize("");
                if (strlen($authentication_mechanism)) {
                    if (!strcmp($authentication_mechanism, $mechanism)) {
                        $mechanisms[] = $mechanism;
                        break;
                    }
                } else
                    $mechanisms[] = $mechanism;
            }
            $sasl = new sasl_client_class;
            if (IsSet($user))
                $sasl->SetCredential("user", $user);
            if (IsSet($password))
                $sasl->SetCredential("password", $password);
            if (IsSet($realm))
                $sasl->SetCredential("realm", $realm);
            if (IsSet($workstation))
                $sasl->SetCredential("workstation", $workstation);
            $sasl->SetCredential("uri", $this->request_uri);
            $sasl->SetCredential("method", $this->request_method);
            $sasl->SetCredential("session", $this->session);
            do {
                $status = $sasl->Start($mechanisms, $message, $interactions);
            } while ($status == SASL_INTERACT);
            switch ($status) {
                case SASL_CONTINUE:
                    break;
                case SASL_NOMECH:
                    return ($this->SetError(($proxy ? "proxy " : "") . "authentication error: " . (strlen($authentication_mechanism) ? "authentication mechanism " . $authentication_mechanism . " may not be used: " : "") . $sasl->error, HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
                default:
                    return ($this->SetError("Could not start the SASL " . ($proxy ? "proxy " : "") . "authentication client: " . $sasl->error, HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            }
            if ($proxy >= 0) {
                for (; ;) {
                    if (strlen($error = $this->ReadReplyBody($body, $this->file_buffer_length)))
                        return ($error);
                    if (strlen($body) == 0)
                        break;
                }
            }
            $authorization_value = $sasl->mechanism . (IsSet($message) ? " " . ($sasl->encode_response ? base64_encode($message) : $message) : "");
            $request_arguments = $this->request_arguments;
            $arguments = $request_arguments;
            $arguments["Headers"][$authorization_header] = $authorization_value;
            if (!$proxy
                && strlen($proxy_authorization))
                $arguments["Headers"]["Proxy-Authorization"] = $proxy_authorization;
            if (strlen($error = $this->Close())
                || strlen($error = $this->Open($arguments)))
                return ($this->SetError($error, $this->error_code));
            $authenticated = 0;
            if (IsSet($message)) {
                if ($proxy < 0) {
                    if (strlen($error = $this->ConnectFromProxy($arguments, $headers)))
                        return ($this->SetError($error, $this->error_code));
                } else {
                    if (strlen($error = $this->SendRequest($arguments))
                        || strlen($error = $this->ReadReplyHeadersResponse($headers)))
                        return ($this->SetError($error, $this->error_code));
                }
                if (!IsSet($headers[$authenticate_header]))
                    $authenticate = array();
                elseif (GetType($headers[$authenticate_header]) == "array")
                    $authenticate = $headers[$authenticate_header];
                else
                    $authenticate = array($headers[$authenticate_header]);
                for ($mechanism = 0; $mechanism < count($authenticate); $mechanism++) {
                    if (!strcmp($this->Tokenize($authenticate[$mechanism], " "), $sasl->mechanism)) {
                        $response = $this->Tokenize("");
                        break;
                    }
                }
                switch ($this->response_status) {
                    case $authenticate_status:
                        break;
                    case "301":
                    case "302":
                    case "303":
                    case "307":
                        if ($proxy >= 0)
                            return ($this->Redirect($headers));
                    default:
                        if (intval($this->response_status / 100) == 2) {
                            if ($proxy)
                                $proxy_authorization = $authorization_value;
                            $authenticated = 1;
                            break;
                        }
                        if ($proxy
                            && !strcmp($this->response_status, "401")) {
                            $proxy_authorization = $authorization_value;
                            $authenticated = 1;
                            break;
                        }
                        return ($this->SetError(($proxy ? "proxy " : "") . "authentication error: " . $this->response_status . " " . $this->response_message, HTTP_CLIENT_ERROR_PROTOCOL_FAILURE));
                }
            }
            for (; !$authenticated;) {
                do {
                    $status = $sasl->Step($response, $message, $interactions);
                } while ($status == SASL_INTERACT);
                switch ($status) {
                    case SASL_CONTINUE:
                        $authorization_value = $sasl->mechanism . (IsSet($message) ? " " . ($sasl->encode_response ? base64_encode($message) : $message) : "");
                        $arguments = $request_arguments;
                        $arguments["Headers"][$authorization_header] = $authorization_value;
                        if (!$proxy
                            && strlen($proxy_authorization))
                            $arguments["Headers"]["Proxy-Authorization"] = $proxy_authorization;
                        if ($proxy < 0) {
                            if (strlen($error = $this->ConnectFromProxy($arguments, $headers)))
                                return ($this->SetError($error, $this->error_code));
                        } else {
                            if (strlen($error = $this->SendRequest($arguments))
                                || strlen($error = $this->ReadReplyHeadersResponse($headers)))
                                return ($this->SetError($error, $this->error_code));
                        }
                        switch ($this->response_status) {
                            case $authenticate_status:
                                if (GetType($headers[$authenticate_header]) == "array")
                                    $authenticate = $headers[$authenticate_header];
                                else
                                    $authenticate = array($headers[$authenticate_header]);
                                for ($response = "", $mechanism = 0; $mechanism < count($authenticate); $mechanism++) {
                                    if (!strcmp($this->Tokenize($authenticate[$mechanism], " "), $sasl->mechanism)) {
                                        $response = $this->Tokenize("");
                                        break;
                                    }
                                }
                                if ($proxy >= 0) {
                                    for (; ;) {
                                        if (strlen($error = $this->ReadReplyBody($body, $this->file_buffer_length)))
                                            return ($error);
                                        if (strlen($body) == 0)
                                            break;
                                    }
                                }
                                $this->state = "Connected";
                                break;
                            case "301":
                            case "302":
                            case "303":
                            case "307":
                                if ($proxy >= 0)
                                    return ($this->Redirect($headers));
                            default:
                                if (intval($this->response_status / 100) == 2) {
                                    if ($proxy)
                                        $proxy_authorization = $authorization_value;
                                    $authenticated = 1;
                                    break;
                                }
                                if ($proxy
                                    && !strcmp($this->response_status, "401")) {
                                    $proxy_authorization = $authorization_value;
                                    $authenticated = 1;
                                    break;
                                }
                                return ($this->SetError(($proxy ? "proxy " : "") . "authentication error: " . $this->response_status . " " . $this->response_message));
                        }
                        break;
                    default:
                        return ($this->SetError("Could not process the SASL " . ($proxy ? "proxy " : "") . "authentication step: " . $sasl->error, HTTP_CLIENT_ERROR_PROTOCOL_FAILURE));
                }
            }
        }
        return ("");
    }

    Function ReadReplyBody(&$body, $length)
    {
        $body = "";
        if (strlen($this->error))
            return ($this->error);
        switch ($this->state) {
            case "Disconnected":
                return ($this->SetError("connection was not yet established", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            case "Connected":
            case "ConnectedToProxy":
                return ($this->SetError("request was not sent", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            case "RequestSent":
                if (($error = $this->ReadReplyHeaders($headers)) != "")
                    return ($error);
                break;
            case "GotReplyHeaders":
                break;
            case 'ResponseReceived':
                $body = '';
                return ('');
            default:
                return ($this->SetError("can not get request headers in the current connection state", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
        }
        if ($this->content_length_set)
            $length = min($this->content_length - $this->read_length, $length);
        $body = '';
        if ($length > 0) {
            if (!$this->EndOfInput()
                && ($body = $this->ReadBytes($length)) == "") {
                if (strlen($this->error))
                    return ($this->SetError("could not get the request reply body: " . $this->error, $this->error_code));
            }
            $this->read_length += strlen($body);
            if ($this->EndOfInput())
                $this->state = 'ResponseReceived';
        }
        return ("");
    }

    Function ReadReplyHeaders(&$headers)
    {
        if (strlen($error = $this->ReadReplyHeadersResponse($headers)))
            return ($error);
        $proxy_authorization = "";
        while (!strcmp($this->response_status, "100")) {
            $this->state = "RequestSent";
            if (strlen($error = $this->ReadReplyHeadersResponse($headers)))
                return ($error);
        }
        switch ($this->response_status) {
            case "301":
            case "302":
            case "303":
            case "307":
                if (strlen($error = $this->Redirect($headers)))
                    return ($error);
                break;
            case "407":
                if (strlen($error = $this->Authenticate($headers, 1, $proxy_authorization, $this->proxy_request_user, $this->proxy_request_password, $this->proxy_request_realm, $this->proxy_request_workstation)))
                    return ($error);
                if (strcmp($this->response_status, "401"))
                    return ("");
            case "401":
                return ($this->Authenticate($headers, 0, $proxy_authorization, $this->request_user, $this->request_password, $this->request_realm, $this->request_workstation));
        }
        return ("");
    }

    Function EndOfInput()
    {
        if ($this->use_curl)
            return ($this->read_response >= strlen($this->response));
        if ($this->chunked)
            return ($this->last_chunk_read);
        if ($this->content_length_set)
            return ($this->content_length <= $this->read_length);
        return (feof($this->connection));
    }

    Function ReadBytes($length)
    {
        if ($this->use_curl) {
            $bytes = substr($this->response, $this->read_response, min($length, strlen($this->response) - $this->read_response));
            $this->read_response += strlen($bytes);
            if ($this->debug
                && $this->debug_response_body
                && strlen($bytes))
                $this->OutputDebug("S " . $bytes);
        } else {
            if ($this->chunked) {
                for ($bytes = "", $remaining = $length; $remaining;) {
                    if (strlen($this->ReadChunkSize()))
                        return ("");
                    if ($this->remaining_chunk == 0) {
                        $this->last_chunk_read = 1;
                        break;
                    }
                    $ask = min($this->remaining_chunk, $remaining);
                    $chunk = @fread($this->connection, $ask);
                    $read = strlen($chunk);
                    if ($read == 0) {
                        $this->SetDataAccessError("it was not possible to read data chunk from the HTTP server");
                        return ("");
                    }
                    if ($this->debug
                        && $this->debug_response_body)
                        $this->OutputDebug("S " . $chunk);
                    $bytes .= $chunk;
                    $this->remaining_chunk -= $read;
                    $remaining -= $read;
                    if ($this->remaining_chunk == 0) {
                        if (feof($this->connection))
                            return ($this->SetError("reached the end of data while reading the end of data chunk mark from the HTTP server", HTTP_CLIENT_ERROR_PROTOCOL_FAILURE));
                        $data = @fread($this->connection, 2);
                        if (strcmp($data, "\r\n")) {
                            $this->SetDataAccessError("it was not possible to read end of data chunk from the HTTP server");
                            return ("");
                        }
                    }
                }
            } else {
                $bytes = @fread($this->connection, $length);
                if (strlen($bytes)) {
                    if ($this->debug
                        && $this->debug_response_body)
                        $this->OutputDebug("S " . $bytes);
                } else
                    $this->SetDataAccessError("it was not possible to read data from the HTTP server", $this->connection_close);
            }
        }
        return ($bytes);
    }

    Function OutputDebug($message)
    {
        if ($this->log_debug)
            error_log($message);
        else {
            $message .= "\n";
            if ($this->html_debug)
                $message = str_replace("\n", "<br />\n", HtmlEntities($message));
            echo $message;
            flush();
        }
    }

    Function ReadChunkSize()
    {
        if ($this->remaining_chunk == 0) {
            $debug = $this->debug;
            if (!$this->debug_response_body)
                $this->debug = 0;
            $line = $this->GetLine();
            $this->debug = $debug;
            if (GetType($line) != "string")
                return ($this->SetError("could not read chunk start: " . $this->error, $this->error_code));
            $this->remaining_chunk = hexdec($line);
            if ($this->remaining_chunk == 0) {
                if (!$this->debug_response_body)
                    $this->debug = 0;
                $line = $this->GetLine();
                $this->debug = $debug;
                if (GetType($line) != "string")
                    return ($this->SetError("could not read chunk end: " . $this->error, $this->error_code));
            }
        }
        return ("");
    }

    Function GetLine()
    {
        for ($line = ""; ;) {
            if ($this->use_curl) {
                $eol = strpos($this->response, "\n", $this->read_response);
                $data = ($eol ? substr($this->response, $this->read_response, $eol + 1 - $this->read_response) : "");
                $this->read_response += strlen($data);
            } else {
                if (feof($this->connection)) {
                    $this->SetDataAccessError("reached the end of data while reading from the HTTP server connection");
                    return (0);
                }
                $data = fgets($this->connection, 100);
            }
            if (GetType($data) != "string"
                || strlen($data) == 0) {
                $this->SetDataAccessError("it was not possible to read line from the HTTP server");
                return (0);
            }
            $line .= $data;
            $length = strlen($line);
            if ($length
                && !strcmp(substr($line, $length - 1, 1), "\n")) {
                $length -= (($length >= 2 && !strcmp(substr($line, $length - 2, 1), "\r")) ? 2 : 1);
                $line = substr($line, 0, $length);
                if ($this->debug)
                    $this->OutputDebug("S $line");
                return ($line);
            }
        }
    }

    Function SetDataAccessError($error, $check_connection = 0)
    {
        $this->error = $error;
        $this->error_code = HTTP_CLIENT_ERROR_COMMUNICATION_FAILURE;
        if (!$this->use_curl
            && function_exists("socket_get_status")) {
            $status = socket_get_status($this->connection);
            if ($status["timed_out"])
                $this->error .= ": data access time out";
            elseif ($status["eof"]) {
                if ($check_connection)
                    $this->error = "";
                else
                    $this->error .= ": the server disconnected";
            }
        }
    }

    Function SetError($error, $error_code = HTTP_CLIENT_ERROR_UNSPECIFIED_ERROR)
    {
        $this->error_code = $error_code;
        return ($this->error = $error);
    }

    Function GetFileDefinition($file, &$definition)
    {
        $name = "";
        if (IsSet($file["FileName"]))
            $name = basename($file["FileName"]);
        if (IsSet($file["Name"]))
            $name = $file["Name"];
        if (strlen($name) == 0)
            return ("it was not specified the file part name");
        if (IsSet($file["Content-Type"])) {
            $content_type = $file["Content-Type"];
            $type = $this->Tokenize(strtolower($content_type), "/");
            $sub_type = $this->Tokenize("");
            switch ($type) {
                case "text":
                case "image":
                case "audio":
                case "video":
                case "application":
                case "message":
                    break;
                case "automatic":
                    switch ($sub_type) {
                        case "name":
                            switch (GetType($dot = strrpos($name, ".")) == "integer" ? strtolower(substr($name, $dot)) : "") {
                                case ".xls":
                                    $content_type = "application/excel";
                                    break;
                                case ".hqx":
                                    $content_type = "application/macbinhex40";
                                    break;
                                case ".doc":
                                case ".dot":
                                case ".wrd":
                                    $content_type = "application/msword";
                                    break;
                                case ".pdf":
                                    $content_type = "application/pdf";
                                    break;
                                case ".pgp":
                                    $content_type = "application/pgp";
                                    break;
                                case ".ps":
                                case ".eps":
                                case ".ai":
                                    $content_type = "application/postscript";
                                    break;
                                case ".ppt":
                                    $content_type = "application/powerpoint";
                                    break;
                                case ".rtf":
                                    $content_type = "application/rtf";
                                    break;
                                case ".tgz":
                                case ".gtar":
                                    $content_type = "application/x-gtar";
                                    break;
                                case ".gz":
                                    $content_type = "application/x-gzip";
                                    break;
                                case ".php":
                                case ".php3":
                                    $content_type = "application/x-httpd-php";
                                    break;
                                case ".js":
                                    $content_type = "application/x-javascript";
                                    break;
                                case ".ppd":
                                case ".psd":
                                    $content_type = "application/x-photoshop";
                                    break;
                                case ".swf":
                                case ".swc":
                                case ".rf":
                                    $content_type = "application/x-shockwave-flash";
                                    break;
                                case ".tar":
                                    $content_type = "application/x-tar";
                                    break;
                                case ".zip":
                                    $content_type = "application/zip";
                                    break;
                                case ".mid":
                                case ".midi":
                                case ".kar":
                                    $content_type = "audio/midi";
                                    break;
                                case ".mp2":
                                case ".mp3":
                                case ".mpga":
                                    $content_type = "audio/mpeg";
                                    break;
                                case ".ra":
                                    $content_type = "audio/x-realaudio";
                                    break;
                                case ".wav":
                                    $content_type = "audio/wav";
                                    break;
                                case ".bmp":
                                    $content_type = "image/bitmap";
                                    break;
                                case ".gif":
                                    $content_type = "image/gif";
                                    break;
                                case ".iff":
                                    $content_type = "image/iff";
                                    break;
                                case ".jb2":
                                    $content_type = "image/jb2";
                                    break;
                                case ".jpg":
                                case ".jpe":
                                case ".jpeg":
                                    $content_type = "image/jpeg";
                                    break;
                                case ".jpx":
                                    $content_type = "image/jpx";
                                    break;
                                case ".png":
                                    $content_type = "image/png";
                                    break;
                                case ".tif":
                                case ".tiff":
                                    $content_type = "image/tiff";
                                    break;
                                case ".wbmp":
                                    $content_type = "image/vnd.wap.wbmp";
                                    break;
                                case ".xbm":
                                    $content_type = "image/xbm";
                                    break;
                                case ".css":
                                    $content_type = "text/css";
                                    break;
                                case ".txt":
                                    $content_type = "text/plain";
                                    break;
                                case ".htm":
                                case ".html":
                                    $content_type = "text/html";
                                    break;
                                case ".xml":
                                    $content_type = "text/xml";
                                    break;
                                case ".mpg":
                                case ".mpe":
                                case ".mpeg":
                                    $content_type = "video/mpeg";
                                    break;
                                case ".qt":
                                case ".mov":
                                    $content_type = "video/quicktime";
                                    break;
                                case ".avi":
                                    $content_type = "video/x-ms-video";
                                    break;
                                case ".eml":
                                    $content_type = "message/rfc822";
                                    break;
                                default:
                                    $content_type = "application/octet-stream";
                                    break;
                            }
                            break;
                        default:
                            return ($content_type . " is not a supported automatic content type detection method");
                    }
                    break;
                default:
                    return ($content_type . " is not a supported file content type");
            }
        } else
            $content_type = "application/octet-stream";
        $definition = array(
            "Content-Type" => $content_type,
            "NAME" => $name
        );

        if (IsSet($file["FileName"])) {
            if (GetType($length = @filesize($file["FileName"])) != "integer") {
                $error = "it was not possible to determine the length of the file " . $file["FileName"];
                if (IsSet($php_errormsg)
                    && strlen($php_errormsg))
                    $error .= ": " . $php_errormsg;
                if (!file_exists($file["FileName"]))
                    $error = "it was not possible to access the file " . $file["FileName"];
                return ($error);
            }
            $definition["FILENAME"] = $file["FileName"];
            $definition["Content-Length"] = $length;
        } elseif (IsSet($file["Data"]))
            $definition["Content-Length"] = strlen($definition["DATA"] = $file["Data"]);
        else
            return ("it was not specified a valid file name");
        return ("");
    }

    Function PickCookies(&$cookies, $secure)
    {
        if (IsSet($this->cookies[$secure])) {
            $now = gmdate("Y-m-d H-i-s");
            for ($domain = 0, Reset($this->cookies[$secure]); $domain < count($this->cookies[$secure]); Next($this->cookies[$secure]), $domain++) {
                $domain_pattern = Key($this->cookies[$secure]);
                $match = strlen($this->request_host) - strlen($domain_pattern);
                if ($match >= 0
                    && !strcmp($domain_pattern, substr($this->request_host, $match))
                    && ($match == 0
                        || $domain_pattern[0] == "."
                        || $this->request_host[$match - 1] == ".")) {
                    for (Reset($this->cookies[$secure][$domain_pattern]), $path_part = 0; $path_part < count($this->cookies[$secure][$domain_pattern]); Next($this->cookies[$secure][$domain_pattern]), $path_part++) {
                        $path = Key($this->cookies[$secure][$domain_pattern]);
                        if (strlen($this->request_uri) >= strlen($path)
                            && substr($this->request_uri, 0, strlen($path)) == $path) {
                            for (Reset($this->cookies[$secure][$domain_pattern][$path]), $cookie = 0; $cookie < count($this->cookies[$secure][$domain_pattern][$path]); Next($this->cookies[$secure][$domain_pattern][$path]), $cookie++) {
                                $cookie_name = Key($this->cookies[$secure][$domain_pattern][$path]);
                                $expires = $this->cookies[$secure][$domain_pattern][$path][$cookie_name]["expires"];
                                if ($expires == ""
                                    || strcmp($now, $expires) < 0)
                                    $cookies[$cookie_name] = $this->cookies[$secure][$domain_pattern][$path][$cookie_name];
                            }
                        }
                    }
                }
            }
        }
    }

    Function FlushData()
    {
        if (!fflush($this->connection)) {
            $this->SetDataAccessError("it was not possible to send data to the HTTP server");
            return (0);
        }
        return (1);
    }

    Function ReadWholeReplyBody(&$body)
    {
        $body = '';
        for (; ;) {
            if (strlen($error = $this->ReadReplyBody($block, $this->file_buffer_length)))
                return ($error);
            if (strlen($block) == 0)
                return ('');
            $body .= $block;
        }
    }

    Function ReadWholeReplyIntoTemporaryFile(&$file)
    {
        if (!($file = tmpfile()))
            return $this->SetPHPError('could not create the temporary file to save the response', $php_errormsg, HTTP_CLIENT_ERROR_CANNOT_ACCESS_LOCAL_FILE);
        for (; ;) {
            if (strlen($error = $this->ReadReplyBody($block, $this->file_buffer_length))) {
                fclose($file);
                return ($error);
            }
            if (strlen($block) == 0) {
                if (@fseek($file, 0) != 0) {
                    $error = $this->SetPHPError('could not seek to the beginning of temporary file with the response', $php_errormsg, HTTP_CLIENT_ERROR_CANNOT_ACCESS_LOCAL_FILE);
                    fclose($file);
                    return $error;
                }
                return ('');
            }
            if (!@fwrite($file, $block)) {
                $error = $this->SetPHPError('could not write to the temporary file to save the response', $php_errormsg, HTTP_CLIENT_ERROR_CANNOT_ACCESS_LOCAL_FILE);
                fclose($file);
                return $error;
            }
        }
    }

    Function GetPersistentCookies(&$cookies, $domain = '', $secure_only = 0)
    {
        $this->SavePersistentCookies($cookies, $domain, $secure_only);
    }

    Function SavePersistentCookies(&$cookies, $domain = '', $secure_only = 0)
    {
        $this->SaveCookies($cookies, $domain, $secure_only, 1);
    }

    Function SaveCookies(&$cookies, $domain = '', $secure_only = 0, $persistent_only = 0)
    {
        $now = gmdate("Y-m-d H-i-s");
        $cookies = array();
        for ($secure_cookies = 0, Reset($this->cookies); $secure_cookies < count($this->cookies); Next($this->cookies), $secure_cookies++) {
            $secure = Key($this->cookies);
            if (!$secure_only
                || $secure) {
                for ($cookie_domain = 0, Reset($this->cookies[$secure]); $cookie_domain < count($this->cookies[$secure]); Next($this->cookies[$secure]), $cookie_domain++) {
                    $domain_pattern = Key($this->cookies[$secure]);
                    $match = strlen($domain) - strlen($domain_pattern);
                    if (strlen($domain) == 0
                        || ($match >= 0
                            && !strcmp($domain_pattern, substr($domain, $match))
                            && ($match == 0
                                || $domain_pattern[0] == "."
                                || $domain[$match - 1] == "."))) {
                        for (Reset($this->cookies[$secure][$domain_pattern]), $path_part = 0; $path_part < count($this->cookies[$secure][$domain_pattern]); Next($this->cookies[$secure][$domain_pattern]), $path_part++) {
                            $path = Key($this->cookies[$secure][$domain_pattern]);
                            for (Reset($this->cookies[$secure][$domain_pattern][$path]), $cookie = 0; $cookie < count($this->cookies[$secure][$domain_pattern][$path]); Next($this->cookies[$secure][$domain_pattern][$path]), $cookie++) {
                                $cookie_name = Key($this->cookies[$secure][$domain_pattern][$path]);
                                $expires = $this->cookies[$secure][$domain_pattern][$path][$cookie_name]["expires"];
                                if ((!$persistent_only
                                        && strlen($expires) == 0)
                                    || (strlen($expires)
                                        && strcmp($now, $expires) < 0))
                                    $cookies[$secure][$domain_pattern][$path][$cookie_name] = $this->cookies[$secure][$domain_pattern][$path][$cookie_name];
                            }
                        }
                    }
                }
            }
        }
    }

    Function RestoreCookies($cookies, $clear = 1)
    {
        $new_cookies = ($clear ? array() : $this->cookies);
        for ($secure_cookies = 0, Reset($cookies); $secure_cookies < count($cookies); Next($cookies), $secure_cookies++) {
            $secure = Key($cookies);
            if (GetType($secure) != "integer")
                return ($this->SetError("invalid cookie secure value type (" . serialize($secure) . ")", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
            for ($cookie_domain = 0, Reset($cookies[$secure]); $cookie_domain < count($cookies[$secure]); Next($cookies[$secure]), $cookie_domain++) {
                $domain_pattern = Key($cookies[$secure]);
                if (GetType($domain_pattern) != "string")
                    return ($this->SetError("invalid cookie domain value type (" . serialize($domain_pattern) . ")", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
                for (Reset($cookies[$secure][$domain_pattern]), $path_part = 0; $path_part < count($cookies[$secure][$domain_pattern]); Next($cookies[$secure][$domain_pattern]), $path_part++) {
                    $path = Key($cookies[$secure][$domain_pattern]);
                    if (GetType($path) != "string"
                        || strcmp(substr($path, 0, 1), "/"))
                        return ($this->SetError("invalid cookie path value type (" . serialize($path) . ")", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
                    for (Reset($cookies[$secure][$domain_pattern][$path]), $cookie = 0; $cookie < count($cookies[$secure][$domain_pattern][$path]); Next($cookies[$secure][$domain_pattern][$path]), $cookie++) {
                        $cookie_name = Key($cookies[$secure][$domain_pattern][$path]);
                        $expires = $cookies[$secure][$domain_pattern][$path][$cookie_name]["expires"];
                        $value = $cookies[$secure][$domain_pattern][$path][$cookie_name]["value"];
                        if (GetType($expires) != "string"
                            || (strlen($expires)
                                && !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}\$/", $expires)))
                            return ($this->SetError("invalid cookie expiry value type (" . serialize($expires) . ")", HTTP_CLIENT_ERROR_INVALID_PARAMETERS));
                        $new_cookies[$secure][$domain_pattern][$path][$cookie_name] = array(
                            "name" => $cookie_name,
                            "value" => $value,
                            "domain" => $domain_pattern,
                            "path" => $path,
                            "expires" => $expires,
                            "secure" => $secure
                        );
                    }
                }
            }
        }
        $this->cookies = $new_cookies;
        return ("");
    }
}

;

?>

