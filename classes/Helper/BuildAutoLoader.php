<?php
namespace Resmush\Helper;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class BuildAutoLoader
{

  public static function buildJSON()
  {
ThisCrash();

    echo 'Building Plugin.JSON';
    $plugin = array(
        'name' => 'Resmush/Plugin',
        'description' => 'Resmush AutoLoader',
        'type' => 'function',
        'autoload' => array('psr-4' => array('Resmush' => 'class'),
            'files' => self::getFiles(),
        ),
      );

    $f = fopen('class/plugin.json', 'w');
    $result = fwrite($f, json_encode($plugin));


    if ($result === false)
      echo "!!! Error !!! Could not write Plugin.json";

    fclose($f);
  }

  public static function getFiles()
  {
    $legacy = array(
        'resmushit.admin.php'
    );


    echo "Build Plugin.JSON ";
    return $legacy; //array_merge($main,$models,$externals);
  }

}
