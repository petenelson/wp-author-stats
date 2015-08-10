<?php
/*
Plugin Name: WP Author Stats
Description: Display and download author stats (total posts, word count, and views using Jetpack)
Author: Pete Nelson
Version: 1.0.0
*/

$plugin_class_file = 'wp-author-stats';

$includes = array(
	'includes/class-' . $plugin_class_file . '-common.php',
	'includes/class-' . $plugin_class_file . '-i18n.php',
	'admin/class-' . $plugin_class_file . '-admin.php',
);

$class_base = 'WP_Author_Stats';

$classes = array(
	$class_base . '_Common',
	$class_base . '_i18n',
	$class_base . '_Admin',
);


// activation hook
register_activation_hook( __FILE__, function() {
	require_once 'includes/class-wp-author-stats-activator.php';
	WP_Author_Stats_Activator::activate();
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
