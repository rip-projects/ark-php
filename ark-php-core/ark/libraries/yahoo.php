<?php

/**
 * yahoo.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

/**
 * Description of yahoo
 *
 * @author jafar
 */
class yahoo {

    var $_obj;
    var $request_token_url = 'https://api.login.yahoo.com/oauth/v2/get_request_token';
    var $auth_url = 'https://api.login.yahoo.com/oauth/v2/request_auth';
    var $token_url = 'https://api.login.yahoo.com/oauth/v2/get_token';
    var $user_url = 'http://social.yahooapis.com/v1/user/me/profile/usercard';
    var $consumer_key = '';
    var $consumer_secret = '';
    var $default_scope = '';
    var $_enable_debug = false;

    function __construct() {
        $this->_obj = & get_instance();

        $this->_obj->load->library('session');
        $this->_obj->load->config('yahoo');
        $this->_obj->load->helper('url');

        $this->consumer_key = $this->_obj->config->item('yahoo_consumer_key');
        $this->consumer_secret = $this->_obj->config->item('yahoo_consumer_secret');
        $this->default_scope = $this->_obj->config->item('yahoo_default_scope');

        $this->connection = new yahooConnection($this);
        $this->session = new yahooSession($this);
    }

    public function logged_in() {
        return $this->session->logged_in();
    }

    public function login($scope = NULL) {
        return $this->session->login($scope);
    }

    public function login_url($scope = NULL) {
        return $this->session->login_url($scope);
    }

    public function logout() {
        return $this->session->logout();
    }

    public function user() {
        return $this->session->get();
    }

    public function call($method, $uri, $data = array()) {
        $response = FALSE;

        try {
            switch ($method) {
                case 'get':
                    $response = $this->connection->get($this->append_token($this->api_url . $uri), $data);
                    break;

                case 'post':
                    $response = $this->connection->post($this->append_token($this->api_url . $uri), $data);
                    break;
            }
        } catch (yahooException $e) {
            $this->_errors[] = $e;

            if ($this->_enable_debug) {
                echo $e;
            }
        }

        return $response;
    }

    public function simple_call($method, $uri, $data = array()) {
        $response = FALSE;

        try {
            $url = $this->append_token($uri);
            switch ($method) {
                case 'get':
                    $response = $this->connection->get($url, $data);
                    break;

                case 'post':
                    $response = $this->connection->post($url, $data);
                    break;
            }
        } catch (yahooException $e) {
            $this->_errors[] = $e;

            if ($this->_enable_debug) {
                log_message('error', print_r($e, 1));
            }
        }

        return $response;
    }

    public function errors() {
        return $this->_errors;
    }

    public function last_error() {
        if (count($this->_errors) == 0)
            return NULL;

        return $this->_errors[count($this->_errors) - 1];
    }

    public function append_token($url) {
        return $this->session->append_token($url);
    }

    public function set_callback($url) {
        return $this->session->set_callback($url);
    }

    public function enable_debug($debug = TRUE) {
        $this->_enable_debug = (bool) $debug;
    }

}

class yahooConnection {

    // Allow multi-threading.

    private $_mch = NULL;
    private $_properties = array();
    private $_lib;

    function __construct(&$lib) {
        $this->_lib = $lib;

        $this->_mch = curl_multi_init();

        $this->_properties = array(
            'code' => CURLINFO_HTTP_CODE,
            'time' => CURLINFO_TOTAL_TIME,
            'length' => CURLINFO_CONTENT_LENGTH_DOWNLOAD,
            'type' => CURLINFO_CONTENT_TYPE
        );
    }

