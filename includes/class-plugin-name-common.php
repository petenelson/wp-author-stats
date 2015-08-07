<?php

if ( ! defined( 'ABSPATH' ) ) die( 'restricted access' );

if ( ! class_exists( 'My_Plugin_Name_Common' ) ) {

	class My_Plugin_Name_Common {

		static $plugin_name    = 'plugin-name';
		static $version        = '2015-07-03-01';


		public function plugins_loaded() {

		}


	} // end class

}
