<?php

/**
 * xmenu.php
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

class xmenu {

    var $xmenu_items = array();
    var $xmenu_source = 'inline';
    var $home_url = '';
    var $ci;

    function __construct($params = array()) {
        $this->ci = &get_instance();

        $source = explode(':', trim($params['xmenu_source']));
        switch($source[0]) {
            case 'model':
                if (method_exists($this->ci->_model($source[1]), $source[2])) {
                    $params['xmenu_items'] = $this->ci->_model($source[1])->{$source[2]}();
                }
                break;
            default:
                break;
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
        if ($this->ci->config->item('use_db')) {
            $this->xmenu_items = $this->_check_access($this->xmenu_items);
        }
    }

    function _check_access($menus) {
        if (empty($menus)) {
            return array();
        }

        $new_menus = array();
        foreach($menus as $menu) {
            $show = false;
            if (!empty($menu['uri'])) {
                if ($this->ci->_model('user')->privilege($menu['uri'])) {
                    $show = true;
                }
            }
            if (empty($menu['children'])) {
                $menu['children'] = array();
            }
            $menu['children'] = $this->_check_access($menu['children']);
            if (!empty($menu['children'])) {
                $show = true;
            }
            if ($show) {
                $new_menus[] = $menu;
            }
        }
        return $new_menus;
    }

    function _get_menu($menus, $top = false) {
        return $this->ci->load->view('libraries/xmenu_show', array(
            'menus' => $menus,
            'self' => $this,
            'top' => $top,
                ), true);
    }

    function show() {
        $menu_string = '';
        if (!empty($this->xmenu_items)) {
            $menu_string = $this->_get_menu($this->xmenu_items, true);
        }
        return $menu_string;
    }

    function flatten_menu($menus) {
        $result = array();
        foreach($menus as $menu) {

            if (!empty($menu['children'])) {
                $result = array_merge($result, $this->flatten_menu($menu['children']));
            } elseif (!empty($menu['image'])) {
                $result[] = $menu;
            }
        }
        return $result;
    }

    function show_desktop($col = 5, $grouping = false) {
        if (!empty($this->xmenu_items)) {
            if ($grouping) {
                $menus = array();
                foreach($this->xmenu_items as $m) {
                    $m['children'] = $this->flatten_menu($m['children']);
                    if (!empty($m['children'])) {
                        $menus[] = $m;
                    }
                }
            } else {
                $menus = $this->flatten_menu($this->xmenu_items);
            }
            return $this->ci->load->view('libraries/xmenu_show_desktop', array(
                'menus' => $menus,
                'self' => $this,
                'classes' => array('auto', 'auto', 'half', 'thirds', 'quarter', 'fifths'),
                'col' => $col,
                'grouping' => $grouping,
            ), true);
        }
    }

    function _get_breadcrumb_path($menus = 'top') {
        if ($menus == 'top') {
            $menus = $this->xmenu_items;
        }

        $uri = $this->ci->_get_uri($this->ci->uri->rsegments[2]);
        foreach ($menus as $menu) {
            if (!empty($menu['uri']) && $menu['uri'] == $uri) {
                return array($menu);
            } else if (!empty($menu['children'])) {
                $sub_menu = $this->_get_breadcrumb_path($menu['children']);
                if ($sub_menu !== null) {
                    if (empty($sub_menu['uri'])) {
                        return array_merge(array($menu), $sub_menu);
                    } else {
                        return array($menu, $sub_menu);
                    }
                }
            }
        }
        return null;
    }

    function breadcrumb($breadcrumb = null, $show_home = true) {
        if ($breadcrumb == null) {
            $breadcrumb = $this->_get_breadcrumb_path();
            if (empty($breadcrumb)) {
                $breadcrumb = array(
                    array('title' => humanize($this->ci->_name), 'uri' => $this->ci->uri->rsegments[1]),
                );
                if ($this->ci->uri->rsegments[2] != 'index') {
                    $breadcrumb[] = array('title' => humanize($this->ci->uri->rsegments[2]), 'uri' => $this->ci->uri->uri_string);
                }
            }
        }
        return $this->ci->load->view('libraries/xmenu_breadcrumb', array(
            'breadcrumb' => $breadcrumb,
            'show_home' => $show_home,
            'self' => $this,
                ), true);
    }

}

