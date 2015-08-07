<?php

if ( ! defined( 'ABSPATH' ) ) die( 'restricted access' );

if ( ! class_exists( 'WP_Author_Stats_i18n' ) ) {

	class WP_Author_Stats_i18n {


		public function plugins_loaded() {

			load_plugin_textdomain(
				WP_Author_Stats_Common::PLUGIN_NAME,
				false,
				dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
			);

		}


	} // end class

}
