<?php
namespace Resmush\Controller;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class WpCliController
{
  protected static $instance;


  public static function getInstance()
  {
    if (is_null(self::$instance))
     self::$instance = new static();

    return self::$instance;
  }

  public function __construct()
  {
    /**
    *
    * Declares WPCLI extension if in WP_CLI context
    *
    */
    if( defined( 'WP_CLI' ) && WP_CLI ) {
    	WP_CLI::add_command( 'resmushit', 'reSmushitWPCLI' );
    }
  }

}
