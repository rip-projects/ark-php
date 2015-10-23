<?php

/**
 *
 * boostrap.php
 *
 * @package   ark-php
 * @author    Jafar Shadiq <jafar@xinix.co.id>
 * @copyright Copyright(c) 2013 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2013-01-17 04:06:42
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2013-01-17 04:06:42   Jafar Shadiq <jafar@xinix.co.id>
 *
 *
 */

require_once __DIR__.'/common.php';

function ark_handle_exceptions($exception) {
  if (function_exists('log_message')) {
    $h ='Exception of type \''.get_class($exception).'\' occurred with Message: '.$exception->getMessage().' in File '.$exception->getFile().' at Line '.$exception->getLine();
    $bt =$exception->getTraceAsString();
    log_message('error', $h."\nBacktrace:\n".$bt, TRUE);
  }
  show_exception($exception);
  // mail('dev-mail@example.com', 'An Exception Occurred', $msg, 'From: test@example.com');
}

function ark_handle_shutdown() {
  $error = error_get_last();
  if (isset($error)) {
    _exception_handler($error['type'], $error['message'], $error['file'], $error['line']);
  }
}

function ark_get_manifest() {
  $manifest = array(
    'ENVIRONMENT' => 'development',
  );
  if (file_exists(APPPATH.'manifest.php')) {
    @require_once APPPATH.'manifest.php';
  } else {
    header('HTTP/1.1 500 ARCH-PHP Configuration Server Error', true, 500);
    throw new Exception("Manifest not found. Probably application data is missing or broken at ".APPPATH.'manifest.php');
  }
  return $manifest;
}

function ark_autoload_class($class) {
  $class = strtolower($class);
  if (substr($class, 0, 2) == 'ci') return;

  $exploded = explode('_', $class);
  $match_class = $exploded[count($exploded)-1];

  if ($match_class === 'model' || $match_class === 'controller') {
    foreach (array(APPPATH, ARCHPATH) as $path) {
      $file_path = $path . $match_class . 's/' . $class . '.php';
      if (file_exists($file_path)) {
        require_once $file_path;
        break;
      }
    }
  }
}

function ark_bootstrap($dir) {
  define('ARCHPHP_VERSION', '0.0.1');

  define('COREPATH', __DIR__.'/../');
  define('ARCHPATH', COREPATH.'ark/');
  define('BASEPATH', COREPATH.'system/');
  define('ROOTPATH', $dir);
  define('APPROOTPATH', ROOTPATH.'application/');

  set_exception_handler('ark_handle_exceptions');
  register_shutdown_function('ark_handle_shutdown');
  spl_autoload_register('ark_autoload_class');

  require_once ROOTPATH.'config/ark.php';
  $config['default'] = ($config['default']) ? $config['default'] : 'default';

  if (is_cli_request()) {
    $path = APPROOTPATH.$_SERVER['ARKAPPID'].'/';
  } else {
    $a = (!empty($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : '/';
    $a = explode($a, $_SERVER['SCRIPT_NAME']);
    $a = explode('/index.php', $a[0]);
    $a = explode('/', $a[0]);
    $a = $a[count($a)-1];
    if (empty($a)) {
      $a = $config['default'];
    }
    $path = APPROOTPATH.$a.'/';
  }
  if (!file_exists($path)) {
    $path = APPROOTPATH.$config['default'].'/';
  }
  define('APPPATH', $path);

  define('APPMODPATH', APPPATH . 'modules/');
  define('ARCHMODPATH', ARCHPATH . 'modules/');

  $manifest = ark_get_manifest();
  define('ENVIRONMENT', $manifest['ENVIRONMENT']);
  define('DATAPATH', (isset($manifest['DATAPATH'])) ? $manifest['DATAPATH'] : 'data/');
  define('THEMEPATH', (isset($manifest['THEMEPATH'])) ? $manifest['THEMEPATH'] : 'themes/');
}

