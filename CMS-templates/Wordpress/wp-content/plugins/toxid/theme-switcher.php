<?php

/*
Plugin Name: TOXID Themeswitcher
Plugin URI: http://toxid.org/
Description: Switch theme for TOXID-cURL.
Version: 1.1
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
        add_filter('preview_page_link', array(&$this, 'add_preview_theme'));
        add_filter('preview_post_link', array(&$this, 'add_preview_theme'));
        add_action('admin_init', array(&$this, 'init_admin'));
    }
    
    function add_preview_theme($link)
    {
        $theme = urlencode(get_option('toxid_preview_theme'));
        $link .= (strpos($link, '?') === false ? '?' : '&') . 'wptheme=' . $theme;
        return $link;
    }
    
    function init_admin()
    {
        register_setting('general', 'toxid_preview_theme');
        add_settings_section('toxid-settings', 'TOXID', '__return_false', 'general');
        add_settings_field('toxid_preview_theme', 'Theme used for previews', array(&$this, 'admin_toxid_preview_theme_field'), 'general', 'toxid-settings');
    }
    
    function admin_toxid_preview_theme_field()
    {
        $themes = array_keys(get_themes());
        $currentTheme = get_option('toxid_preview_theme');
        echo '<select name="toxid_preview_theme">';
        echo '<option>' . __('None') . '</option>';
        foreach ($themes as $theme) {
            printf('<option value="%s" %s>%s</option>', esc_attr($theme), ($theme == $currentTheme ? 'selected' : ''), esc_html($theme));
        }
        echo '</select>';
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