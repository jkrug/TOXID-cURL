<?php

/*
Plugin Name: TOXID Themeswitcher
Plugin URI: http://toxid.org/
Description: Switch theme for TOXID-cURL.
Version: 1.0
Author: Joscha Krug
Author URI: http://www.marmalade.de

mostly copied from Theme-Switcher-Plugin by
Author: Ryan Boren
Author URI: http://ryan.boren.me/

Adapted from Ryan Boren theme switcher.
http://ryan.boren.me/

*/

class ThemeSwitcher {

	function ThemeSwitcher()
	{		
		add_filter('stylesheet', array(&$this, 'get_stylesheet'));
		add_filter('template', array(&$this, 'get_template'));
	}
	
	function get_stylesheet($stylesheet = '') {
		$theme = $this->get_theme();

		if (empty($theme)) {
			return $stylesheet;
		}

		$theme = get_theme($theme);

		// Don't let people peek at unpublished themes.
		if (isset($theme['Status']) && $theme['Status'] != 'publish')
			return $template;		
		
		if (empty($theme)) {
			return $stylesheet;
		}

		return $theme['Stylesheet'];
	}

	function get_template($template) {
		$theme = $this->get_theme();

		if (empty($theme)) {
			return $template;
		}

		$theme = get_theme($theme);
		
		if ( empty( $theme ) ) {
			return $template;
		}

		// Don't let people peek at unpublished themes.
		if (isset($theme['Status']) && $theme['Status'] != 'publish')
			return $template;		

		return $theme['Template'];
	}

	function get_theme() {
		if ( ! empty($_GET["wptheme"] ) ) {
			return $_GET["wptheme"];
		} else {
			return '';
		}
	}
}

$theme_switcher = new ThemeSwitcher();