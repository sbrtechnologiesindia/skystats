<?php 
/*
Plugin Name: SkyStats
Plugin URI: http://127.0.0.1
Description: Adds a dashboard for site data and social media information.
Version: 0.0.1 Alpha
Author: Anonymous	
Author URI: http://127.0.0.1
License: GPL v3
*/
if(!defined('ABSPATH'))
	die("No direct access!");

define('MARKET_PLUGIN_PATH' , dirname(__FILE__));
define('MARKET_PLUGIN_URL' , plugins_url().'/skystats');
define('SERVER_APP_URL' , 'http://dev.clunkymembrane.com/');
define('APP_NAME' , 'SkyStats');
define('APP_NAME_SLUG' , 'skystats');
define('NEWAPPNAME','SkyStats');
define('PLUGIN_PREFIX','skst_');
define('ACTIVEMSG','For this plugin to obtain metrics, you must allow the plugin to access a remote server, where analytic data is collected for your profile. ');
define('ACTIVELINK','Please Allow Access from <a href="?page=wp-skystats-settings">Settings</a> to get the result.');
define('NOCURL','Please activate the CURL to get the result');

require(MARKET_PLUGIN_PATH . '/includes/functions.php'); // non ajax helper functions
require(MARKET_PLUGIN_PATH.'/classes/settings.class.php');
require(MARKET_PLUGIN_PATH . '/includes/ajax.php');

$settingObj = new skst_Administration;
add_action('init', PLUGIN_PREFIX.'myStartSession', 1);
add_action('init', PLUGIN_PREFIX.'myStartSession', 1);
add_action('wp_logout', PLUGIN_PREFIX.'myEndSession');
add_action('wp_login', PLUGIN_PREFIX.'myEndSession');

function skst_myStartSession() 
{
    if(!session_id()) {
        session_start();
    }
}

function skst_myEndSession() {
    session_destroy ();
}
register_activation_hook(__FILE__, PLUGIN_PREFIX.'activate');
function skst_activate()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "options";	
	if(get_option('skst_plugin_notice_shown')<>'')
	{
		$sql = "update " . $table_name.			
		" set option_value='false' where option_name='skst_plugin_notice_shown'" ;
	}
	else
	{
		$sql = "INSERT INTO " . $table_name.			
		" VALUES ('','skst_plugin_notice_shown','false','yes')";	
	}	
	$wpdb->query($sql);		
	
}
add_action('admin_notices', 'skst_plugin_admin_notices');
function skst_plugin_admin_notices() {
	

	global $wpdb;
	$table_name = $wpdb->prefix . "options";
	if (get_option('skst_plugin_notice_shown')!="true"  ) 
	{
    	echo "<div class='updated'> <p> ".ACTIVEMSG." Enable now?<div style=\"background-color:#0073a4;padding:5px 5px 5px 10px;border:1px solid #cccccc;width:45px;color:#ffffff; cursor:pointer;\" class=\"enable\">Enable</div> </p> </div>";    			
		$sql = "update " . $table_name.			
		" set option_value='true' where option_name='skst_plugin_notice_shown'" ;			
		$wpdb->query($sql);	
			
	}
}
register_deactivation_hook( __FILE__, PLUGIN_PREFIX.'deactivate' );
function skst_deactivate()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "options";	
	$sql = "update " . $table_name.			
		" set option_value='0' where option_name='skst_allow_access'" ;		
		$wpdb->query($sql);	
		$sql = "update " . $table_name.			
		" set option_value='false' where option_name='skst_plugin_notice_shown'" ;			
		$wpdb->query($sql);				
}//end of function

function skst_get_book_post_type_template($single_template) {
 global $post;

 if ($post->post_name == 'upgrade') {
      $single_template = MARKET_PLUGIN_PATH . '/upgrade_template.php';
 } else if ($post->post_name == 'viewuser') {
      $single_template = MARKET_PLUGIN_PATH . '/userview_template.php';
 }
 return $single_template;
}

add_filter( "page_template", PLUGIN_PREFIX."get_book_post_type_template" ) ;