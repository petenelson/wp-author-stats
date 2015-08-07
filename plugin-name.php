<?php
/*
Plugin Name: wp-plugin-boilerplate
Description: Description
Author: Pete Nelson
Version: 1.0.0
*/

$plugin_class_file = 'plugin-name';

$includes = array(
	'includes/class-' . $plugin_class_file . '-common.php',
	'includes/class-' . $plugin_class_file . '-i18n.php',
	'includes/class-' . $plugin_class_file . '.php',
	'admin/class-' . $plugin_class_file . '-admin.php',
);

$class_base = 'My_Plugin_Name';

$classes = array(
	$class_base . '_Common',
	$class_base . '_i18n',
	$class_base . '',
	$class_base . '_Admin',
);


// activation hook
register_activation_hook( __FILE__, function() {
	require_once 'includes/class-plugin-name-activator.php';
	My_Plugin_Name_Activator::activate();
} );

// include classes
foreach ( $includes as $include ) {
	require_once plugin_dir_path( __FILE__ ) . $include;
}

// instantiate classes and hook into WordPress
foreach ( $classes as $class ) {
	$plugin = new $class();
	add_action( 'plugins_loaded', array( $plugin, 'plugins_loaded' ) );
}
