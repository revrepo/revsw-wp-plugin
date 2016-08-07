<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
Plugin Name: RevAPM Cache
Plugin URI: https://www.revapm.com/
Description: A plugin for wordpress cache based on RevAPM CDN network
Version: 0.0.5
Author: Modular Coding Inc
Author URI: https://modcoding.com
License: See license.txt
*/
define ("MCREVAPM_PLUGIN_VERSION","ver=0.0.5");
define ("MCREVAPM_PLUGIN_DIR",str_replace(DIRECTORY_SEPARATOR,"/",plugin_dir_path( __FILE__ )));
define ("MCREVAPM_PLUGIN_URL",plugins_url()."/revapm-cache/");
define ("MCREVAPM_SITE_URL",site_url()."/");
define ("MCREVAPM_AJAX_URL",admin_url('admin-ajax.php'));
define ("MCREVAPM_MAX_TIMEOUT",5*60); // 5 minutes
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
		'mcrevapm_settings', 'mcrevapm_settings', MCREVAPM_PLUGIN_URL."assets/img/logo16.png" );
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
		foreach ($ar_settings["domains"] as $ar_domain){
			$old_domain = $ar_domain["name"];
			$new_domain = $ar_domain["cname"];
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("2.footer old = $old_domain new = ".$new_domain);
			$s = str_replace($old_domain,$new_domain,$s);
		}
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
	include_once MCREVAPM_PLUGIN_DIR."inc/api.php";
	$api = new MCREVAPM_API();
	$json_data = @file_get_contents("php://input");
	$request = @json_decode($json_data, true);
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("1.mcrevapm_save_system_settings\n$json_data\n".print_r($request,true));
	echo $api->save_system_settings($request);
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("<mcrevapm_save_system_settings");
	exit(0);
}
add_action("wp_ajax_mcrevapmsavesystemsettings","mcrevapm_save_system_settings");

function mcrevapm_get_domains() {
if (defined("MCREVAPM_DEBUG")) mcrevapm_log(">mcrevapm_get_domains");
	include_once MCREVAPM_PLUGIN_DIR."inc/api.php";
	$api = new MCREVAPM_API();
	$ar_settings = @json_decode(get_option("mcrevapm_settings"),true);
	$api_key = @$ar_settings["api_key"];
	echo $api->get_domains($api_key,"json");
if (defined("MCREVAPM_DEBUG")) mcrevapm_log("<mcrevapm_get_domains");
	exit(0);
}
add_action("wp_ajax_mcrevapmgetdomains","mcrevapm_get_domains");

if (defined("MCREVAPM_TESTS_ENABLED")) {
	include_once MCREVAPM_PLUGIN_DIR."tests.php";
}