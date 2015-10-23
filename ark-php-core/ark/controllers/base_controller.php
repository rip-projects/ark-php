<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * base_controller.php
 *
 * @package   ark-php
 * @author    jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2012/09/11 22:07:34
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2012/09/11 22:07:40   jafar <jafar@xinix.co.id>
 *
 *
 */

class base_controller extends CI_Controller {

  var $_validation;
  var $_name;
  var $_data;
  var $_view;
  var $_layout_view = 'layouts/main';

  function __construct() {
    parent::__construct();

    // if router load this controller as module then load current package path
    if ($this->router->is_module) {
      $this->load->add_package_path($this->router->base_dir);
    }

    $this->load->helper(array('url', 'form', 'x', 'text', 'inflector', 'xform'));

    $this->_name = $this->uri->rsegments[1];
    $this->_page_title = $this->config->item('page_title') . ' - ' . ((empty($this->uri->rsegments[1])) ? '' : humanize($this->uri->rsegments[1])) . ((empty($this->uri->rsegments[2])) ? ' ' : ' ' . humanize($this->uri->rsegments[2]));
    $this->output->enable_profiler($this->config->item('enable_profiler'));

    $this->load->library('session');
    $this->load->library('xparam');
    $this->load->driver('chandler');
    $this->load->driver('xauth', null, 'auth');
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));

    $this->hooks->load();
  }

  function _validate($csrf = false) {
    $result = true;

    if (!$this->security->is_valid_token() && $csrf) {
      add_error(l('Cannot access resources, Contact Administrator'));
      $result = false;
    } else {
      $this->load->library('form_validation');
      if (!empty($this->_validation[$this->uri->rsegments[2]])) {
        $this->form_validation->set_rules($this->_validation[$this->uri->rsegments[2]]);
        $result = $this->form_validation->run();
        if (!$result) {
          add_error($this->form_validation->get_errors());
        }
      }

      $uploader = null;
      if (isset($this->ximage)) {
        $uploader = $this->ximage;
      } elseif (isset($this->upload)) {
        $uploader = $this->upload;
      }
      if (!empty($uploader)) {
        if ($_FILES[$uploader->field]['error'] !== 4) {
          if (!$uploader->do_upload($uploader->field)) {
            $result = false;
            add_error($uploader->error_msg);
          }
        }
      }

      if (isset($_POST['captcha'])) {
        if (!validate_captcha()) {
          $result = false;
          add_error(l('You must submit the word that appears in the image'));
        }
      } elseif (isset($_POST['recaptcha_response_field'])) {
        $this->load->library('recaptcha');
        $this->lang->load('recaptcha');
        $captcha = trim($_POST['recaptcha_response_field']);
        if (empty($captcha) || !$this->recaptcha->check_answer($this->input->ip_address(), $this->input->post('recaptcha_challenge_field'), $this->input->post('recaptcha_response_field'))) {
          $result = false;
          add_error(l('You must submit the word that appears in the image'));
        }
      }
    }

    return $result;
  }

  function &_model($name = '') {
    if (empty($name)) {
      $name = $this->_name;
    } else {
      if (file_exists(APPMODPATH . $name . '/models/' . $name . '_model.php')) {
        $this->load->add_package_path(APPMODPATH . $name);
      } elseif (file_exists(ARCHMODPATH . $name . '/models/' . $name . '_model.php')) {
        $this->load->add_package_path(ARCHMODPATH . $name);
      }
    }
    $model_name = $name . '_model';
    $this->load->model($model_name);
    return $this->$model_name;
  }

  function _post_controller_constructor() {
    // FIXME this should be plugin
    // if (empty($this->_trail_excluded[$this->uri->rsegments[2]])) {
    //   $this->_model('user')->add_trail();
    // }

    // FIXME this should be plugin
    // if (!$this->_check_access()) {
    //   $this->chandler->on_restricted();
    // }
  }

  function _post_controller() {

  }

  function _get_uri($action = '') {
    $pos = array_search($this->_name, $this->uri->segments);
    if ($pos === false) {
      throw new RuntimeException('Error creating uri for action: ' . $action . ' with uri: ' . $this->uri->uri_string);
    }
    $uri = array_chunk($this->uri->segments, $pos);
    return implode('/', $uri[0]) . '/' . $action;
  }

  function _get_redirect() {
    $redirect = '';
    if (!empty($_REQUEST['continue'])) {
      $r = $_REQUEST['continue'];
      if (strpos($r, 'user/unauthorized') === FALSE) {
        $redirect = $r;
      }
    }
    if (empty($redirect) && !empty($_GET['continue'])) {
      $r =  $_GET['continue'];
      if (strpos($r, 'user/unauthorized') === FALSE) {
        $redirect = $r;
      }
    }
    if (empty($redirect) && !empty($_POST['continue'])) {
      $r = $_POST['continue'];
      if (strpos($r, 'user/unauthorized') === FALSE) {
        $redirect = $r;
      }
    }
    $continue = $this->session->userdata('continue');
    if (empty($redirect) && !empty($continue)) {
      $this->session->userdata('continue', NULL);
      $r = $continue;
      if (strpos($r, 'user/unauthorized') === FALSE) {
        $redirect = $r;
      }
    }
    if (empty($redirect) && !empty($_COOKIE['continue'])) {
      $r = $_COOKIE['continue'];
      if (strpos($r, 'user/unauthorized') === FALSE) {
        $redirect = $r;
      }
    }
    if (empty($redirect)) {
      $redirect = base_url();
    }
    return $redirect;
  }

  function _check_access() {
    if (strtoupper($this->config->item('uri_protocol')) === 'CLI') {
      return true;
    }

    $result = $this->auth->privilege($this->uri);
    return ($result) ? true : false;
  }

}
