<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
Plugin Name: RevAPM Cache
Plugin URI: https://www.revapm.com/
Description: A plugin for wordpress cache based on RevAPM CDN network
Version: 0.0.3
Author: Modular Coding Inc
Author URI: https://modcoding.com
License: See license.txt
*/
define ("MCREVAPM_PLUGIN_VERSION","ver=0.0.3");
define ("MCREVAPM_PLUGIN_DIR",str_replace(DIRECTORY_SEPARATOR,"/",plugin_dir_path( __FILE__ )));
define ("MCREVAPM_PLUGIN_URL",plugins_url()."/revapm-cache/");
define ("MCREVAPM_SITE_URL",site_url()."/");
define ("MCREVAPM_AJAX_URL",admin_url('admin-ajax.php'));
define ("MCREVAPM_DEBUG",1);
//define ("MCREVAPM_DEBUG_CURL",1);
define ("MCREVAPM_TESTS_ENABLED",1);
global $table_prefix;
//define ("MCREVAPM_AREA_TABLE",$table_prefix."revapm_settings");
if (defined("MCREVAPM_DEBUG")) {
	define("MCREVAPM_LOG", MCREVAPM_PLUGIN_DIR . "log.txt");

	function mcrevapm_log( $msg ) {
	//if ( stripos(MCTEST_IP,",".$_SERVER["REMOTE_ADDR"]."," ) === false) return; // do not log other users
		if ( @filesize( MCREVAPM_LOG ) > 100 * 1024 * 1024 )
			@unlink( MCREVAPM_LOG );
		$cur_retry = 1;
		while ( $cur_retry <= 100 ) {
			@$fh = fopen( MCREVAPM_LOG, "a" );
			if ( ! $fh ) {
				$cur_retry ++;
				continue;
			}
//			$s = sprintf("%1.3f",microtime(true))."\t".$msg."\n".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."\n\n";
			$s = @date("d.m.Y H:i:s.")."\t".$msg."\n".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."\n\n";
			@fwrite( $fh, $s );
			@fclose( $fh );
			break;
		} // retry;
	}
}


function mcrevapm_activation(){
	$s = trim(@get_option("mcrevapm_settings"));
if (defined("MCREVAPM_DEBUG")) mcrevapm_log(">activation settings:\n".$s);
	if (strlen($s) <= 0) {
		$ar_default_settings = array(
			"cdn_status" => "off",
			"account_status" => "",
			"api_key" => "",
			"domain_id" => "",
			"domain_name" => "",
			"domain_server" => "",
			"domain_cname" => ""
		);
		add_option("mcrevapm_settings",json_encode($ar_default_settings));
	}
}

function mcrevapm_deactivation(){
	// do nothing for now
	delete_option("mcrevapm_settings");
}

function mcrevapm_settings(){
	include_once MCREVAPM_PLUGIN_DIR."views/settings.php";
}

function mcrevapm_dashboard_add_page(){
	add_menu_page( "RevAPM Cache", "RevAPM Cache", 'manage_options',
		'mcrevapm_settings', 'mcrevapm_settings', MCREVAPM_PLUGIN_URL."assets/img/logo24.png" );
}

function mcrevapm_settings_link($links){
	$url = get_admin_url()."admin.php?page=mcrevapm_settings";
	$settings_link = '<a href="'.$url.'">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}

function mcrevapm_header(){
	@ob_start();
if (defined("MCREVAPM_DEBUG")) mcrevapm_log(">header");
}

function mcrevapm_footer(){
	// TODO: filter content and replace static links to CDN if enabled
if (defined("MCREVAPM_DEBUG")) mcrevapm_log(">footer");
	$s = @ob_get_clean();
	$ar_settings = @json_decode(get_option("mcrevapm_settings"),true);
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("1.footer\n".print_r($ar_settings,true));
	if (@$ar_settings["cdn_status"] == "on") {
		$server = $_SERVER["HTTP_HOST"];
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("2.footer server = ".$server);
		$new_server = $ar_settings["domain_cname"];
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("3.footer new server = ".$new_server);
		$s = str_replace($server,$new_server,$s);
	}
	echo $s;
if (defined("MCREVAPM_DEBUG")) file_put_contents(MCREVAPM_PLUGIN_DIR."page.html",$s);
	if (defined("MCREVAPM_DEBUG")) mcrevapm_log("<footer");
}

function mcrevapm_save_settings(){
	$json_data = @file_get_contents("php://input");
	$ar_settings = @json_decode($json_data, true);
	update_option("mcrevapm_settings",json_encode($ar_settings));
	echo "1";
	exit(0);
}

/////////////////////////////////////////////// filters, actions ///////////////////////////////////////////////////////

register_activation_hook( __FILE__, 'mcrevapm_activation' );
register_deactivation_hook( __FILE__, 'mcrevapm_deactivation' );

// add settings page to left menu in dashboard
add_action('admin_menu', 'mcrevapm_dashboard_add_page');
// settings link in plugin list
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'mcrevapm_settings_link');
//if (!is_admin() && ((int)$_GET["mcdebug"] == 1)) {
if (!is_admin())  {
	add_action('init', 'mcrevapm_header');
	add_action('wp_footer', 'mcrevapm_footer', PHP_INT_MAX);
//	remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
}