    private function _initConnection($url) {
        $this->_ch = curl_init($url);
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, TRUE);
    }

    public function get($url, $params = array()) {
        if (count($params) > 0) {
            $url .= ( strpos($url, '?') === FALSE) ? '?' : '&';

            foreach ($params as $k => $v) {
                $url .= "{$k}={$v}&";
            }

            $url = substr($url, 0, -1);
        }

        $this->_initConnection($url);
        $response = $this->_addCurl($url, $params);

        return $response;
    }

    function _base_string($url, $data = array()) {
        ksort($data);

        $param = array();
        foreach ($data as $k => $v) {
            $param[] = $k . '=' . $v;
        }

        $a = array(
            'POST',
            $url,
            implode('&', $param),
        );

        $a1 = '';
        foreach ($a as $v) {
            $a1[] = urlencode($v);
        }
        return implode('&', $a1);
    }

    function _get_oauth_signature($url, $secret, $data) {
        $base_string = $this->_base_string($url, $data);
        return base64_encode(hash_hmac('sha1', $base_string, $secret, true));
    }

    public function post_auth($url, $secret, $data = array()) {
        $data['oauth_signature'] = $this->_get_oauth_signature($url, $secret, $data);

        $params = array();
        foreach ($data as $k => $v) {
            $params[] = $k . '=' . urlencode($v);
        }

        return $this->post($url, $data);
    }

    public function get_auth($url, $secret, $data = array()) {
        $data['oauth_signature_method'] = 'HMAC-SHA1';
        $data['oauth_signature'] = $this->_get_oauth_signature($url, $secret, $data);

        $params = array();
        foreach ($data as $k => $v) {
            if ($k == 'oauth_signature') {
                $params[] = $k . '=' . urlencode($v);
            } else {
                $params[] = $k . '=' . $v;
            }

        }

        try {
            $result = $this->get($url, $data);
        } catch (Exception $e) {
            log_message('error', print_r($e, 1));
        }
        return $result;
    }

    public function post($url, $params = array()) {
        // Todo
        $post = '';

        foreach ($params as $k => $v) {
            $post .= "{$k}={$v}&";
        }

        $post = substr($post, 0, -1);

        $this->_initConnection($url, $params);
        curl_setopt($this->_ch, CURLOPT_POST, 1);
        curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $post);

        $response = $this->_addCurl($url, $params);

        return $response;
    }

    private function _addCurl($url, $params = array()) {
        $ch = $this->_ch;

        $key = (string) $ch;
        $this->_requests[$key] = $ch;

        $response = curl_multi_add_handle($this->_mch, $ch);
        if ($response === CURLM_OK || $response === CURLM_CALL_MULTI_PERFORM) {
            do {
                $mch = curl_multi_exec($this->_mch, $active);
            } while ($mch === CURLM_CALL_MULTI_PERFORM);

            return $this->_getResponse($key);
        } else {
            return $response;
        }
    }

    private function _getResponse($key = NULL) {
        if ($key == NULL)
            return FALSE;

        if (isset($this->_responses[$key])) {
            return $this->_responses[$key];
        }

        $running = NULL;

        do {
            $response = curl_multi_exec($this->_mch, $running_curl);

            if ($running !== NULL && $running_curl != $running) {
                $this->_setResponse($key);

                if (isset($this->_responses[$key])) {
                    $response = new yahooResponse((object) $this->_responses[$key]);

                    if ($response->__resp->code !== 200) {
                        $error = $response->__resp->code . ' | Request Failed';

                        if (isset($response->__resp->data->error->type)) {
                            $error .= ' - ' . $response->__resp->data->error->type . ' - ' . $response->__resp->data->error->message;
                        }

                        throw new yahooException($error);
                    }

                    return $response;
                }
            }

            $running = $running_curl;
        } while ($running_curl > 0);
    }

    private function _setResponse($key) {
        while ($done = curl_multi_info_read($this->_mch)) {
            $key = (string) $done['handle'];
            $this->_responses[$key]['data'] = curl_multi_getcontent($done['handle']);

            foreach ($this->_properties as $curl_key => $value) {
                $this->_responses[$key][$curl_key] = curl_getinfo($done['handle'], $value);

                curl_multi_remove_handle($this->_mch, $done['handle']);
            }
        }
    }

}

class yahooResponse {

    private $__construct;

