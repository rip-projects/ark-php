<?php

/**
 * MY_Router.php
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
 * Description of MY_Router
 *
 * @author jafar
 */
class MY_Router extends CI_Router {

    var $is_module = false;
    var $module_name;
    var $base_dir = APPPATH;

    function fetch_base_dir() {
        return $this->base_dir;
    }

    /**
     * Validates the supplied segments.  Attempts to determine the path to
     * the controller.
     *
     * @access	private
     * @param	array
     * @return	array
     */
    function _validate_request($segments) {
        if (count($segments) == 0) {
            return $segments;
        }

        // Does the requested controller exist in the root folder?
        if (file_exists(APPPATH . 'controllers/' . $segments[0] . '.php')) {
            return $segments;
        }

        // Is the controller in a sub-folder?
        if (is_dir(APPPATH . 'controllers/' . $segments[0])) {
            // Set the directory and remove it from the segment array
            $this->set_directory($segments[0]);
            $segments = array_slice($segments, 1);

            if (count($segments) > 0) {
                // Does the requested controller exist in the sub-folder?
                if (!file_exists(APPPATH . 'controllers/' . $this->fetch_directory() . $segments[0] . '.php')) {
                    show_404($this->fetch_directory() . $segments[0]);
                }
            } else {
                // Is the method being specified in the route?
                if (strpos($this->default_controller, '/') !== FALSE) {
                    $x = explode('/', $this->default_controller);

                    $this->set_class($x[0]);
                    $this->set_method($x[1]);
                } else {
                    $this->set_class($this->default_controller);
                    $this->set_method('index');
                }

                // Does the default controller exist in the sub-folder?
                if (!file_exists(APPPATH . 'controllers/' . $this->fetch_directory() . $this->default_controller . '.php')) {
                    $this->directory = '';
                    return array();
                }
            }

            return $segments;
        }

        /** REMARK: reekoheek add this * */
        // Does the requested controller exist in the root folder?
        if (file_exists(APPMODPATH . $segments[0] . '/controllers/' . $segments[0] . '.php') || file_exists(APPMODPATH . $segments[0] . '/controllers/' . $segments[0] . '_controller.php')) {
            $this->module_name = $segments[0];
            $this->base_dir = APPMODPATH.$this->module_name.'/';
            $this->is_module = true;
            return $segments;
        } elseif (file_exists(ARCHMODPATH . $segments[0] . '/controllers/' . $segments[0] . '.php') || file_exists(ARCHMODPATH . $segments[0] . '/controllers/' . $segments[0] . '_controller.php')) {
            $this->module_name = $segments[0];
            $this->base_dir = ARCHMODPATH.$this->module_name.'/';
            $this->is_module = true;
            return $segments;
        } elseif (file_exists(ARCHPATH . 'controllers/' . $segments[0] . '.php') || file_exists(ARCHPATH . 'controllers/' . $segments[0] . '_controller.php')) {
            $this->base_dir = ARCHPATH;
            return $segments;
        }

        /** REMARK: reekoheek add this * */
        // If we've gotten this far it means that the URI does not correlate to a valid
        // controller class.  We will now see if there is an override
        if (!empty($this->routes['404_override'])) {
            $x = explode('/', $this->routes['404_override']);

            $this->set_class($x[0]);
            $this->set_method(isset($x[1]) ? $x[1] : 'index');

            return $x;
        }


        // Nothing else to do at this point but show a 404
        show_404($segments[0]);
    }

    function _set_routing()
    {
        // Are query strings enabled in the config file?  Normally CI doesn't utilize query strings
        // since URI segments are more search-engine friendly, but they can optionally be used.
        // If this feature is enabled, we will gather the directory/class/method a little differently
        $segments = array();
        if ($this->config->item('enable_query_strings') === TRUE AND isset($_GET[$this->config->item('controller_trigger')]))
        {
            if (isset($_GET[$this->config->item('directory_trigger')]))
            {
                $this->set_directory(trim($this->uri->_filter_uri($_GET[$this->config->item('directory_trigger')])));
                $segments[] = $this->fetch_directory();
            }

            if (isset($_GET[$this->config->item('controller_trigger')]))
            {
                $this->set_class(trim($this->uri->_filter_uri($_GET[$this->config->item('controller_trigger')])));
                $segments[] = $this->fetch_class();
            }

            if (isset($_GET[$this->config->item('function_trigger')]))
            {
                $this->set_method(trim($this->uri->_filter_uri($_GET[$this->config->item('function_trigger')])));
                $segments[] = $this->fetch_method();
            }
        }

        // Load the routes.php file.
        if (defined('ENVIRONMENT') AND is_file(APPPATH.'config/'.ENVIRONMENT.'/routes.php'))
        {
            include(APPPATH.'config/'.ENVIRONMENT.'/routes.php');
        }
        elseif (is_file(APPPATH.'config/routes.php'))
        {
            include(APPPATH.'config/routes.php');
        }
        else
        {
            $route['default_controller'] = 'site/index';
            $route['404_override'] = '';
        }

        $this->routes = ( ! isset($route) OR ! is_array($route)) ? array() : $route;
        unset($route);

        // Set the default controller so we can display it in the event
        // the URI doesn't correlated to a valid controller.
        $this->default_controller = ( ! isset($this->routes['default_controller']) OR $this->routes['default_controller'] == '') ? FALSE : strtolower($this->routes['default_controller']);

        // Were there any query string segments?  If so, we'll validate them and bail out since we're done.
        if (count($segments) > 0)
        {
            return $this->_validate_request($segments);
        }

        // Fetch the complete URI string
        $this->uri->_fetch_uri_string();

        // Is there a URI string? If not, the default controller specified in the "routes" file will be shown.
        if ($this->uri->uri_string == '')
        {
            return $this->_set_default_controller();
        }

        // Do we need to remove the URL suffix?
        $this->uri->_remove_url_suffix();

        // Compile the segments into an array
        $this->uri->_explode_segments();

        // Parse any custom routing that may exist
        $this->_parse_routes();

        // Re-index the segment array so that it starts with 1 rather than 0
        $this->uri->_reindex_segments();
    }

    function set_class($class)
    {
        $this->class = str_replace(array('/', '.'), '', $class);
        if (!file_exists($this->base_dir . 'controllers/'. $this->class .'.php')) {
            $this->class = $this->class.'_controller';
        }
    }

}
