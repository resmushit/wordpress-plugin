<?php

 /**
   * ReSmushit Template
   * 
   * 
   * @package    Resmush.it
   * @subpackage Template management
   * @author     Charles Bourgeaux <hello@resmush.it>
   */
Class reSmushitTemplate {

	const COMPONENTS_PATH = RESMUSHIT_BASE_PATH . 'templates/components/';
	const COMPONENTS_URL = RESMUSHIT_BASE_URL . 'templates/components/';
	const PAGES_PATH = RESMUSHIT_BASE_PATH . 'templates/pages/';
	const PAGES_URL = RESMUSHIT_BASE_URL . 'templates/pages/';
	const TEMPLATE_EXT = '.tpl.php';
	const DEFAULT_PAGE = 'notfound';

	const PAGES_CONTROLLERS_PATH = RESMUSHIT_BASE_PATH . 'controllers/pages/';
	const COMPONENTS_CONTROLLERS_PATH = RESMUSHIT_BASE_PATH . 'controllers/components/';

	/**
	 *
	 * Loads a component template with CSS & JS
	 *
	 */
	public static function loadComponent($componentName) {
		if(file_exists(self::COMPONENTS_CONTROLLERS_PATH . $componentName . '.php'))
			include self::COMPONENTS_CONTROLLERS_PATH . $componentName . '.php';
		include self::loadTemplate($componentName, 'component', $params);
	}

	/**
	 *
	 * Loads a page template with CSS & JS
	 *
	 */
	public static function loadPage($pageName) {
		if(file_exists(self::PAGES_CONTROLLERS_PATH . $pageName . '.php'))
			include self::PAGES_CONTROLLERS_PATH . $pageName . '.php';
		include self::loadTemplate($pageName, 'page', $params);
	}


	/**
	 *
	 * Generic function to load a template with CSS & JS
	 *
	 * @return boolean
	 */
	private static function loadTemplate($elementName, $elementType) {
		switch($elementType) {
			case 'component':
				$templatePath = self::COMPONENTS_PATH;
				$templateURL = self::COMPONENTS_URL;
				break;
			case 'page':
				$templatePath = self::PAGES_PATH;
				$templateURL = self::PAGES_URL;
				break;
			default:
				return FALSE;
				break;
		}

		if(file_exists($templatePath . $elementName . self::TEMPLATE_EXT)) {
			if(file_exists($templatePath . $elementName . '.css')) {
				wp_register_style( 'resmushit-' . $elementType . '-' . $elementName . '-css', $templateURL . $elementName . '.css?' . hash_file('crc32', $templatePath . $elementName . '.css') );
				wp_enqueue_style( 'resmushit-' . $elementType . '-' . $elementName . '-css' );
			}

			if(file_exists($templatePath . $elementName . '.js')) {
				wp_register_script( 'resmushit-' . $elementType . '-' . $elementName . '-js', $templateURL . $elementName . '.js?' . hash_file('crc32', $templatePath . $elementName . '.js'));
	    		wp_enqueue_script( 'resmushit-' . $elementType . '-' . $elementName . '-js' );
			}
			return $templatePath . $elementName . self::TEMPLATE_EXT;
		} else {
			if($elementType == 'page') {
				return self::loadTemplate(self::DEFAULT_PAGE, 'page');
			}
			return FALSE;
		}
	}
}
