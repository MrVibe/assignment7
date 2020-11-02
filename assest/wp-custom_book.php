<?php
/*
Plugin Name: custom book
Description: This is a test book plugin,which is used by the students. 
Author: Adarsh Kumar Shah
Text Domain:custom_book-plugin
Version: 1.0
*/
// add_menu_page() is used to create a menu in the admin pannel

// defining constants

define("PLUGIN_DIR_PATH",plugin_dir_path(__FILE__));
define("PLUGIN_URL",plugins_url());

define("PLUGIN_VERSION", '1.0');

function add_my_custum_book_menu(){
	add_menu_page("custom_book", //page title
		"custom_book-plugin",    //menu title
		"manage_options",        // admin lavel
		"custom_book_plugin",    // page slug
		"all_new_book",         // call back function
		"dashicons-book"                      // icon uri
		);                      //position
	    add_submenu_page(
			"custom_book_plugin",// custum slug
		     "All New",          // page title 
		     "All New",          // menu title 
		     "manage_options",   // capablity user level access
		     "custom_book_plugin",          // menu slug
		     "all_new_book");    // call back function

	    add_submenu_page(
			"custom_book_plugin",// custum slug
		     "Add New",          // page title 
		     "Add New",          // menu title 
		     "manage_options",   // capablity user level access
		     "add_new_book",     // menu slug
		     "add_new_book");    // call back function
}
add_action("admin_menu","add_my_custum_book_menu");
function all_new_book(){
	include_once PLUGIN_DIR_PATH."/view/all_new_book.php";

}
function add_new_book(){
	include_once PLUGIN_DIR_PATH."/view/add_new_book.php";
	insert_data();
}

function custum_book_plugin(){
	// css and js file
	wp_enqueue_style("custum_book_plugin_style", // unique name
		PLUGIN_URL."/custom_book/assest/css/style.css", // css file path
	'', // dependency on other file
    PLUGIN_VERSION);

    wp_enqueue_script("custum_book_plugin_script", // unique name
		PLUGIN_URL."/custom_book/assest/js/script.js", // css file path
	'', // dependency on other file
    PLUGIN_VERSION,
    true);
    $object_array=array(
      "Name"=>"Online Solutions",
      "Author"=>"Adarsh"
    );
    wp_localize_script("custum_book_plugin_script","online_book_management" ,$object_array);
}
add_action("init","custum_book_plugin");


function my_book_table(){
	global $wpdb;
	return $wpdb->prefix."my_book_list"; // wp_my_book_list
}
//table generater
function my_book_list_generate_table_script(){
    global $wpdb;
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    $sql_query_to_create_table="CREATE TABLE `wp_my_book_list` (
		 `id` int(11) NOT NULL AUTO_INCREMENT,
		 `book_name` varchar(255) NOT NULL,
		 `author` varchar(255) NOT NULL,
		 `category` varchar(255) NOT NULL,
		 `about` text NOT NULL,
		 `book_image` text NOT NULL,
		 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		 PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1";
	dbDelta($sql_query_to_create_table);
}
register_activation_hook(__FILE__,"my_book_list_generate_table_script");

// deactivate table
function my_book_list_drop_table(){
	global $wpdb;
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    $wpdb->query('DROP table if Exists wp_my_book_list');

    //step-1: we get the id of the post page
    //delete the page from table

    $the_post_id=get_option("plugin_page"); // getting the id of the post name (plugin_page)
    if(!empty($the_post_id)){
    	wp_delete_post($the_post_id, true);
    }
}
register_deactivation_hook(__FILE__,"my_book_list_drop_table");

// creating a dynamic page on activation of plugin

function create_page(){
	// creating page

	$page=array();
	$page['post_title']="Online Book Management";
	$page['post_content']="Learning platform for students";
	$page['post_status']="publish";
	$page['post_title']=" Online Book Management";
	$page['post_type']="page";
    
	//$post_id=wp_insert_post($page); // post_id as return value

	//add_option("plugin_page",$post_id); // with the help of this post_id we can delete the plugin page on deactivation of plugin.
}
register_activation_hook(__FILE__,"create_page");

//inserting form data into wordpress database






