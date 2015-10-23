<?php

/**
 * Xldap.php
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

class Xldap_Exception extends Exception {
    public function __construct($message, $code = 1, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class xldap {
    var $hostname = '';
    var $port = 389;
    var $protocol_version = 3;
    var $base_dn = '';
    var $user_dn = '';
    var $password = '';
    var $user_base = '';
    var $query = '';
    var $fields = '';

    var $conn;

    function  __construct($params = array()) {
        if (!function_exists('ldap_connect')) {
            throw new Exception('PHP LDAP module not loaded yet!');
        }
        $this->initialize($params);
    }

    function initialize($params = array()) {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }

        $this->conn = ldap_connect($this->hostname, $this->port);
        if (!$this->conn) {
            throw new Xldap_Exception('Could not connect to '.$this->hostname);
        }

        ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, $this->protocol_version);

        $this->auth();
    }

    function auth($rdn = '', $pass = '') {
        if ($rdn === '') {
            $rdn = $this->user_dn;
        } else {
            $rdn = $this->search_user_rdn($rdn);
        }
        $pass = ($pass === '') ? $this->password : $pass;

        $bind = @ldap_bind($this->conn, $rdn, $pass);

        if (!$bind) {
            throw new Xldap_Exception('Invalid credentials with rdn '.$this->user_dn);
        }
    }

    function search_user_rdn($rdn) {
        $sr = ldap_search($this->conn,$this->user_base,sprintf($this->query, $rdn),$this->fields);
        $records = ldap_get_entries($this->conn, $sr);

        if ($records["count"] != "1") {
            throw new Xldap_Exception('Wrong user '.$rdn);
        }

        return $records[0]["dn"];
    }

    function get_user($rdn) {
        $sr = ldap_search($this->conn,$this->user_base,sprintf($this->query, $rdn),array());
        $records = ldap_get_entries($this->conn, $sr);

        if (empty($records["count"])) {
            return array();
        } else {
            $user = array();
            foreach($records[0] as $key => $value) {
                if (is_array($value)) {
                    $arr = array();
                    if ($value['count'] > 1) {
                        $arr = array();
                        for($i = 0; $i < $value['count']; $i++) {
                            $arr[] = $value[$i];
                        }
                        $user[$key] = $arr;
                    } else {
                        $user[$key] = $value[0];
                    }
                }
            }
            return $user;
        }
    }

    function change_passwd($rdn, $pass, $new_pass) {
        $rdn = $this->search_user_rdn($rdn);
        $this->auth($rdn, $pass);
        $entry = array();
        $entry["userPassword"] = "{SHA}" . base64_encode( pack( "H*", sha1( $new_pass ) ) );
        if (ldap_modify($this->conn, $rdn, $entry) === false) {
            throw new Xldap_Exception('Your password cannot be change, please contact the administrator');
        }
    }
}