    public function __construct($resp) {
        $this->__resp = $resp;

        $type = explode(';', $this->__resp->type);
        foreach ($type as $key => $val) {
            $type[$key] = trim($type[$key]);
        }
        if (strpos($type[0], 'xml') !== false) {
            $data = simplexml_load_string($this->__resp->data);
//            $json = json_encode($xml);
//            $data = json_decode($json);
        } else {
            $data = json_decode($this->__resp->data);
        }

        if ($data !== NULL) {
            $this->__resp->data = $data;
        }
    }

    public function __get($name) {
        if ($this->__resp->code < 200 || $this->__resp->code > 299)
            return FALSE;

        $result = array();

        if (is_string($this->__resp->data)) {
            parse_str($this->__resp->data, $result);
            $this->__resp->data = (object) $result;
        }

        if ($name === '_result') {
            return $this->__resp->data;
        }

        return $this->__resp->data->$name;
    }

}

class yahooException extends Exception {

    function __construct($string) {
        parent::__construct($string);
    }

    public function __toString() {
        return "exception '" . __CLASS__ . "' with message '" . $this->getMessage() . "' in " . $this->getFile() . ":" . $this->getLine() . "\nStack trace:\n" . $this->getTraceAsString();
    }

}

class yahooSession {

    private $client_id;
    private $client_secret;
    private $_lib;

    function __construct(&$lib) {
        $this->_lib = $lib;
        $this->_obj = & get_instance();

        $this->connection = &$this->_lib->connection;

        $this->_set('scope', $this->_lib->default_scope);

        if (!$this->logged_in()) {
            // Initializes the callback to this page URL.
            $this->set_callback();
        }
    }

    public function logged_in() {
        return ( $this->get() === NULL ) ? FALSE : TRUE;
    }

    public function logout() {
        $this->_unset('token');
        $this->_unset('user');
    }

    public function login_url($scope = NULL) {

        $data = array(
            'oauth_nonce' => md5(uniqid()),
            'oauth_timestamp' => time(),
            'oauth_consumer_key' => $this->_lib->consumer_key,
//            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_signature_method' => 'PLAINTEXT',
            'oauth_signature' => $this->_lib->consumer_secret . '%26',
            'oauth_version' => '1.0',
            'xoauth_lang_pref' => 'en-us',
            'oauth_callback' => $this->_get('callback'),
//            'oauth_callback' => 'oob',
        );

//        $secret = $this->_lib->consumer_secret . '&';

        $params = array();
        foreach ($data as $k => $v) {
            $params[] = $k . '=' . $v;
        }
        $url = $this->_lib->request_token_url; // . '?' . implode('&', $params);
//        $result = $this->connection->post_auth($url, $secret, $data);
        $result = $this->connection->post($url, $data);
        $result = $result->__resp->data;

        $a = explode('&', $result);
        $b = array();
        foreach ($a as $val) {
            $c = explode('=', $val);
            $b[$c[0]] = $c[1];
        }
        $this->_set('oauth_token_secret', $b['oauth_token_secret']);

//        if (empty($scope)) {
//            $scope = $this->_get('scope');
//        } else {
//            $this->_set('scope', $scope);
//        }
//
//        if (!empty($scope)) {
//            $url .= '&scope=' . $scope;
//        }

        return $this->_lib->auth_url . '?' . $result;
    }

    public function login($scope = NULL) {
        $this->logout();

        if (!$this->_get('callback'))
            $this->_set('callback', current_url());

        $url = $this->login_url($scope);

        return redirect($url);
    }

