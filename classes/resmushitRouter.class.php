<?php

 /**
   * ReSmushit Router
   * 
   * 
   * @package    Resmush.it
   * @subpackage Routing management
   * @author     Charles Bourgeaux <hello@resmush.it>
   */
Class reSmushitRouter {

	const DEFAULT_PAGE='optimize';
	const NOTFOUND_PAGE='notfound';


	/**
	 *
	 * Get the Route
	 * @return string
	 */
	public static function loadRoute() {
		reSmushitTemplate::loadComponent('global');
	}


	/**
	 *
	 * Get the Route
	 * @return string
	 */
	public static function getRoute() {
		if(isset($_GET['tabs']) && $_GET['tabs']) {
			$currentRoute = htmlspecialchars(pg_escape_string($_GET['tabs']));
			
			if(in_array($currentRoute, self::authorizedRoutes())) {
				return $currentRoute;
			}
			return self::NOTFOUND_PAGE;
		}
		return self::DEFAULT_PAGE;
	}



	/**
	 *
	 * Get the Route
	 * @return string
	 */
	public static function getRouteURL($slug) {
		if(in_array($slug, self::authorizedRoutes())) {
			return get_admin_url() . "upload.php?page=resmushit_options&tabs=$slug";
		}
		return get_admin_url() . "upload.php?page=resmushit_options&tabs=" . self::NOTFOUND_PAGE;
	}

	/**
	 *
	 * return authorized route slugs
	 * @return string
	 */
	public static function authorizedRoutes() {
		return array(
			self::DEFAULT_PAGE,
			self::NOTFOUND_PAGE,
			'tools',
			'statistics',
			'settings',
			'logs',
			'support'
		);
	}
}
