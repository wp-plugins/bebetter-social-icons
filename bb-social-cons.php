<?php
/*
Plugin Name: BeBetter Social Icons
Plugin URI: https://bebetterhotels.com/plugin
Description: Easily add social icons to your site form lots of different font icons. You can put the social icons on any page/post/sidebar/header/footer etc. Based on http://magnigenie.com/wp-social-icons-easily-add-social-icons-site/
Version: 2.5
Author: BeBetter Hotels
Author URI: https://bebetterhotels.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */


define('BBSI_FILE', __FILE__);
define('BBSI_VERSION', '2.5');
define('BBSI_PATH', plugin_dir_path(__FILE__));
define('BBSI_URL_PATH', plugin_dir_url( __FILE__ ));
require BBSI_PATH . 'inc/bbsi.php';

new BBsi();
