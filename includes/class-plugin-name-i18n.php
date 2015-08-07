<?php

if ( ! defined( 'ABSPATH' ) ) die( 'restricted access' );

if ( ! class_exists( 'My_Plugin_Name_i18n' ) ) {

	class My_Plugin_Name_i18n {


		public function plugins_loaded() {

			load_plugin_textdomain(
				My_Plugin_Name_Common::$plugin_name,
				false,
				dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
			);

		}


	} // end class

}
