<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();
//delete_option("mczcs_static_page");
global $wpdb;
//$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}zip_code_area" );
//$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}zip_code_zip" );
//$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}zip_code_city" );