///////////////////////////////////////////////// AJAX actions /////////////////////////////////////////////////////////
function mcrevapm_signup(){
if (defined("MCREVAPM_DEBUG")) mcrevapm_log(">mcrevapm_signup");
	include_once MCREVAPM_PLUGIN_DIR."inc/api.php";
	$api = new MCREVAPM_API();
	$data = @json_decode(@file_get_contents("php://input"),true);
	echo $api->signup($data);
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("<mcrevapm_signup");
	exit(0);
}
add_action("wp_ajax_mcrevapmsignup","mcrevapm_signup");

function mcrevapm_get_api_key(){
if (defined("MCREVAPM_DEBUG")) mcrevapm_log(">mcrevapm_get_api_key");
	include_once MCREVAPM_PLUGIN_DIR."inc/api.php";
	$api = new MCREVAPM_API();
	$email = trim(@$_REQUEST["email"]);
	$password = @$_REQUEST["pass"];
	echo $api->get_api_key($email,$password);
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("<mcrevapm_get_api_key");
	exit(0);
}
add_action("wp_ajax_mcrevapmgetapikey","mcrevapm_get_api_key");

function mcrevapm_set_api_key() {
if (defined("MCREVAPM_DEBUG")) mcrevapm_log(">mcrevapm_set_api_key\n".print_r($_REQUEST,true));
	include_once MCREVAPM_PLUGIN_DIR."inc/api.php";
	$api = new MCREVAPM_API();
	$key = @$_REQUEST["key"];
	echo $api->set_api_key($key);
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("<mcrevapm_set_api_key");
	exit(0);
}
add_action("wp_ajax_mcrevapmsetapikey","mcrevapm_set_api_key");

function mcrevapm_add_domain() {
if (defined("MCREVAPM_DEBUG")) mcrevapm_log(">mcrevapm_add_domain\n".print_r($_REQUEST,true));
	include_once MCREVAPM_PLUGIN_DIR."inc/api.php";
	$api = new MCREVAPM_API();
	$domain = @$_REQUEST["domain"];
	$server = @$_REQUEST["server"];
	$header = @$_REQUEST["header"];
	$location = @$_REQUEST["location"];
	echo $api->add_domain($domain,$server,$header,$location);
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("<mcrevapm_add_domain");
	exit(0);
}
add_action("wp_ajax_mcrevapmadddomain","mcrevapm_add_domain");

function mcrevapm_delete_domain() {
if (defined("MCREVAPM_DEBUG")) mcrevapm_log(">mcrevapm_delete_domain\n".print_r($_REQUEST,true));
	include_once MCREVAPM_PLUGIN_DIR."inc/api.php";
	$api = new MCREVAPM_API();
	$id = @$_REQUEST["id"];
	echo $api->delete_domain($id);
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("<mcrevapm_delete_domain");
	exit(0);
}
add_action("wp_ajax_mcrevapmadeletedomain","mcrevapm_delete_domain");

function mcrevapm_save_system_settings() {
if (defined("MCREVAPM_DEBUG")) mcrevapm_log(">mcrevapm_save_system_settings\n".print_r($_REQUEST,true));
	if (!current_user_can("manage_options")) die ("Access denied");
	$json_data = @file_get_contents("php://input");
	$request = @json_decode($json_data, true);
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("1.mcrevapm_save_system_settings request:\n".print_r($request,true));
	$ar_settings = @json_decode(get_option("mcrevapm_settings"),true);
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("2.mcrevapm_save_system_settings old settings:\n".print_r($ar_settings,true));
	$ar_settings["cdn_status"] = trim(@$request["cdn_status"]);
	if ($ar_settings["cdn_status"] == "on") {
			$ar_settings["domain_id"] = trim(@$request["domain_id"]);
			$ar_settings["domain_name"] = trim(@$request["domain_name"]);
			$ar_settings["domain_server"] = trim(@$request["domain_server"]);
			$ar_settings["domain_cname"] = trim(@$request["domain_cname"]);
	}
	update_option("mcrevapm_settings",json_encode($ar_settings));
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("3.mcrevapm_save_system_settings new settings:\n".print_r($ar_settings,true));
	echo "1";
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("<mcrevapm_save_system_settings");
	exit(0);
}
add_action("wp_ajax_mcrevapmsavesystemsettings","mcrevapm_save_system_settings");


if (defined("MCREVAPM_TESTS_ENABLED")) {
	include_once MCREVAPM_PLUGIN_DIR."tests.php";
}