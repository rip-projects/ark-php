<?php

/**
 * base_user_model.php
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

class base_user_model extends app_base_model {

    var $CACHE_KEY_PRIVILEGE = 'USER_PRIVILEGE';
    var $CACHE_TIMEOUT_PRIVILEGE = 86400;

    function __construct() {
        parent::__construct();

        $this->hooks->add_hook('user:post_login', function(&$user = null) {
            if (!isset($user)) {
                return;
            }
            $CI =& get_instance();
            $that = $CI->_model('user');
            if (!empty($user['id'])) {
                // add roles
                $sql = '
                    SELECT g.*, g.id role_id FROM ' . $that->_db()->dbprefix . 'user_role ug
                    JOIN ' . $that->_db()->_protect_identifiers($that->_db()->dbprefix . 'role') . ' g ON ug.role_id = g.id
                    WHERE user_id = ?
                    AND g.status = 1
                ';
                $user['roles'] = $that->_db()->query($sql, array($user['id']))->result_array();

                // add organization
                $sql = '
                    SELECT o.*, o.id org_id FROM ' . $that->_db()->dbprefix . 'user_organization uo
                    JOIN ' . $that->_db()->dbprefix . 'organization o ON uo.org_id = o.id
                    WHERE user_id = ?
                ';
                $user['organization'] = $that->_db()->query($sql, array($user['id']))->result_array();

                $user['is_top_member'] = false;
                foreach($user['organization'] as $org) {
                    if ($org['id'] == 1) {
                        $user['is_top_member'] = true;
                        break;
                    }
                }

                $d = array(
                    'last_login' => date('Y-m-d H:i:s'),
                );
                $user['last_login'] = $d['last_login'];

                $that->save($d, $user['id']);
            }
        });
    }

    function get_login($filters) {
        $CI = &get_instance();
        $sql = 'SELECT * FROM ' . $this->_name . ' WHERE status = 1';
        foreach($filters as $field => $filter) {
            if ($field == 'login') {
                $sql .= ' AND (username = ? OR email = ?) ';
                $params[] = $filter;
                $params[] = $filter;
            } elseif ($field == 'password') {
                $sql .= ' AND password = ? ';
                $params[] = md5($filter);
            } else {
                $sql .= ' AND '.$field.' = ? ';
                $params[] = $filter;
            }
        }
        $sql .= ' LIMIT 1';
        $user = $this->_db()->query($sql, $params)->row_array();

        $this->hooks->call_hook('user:post_login', $user);
        return $user;
    }

    // FIXME reekoheek: seharusnya ga pake login_chat lagi
    // function login_chat($login, $password) {
    //     $CI = &get_instance();
    //     $names = explode('/', $login);
    //     if (strpos($names[0], '@gmail.com') !== FALSE) {
    //         $user = $this->_db()->query('SELECT * FROM user WHERE gtalk_id = ?', array($names[0]))->row_array();
    //     } else {
    //         $user = $this->_db()->query('SELECT * FROM user WHERE ym_id = ?', array($names[0]))->row_array();
    //     }
    //     if (empty($user)) {
    //         return false;
    //     }
    //     return $user;
    // }

    function refetch_user($old_user) {
        $user = $this->_db()->query('SELECT * FROM user WHERE id = ?', array($old_user['id']))->row_array();

        $this->hooks->call_hook('user:post_login', $user);
        if (!empty($user)) {
            $user['login_mode'] = $old_user['login_mode'];
        }
        return $user;
    }

    function before_save(&$data, $id = null) {
        parent::before_save($data, $id);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = md5($data['password']);
        }
    }

    function add_user_role($user_id, $role_id) {
        $user_role = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
        );
        $this->before_save($user_role);
        $this->_db()->insert('user_role', $user_role);
    }

    function update_user_role($user_id, $role_ids) {
        $this->_db()->delete('user_role', array('user_id' => $user_id));

        if (!empty($role_ids)) {
            if (!is_array($role_ids)) {
                $role_ids = array($role_ids);
            }

            foreach ($role_ids as $role_id) {
                if (!empty($role_id)) {
                    $role_id = (empty($role_id)) ? 0 : $role_id;
                    $user_role = array(
                        'user_id' => $user_id,
                        'role_id' => $role_id,
                    );
                    Base_Model::before_save($user_role);
                    $this->_db()->insert('user_role', $user_role);
                }
            }
        }
    }

    function get_by_username($username) {
        $sql = 'SELECT * FROM user WHERE (username = ? OR email = ?) AND status = 1';
        $row = $this->_db()->query($sql, array($username, $username))->row_array();
        $this->hooks->call_hook('user:post_login', $row);
        return $row;

    }

    function privilege($uri, $user_id = '') {
        $CI = &get_instance();

        if (!$this->_db()) return true;

        if (empty($user_id)) {
            $user = $CI->auth->get_user();
            $user_id = $user['id'];
        }

        $uri = explode('?', $uri);
        $uri = $uri[0];

        $uri = explode('/', $uri);
        $controller = $uri[0];

        $uri = array_slice($uri, 0, 2);
        $uri = implode('/', $uri);

        $privileges = $CI->cache->context_get($this->CACHE_KEY_PRIVILEGE);
        if ($privileges === FALSE) {
            $privileges = array();
            $result = $this->_db()->where('status', 1)->get('privilege_user')->result_array();
            foreach ($result as $row) {
                $privileges[$row['user_id']][$row['uri']] = 1;
            }
            $CI->cache->context_save($this->CACHE_KEY_PRIVILEGE, $privileges, $this->CACHE_TIMEOUT_PRIVILEGE);
        }

        if (isset($privileges) && isset($privileges[$user_id]) &&
            (isset($privileges[$role_id]['*']) || isset($privileges[$role_id][$controller.'/*']) || isset($privileges[$role_id][$uri]))) {
            return true;
        }

        return $CI->_model('role')->privilege($uri);
    }

    function register($data) {
        $id = $this->save($data);
        // FIXME hardcoded role_id should be fix to fetch from model
        $this->add_user_role($id, 2);
    }

    function add_trail($activity = '') {
        $CI = &get_instance();

        $user = $CI->auth->get_user();

        if (empty($user['id'])) {
            return;
        }

        $data = (!empty($_REQUEST)) ? json_encode($_REQUEST) : '';
        if (!empty($data['password'])) {
            $data['password'] = '***';
        }

        $trail = array(
            'user_id' => $user['id'],
            'controller' => $CI->uri->rsegments[1] . '/' . $CI->uri->rsegments[2],
            'uri' => $CI->uri->original_uri,
            'method' => $_SERVER['REQUEST_METHOD'],
            'data' => $data,
            'ip_address' => $CI->input->ip_address(),
            'user_agent' => $CI->input->user_agent(),
            'location' => $CI->input->location(),
            'is_login' => $user['is_login'],
            'activity' => $activity,
            'created_time' => date('Y-m-d H:i:s'),
            'created_by' => $user['username'],
        );

        $f = @fopen(APPPATH . 'logs/user_trail-' . (date('Y-m-d')) . '.log.php', 'a+');
        if ($f) {
            fputcsv($f, $trail);
            fclose($f);
        }
    }

    function generate_activation_code($id) {
        $user = $this->get($id);
        $code = uniqid($user['username'] . '_');
        $data = array(
            'activation' => $code,
        );
        $this->save($data, $id);
        return $code;
    }

    function save($data, $id = null) {
        $this->hooks->add_hook('user:post_save', function($data) {
            $CI =& get_instance();
            if (!empty($data['data']['roles'])) {
                $CI->_model()->update_user_role($data['id'], $data['data']['roles']);
            }

            if (!empty($data['data']['groups'])) {

                $CI->db->where('user_id', $data['id'])->delete('user_group');
                foreach($data['data']['groups'] as $group) {
                    $CI->db->insert('user_group', array(
                        'user_id' => $data['id'],
                        'group_id' => $group,
                    ));
                }
            }

            if (!empty($data['data']['organizations'])) {
                $CI->_model()->update_user_org($data['id'], $data['data']['organizations']);
            }
        });
        return parent::save($data, $id);
    }

    function find($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        $params = array();
        $where_str = '';
        $order_str = '';
        $limit_str = '';
        $wheres = array();
        $filter_wheres = array();
        $orders = array();

        $select_count = 'SELECT COUNT(t.id) count';
        $select = 'SELECT t.*';

        $user = $this->auth->get_user();
        if (!$user['is_top_member']) {
            if (isset($user['organization'][0])) {
                $wheres[] = 'uo.org_id = ?';
                $params[] = $user['organization'][0]['id'];
            } else {
                $wheres[] = '0';
            }

        }

        $wheres[] = 't.status != 0';

        if (!empty($filter) && !is_array($filter)) {
            $wheres[] = 'id = ?';
            $params[] = $filter;
        } elseif (is_array($filter)) {
            unset($filter['q']);
            foreach($filter as $k => $f) {
                if ($k == 'name') {
                    $filter_wheres[] = 'CONCAT(first_name, " ", last_name) LIKE ?';
                } else {
                    $filter_wheres[] = '`'.$k.'` LIKE ?';
                }
                $params[] = '%'.$this->db->escape_like_str($f).'%';
            }
            if (!empty($filter_wheres)) {
                $wheres[] = '('. implode(' OR ', $filter_wheres) .')';
            }
        }


        if (!empty($wheres)) {
            $where_str = ' WHERE '.implode(' AND ', $wheres);
        }

        if (!empty($sort) && is_array($sort)) {
            foreach ($sort as $key => $value) {
                $orders[] = $key.' '.(($value) ? $value : 'ASC');
            }
            $order_str = ' ORDER BY '.implode(', ', $orders);
        }

        $table_name = $this->_db()->_protect_identifiers($this->_db()->dbprefix . $this->_name).' t ';
        $table_name .= ' LEFT JOIN user_organization uo ON t.id = uo.user_id ';
        $table_name .= ' LEFT JOIN user_group ug ON t.id = ug.user_id ';
        $sql = ' FROM '.$table_name.$where_str.$order_str;
        $count = $this->_db()->query($select_count.$sql, $params)->row()->count;

        if (!empty($limit)) {
            $limit_str = ' LIMIT ?, ?';
            $params[] = intval($offset);
            $params[] = intval($limit);
        }
        $result = $this->_db()->query($select.$sql.$limit_str, $params)->result_array();
        return $result;
    }

}