    public function get() {
        $token = $this->_find_token();
        if (empty($token)) {
            return NULL;
        }

        // $user = $this->_get('user');
        // if ( !empty($user) ) return $user;

        try {

            $data = array(
                'realm' => 'yahooapis.com',
                'oauth_consumer_key' => $this->_lib->consumer_key,
                'oauth_nonce' => md5(uniqid()),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_timestamp' => time(),
                'oauth_token' => $this->_get('token')->oauth_token,
                'oauth_version' => '1.0',
//                'oauth_signature' => $this->_lib->consumer_secret . '%26' . $this->_get('oauth_token_secret'),
            );
            $secret = $this->_lib->consumer_secret . '&' . $this->_get('oauth_token_secret');

//            $result = $this->connection->get_auth($this->_lib->user_url, $secret, $data);
            $result = $this->connection->get_auth('http://social.yahooapis.com/v1/user/abcdef123/profile', $secret, $data);

            ksort($data);

            $param = array();
            foreach ($data as $k => $v) {
                $param[] = $k . '=' . $v;
            }

            $a = array(
                'POST',
                $this->_lib->user_url,
                implode('&', $param),
            );

            $a1 = '';
            foreach ($a as $v) {
                $a1[] = urlencode($v);
            }
            $base_string = implode('&', $a1);

            $a = base64_encode(hash_hmac('sha1', $base_string, $secret, true));
            $data['oauth_signature'] = $a;

            try {
                $params = array();
                foreach ($data as $k => $v) {
                    $params[] = $k . '=' . urlencode($v);
                }
                $user = $this->connection->get($this->_lib->user_url, $data);
            } catch (Exception $e) {
                log_message('error', print_r($e, 1));
            }
        } catch (yahooException $e) {
            $this->logout();
            return NULL;
        }

        // $this->_set('user', $user);
        return $user;
    }

    private function _find_token() {
        $token = unserialize(serialize($this->_get('token')));

        if (!empty($token)) {
            if (!empty($token->oauth_expires_in) && intval($token->oauth_expires_in) < time()) {
                // Problem, token is expired!
                return $this->logout();
            }

            $this->_set('token', $token);
            return $this->_token_string();
        }

        if (!isset($_GET['oauth_token']) || !isset($_GET['oauth_verifier'])) {
            return $this->logout();
        }

        if (!$this->_get('callback')) {
            $this->_set('callback', current_url());
        }

        try {
            $data = array(
                'oauth_consumer_key' => $this->_lib->consumer_key,
                'oauth_signature_method' => 'PLAINTEXT',
                'oauth_version' => '1.0',
                'oauth_verifier' => $_GET['oauth_verifier'],
                'oauth_token' => $_GET['oauth_token'],
                'oauth_timestamp' => time(),
                'oauth_nonce' => md5(uniqid()),
                'oauth_signature' => $this->_lib->consumer_secret . '%26' . $this->_get('oauth_token_secret'),
            );

            $a = $this->connection->post($this->_lib->token_url, $data);
            $a = explode('&', $a->__resp->data);
            $token = new stdClass();
            foreach ($a as $val) {
                $c = explode('=', $val);
                $token->{$c[0]} = (empty($c[1])) ? '' : $c[1];
            }
        } catch (yahooException $e) {
            $this->logout();
            redirect($this->_strip_query());
            return NULL;
        }

        $this->_unset('callback');
        if ($token->oauth_token) {
            $expires = $token->oauth_expires_in;
            if (!empty($expires)) {
                $token->oauth_expires_in = strval(time() + intval($expires));
            }
            $this->_set('token', $token);
            redirect($this->_strip_query());
        }

        return $this->_token_string();
    }

    private function _get($key) {
        $key = '_yahoo_' . $key;
        return $this->_obj->session->userdata($key);
    }

    private function _set($key, $data) {
        $key = '_yahoo_' . $key;
        $this->_obj->session->set_userdata($key, $data);
    }

    private function _unset($key) {
        $key = '_yahoo_' . $key;
        $this->_obj->session->unset_userdata($key);
    }

    public function set_callback($url = NULL) {
        $this->_set('callback', $this->_strip_query($url));
    }

    private function _token_string() {
        return 'oauth_token=' . $this->_get('token')->oauth_token;
    }

    public function append_token($url) {
        if ($this->_get('token')) {
            $url .= ( strpos($url, '?') === FALSE) ? '?' : '&';
            $url .= $this->_token_string();
        }

        return $url;
    }

    private function _strip_query($url = NULL) {
        if ($url === NULL) {
            $url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'off' ) ? 'http' : 'https';
            $url .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        $parts = explode('?', $url);
        return $parts[0];
    }

}