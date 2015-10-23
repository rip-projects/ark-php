<?php

/**
 * google.php
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
 * Description of google
 *
 * @author jafar
 */
class google {

    var $_obj;
    var $auth_url = 'https://accounts.google.com/o/oauth2/auth';
    var $token_url = 'https://accounts.google.com/o/oauth2/token';
    var $user_url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=1';
    var $client_id = '';
    var $client_secret = '';
    var $default_scope = '';
    var $_enable_debug = false;

    function __construct() {
        $this->_obj = & get_instance();

        $this->_obj->load->library('session');
        $this->_obj->load->config('google');
        $this->_obj->load->helper('url');

        $this->client_id = $this->_obj->config->item('google_client_id');
        $this->client_secret = $this->_obj->config->item('google_client_secret');
        $this->default_scope = $this->_obj->config->item('google_default_scope');

        $this->connection = new googleConnection($this);
        $this->session = new googleSession($this);
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
        } catch (googleException $e) {
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
        } catch (googleException $e) {
            $this->_errors[] = $e;

            if ($this->_enable_debug) {
                log_message('error', print_r($e, true));
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

class googleConnection {

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
                    $response = new googleResponse((object) $this->_responses[$key]);

                    if ($response->__resp->code !== 200) {
                        $error = $response->__resp->code . ' | Request Failed';

                        if (isset($response->__resp->data->error->type)) {
                            $error .= ' - ' . $response->__resp->data->error->type . ' - ' . $response->__resp->data->error->message;
                        }

                        throw new googleException($error);
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

class googleResponse {

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

class googleException extends Exception {

    function __construct($string) {
        parent::__construct($string);
    }

    public function __toString() {
        return "exception '" . __CLASS__ . "' with message '" . $this->getMessage() . "' in " . $this->getFile() . ":" . $this->getLine() . "\nStack trace:\n" . $this->getTraceAsString();
    }

}

class googleSession {

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
        $url = $this->_lib->auth_url . "?client_id=" . $this->_lib->client_id . '&redirect_uri=' . urlencode($this->_get('callback')) . '&response_type=code';

        if (empty($scope)) {
            $scope = $this->_get('scope');
        } else {
            $this->_set('scope', $scope);
        }

        if (!empty($scope)) {
            $url .= '&scope=' . $scope;
        }

        return $url;
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
        if (empty($token))
            return NULL;

        // $user = $this->_get('user');
        // if ( !empty($user) ) return $user;

        try {
            $url = $this->_lib->user_url . '&' . $this->_token_string();
            $user = $this->connection->get($url);
            if (!empty($user) && !empty($user->author)) {
                $user = $user->author;
            } else {
                $user = '';
            }
        } catch (googleException $e) {
            $this->logout();
            return NULL;
        }

        // $this->_set('user', $user);
        return $user;
    }

    private function _find_token() {
        $token = unserialize(serialize($this->_get('token')));

        if (!empty($token)) {

            if (!empty($token->expires_in) && intval($token->expires_in) < time()) {
                // Problem, token is expired!
                return $this->logout();
            }

            $this->_set('token', $token);
            return $this->_token_string();
        }

        if (!isset($_GET['code'])) {
            return $this->logout();
        }

        if (!$this->_get('callback')) {
            $this->_set('callback', current_url());
        }

        try {
            $data = array(
                'code' => $_GET['code'],
                'client_id' => $this->_lib->client_id,
                'client_secret' => $this->_lib->client_secret,
                'redirect_uri' => $this->_get('callback'),
                'grant_type' => 'authorization_code',
            );

            $token = $this->connection->post($this->_lib->token_url, $data);
            $token = $token->__resp->data;
        } catch (googleException $e) {
            $this->logout();
            redirect($this->_strip_query());
            return NULL;
        }

        $this->_unset('callback');

        if ($token->access_token) {
            $expires = $token->expires_in;
            if (!empty($expires)) {
                $token->expires_in = strval(time() + intval($expires));
            }
            $this->_set('token', $token);
            redirect($this->_strip_query());
        }

        return $this->_token_string();
    }

    private function _get($key) {
        $key = '_google_' . $key;
        return $this->_obj->session->userdata($key);
    }

    private function _set($key, $data) {
        $key = '_google_' . $key;
        $this->_obj->session->set_userdata($key, $data);
    }

    private function _unset($key) {
        $key = '_google_' . $key;
        $this->_obj->session->unset_userdata($key);
    }

    public function set_callback($url = NULL) {
        $this->_set('callback', $this->_strip_query($url));
    }

    private function _token_string() {
        return 'access_token=' . $this->_get('token')->access_token;
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
            $url = ( isset($_SERVER['HTTPS'])  && $_SERVER['HTTPS'] === 'off' ) ? 'http' : 'https';
            $url .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        $parts = explode('?', $url);
        return $parts[0];
    }

}