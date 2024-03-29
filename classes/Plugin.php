<?php
namespace Resmush;

use \Resmush\ShortPixelLogger\ShortPixelLogger as Log;

use \Resmush\Controller\AdminController as AdminController;
use \Resmush\Controller\AjaxController as AjaxController;
use \Resmush\Controller\CronController as CronController;
use \Resmush\Controller\ProcessController as ProcessController;


use Resmush\FileSystem\Controller\FileSystemController as FileSystem;



if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


// One day the basis of it all.
class Plugin
{

    protected static $instance;

    public function __construct()
    {
        // Regulare init after wp is loaded. This is fairly late.
        add_action('wp_loaded', array($this, 'init'));
    }

    public static function getInstance()
    {
      if (is_null(self::$instance))
       self::$instance = new Plugin();

      return self::$instance;
    }

    public function init()
    {
        $this->initHooks();

        // All hooks init
        AjaxController::getInstance();
        AdminController::getInstance();
        CronController::getInstance();
        ProcessController::getInstance();
    }


    public function initHooks()
    {

    }

    public function fs()
    {
      return new FileSystem();
    }

    public function process()
    {
       return ProcessController::getInstance();
    }

    public static function checkLogger()
    {
      $log = Log::getInstance();
      if (Log::debugIsActive()) // upload dir can be expensive, so only do this when log is actually active.
      {
        $uploaddir = wp_upload_dir(null, false, false);
        if (isset($uploaddir['basedir']))
        {
          $log->setLogPath($uploaddir['basedir'] . "/resmushit.log");
        }
      }
    }

}
