<?php

if ( ! defined( 'ABSPATH' ) ) die( 'restricted access' );

if ( ! class_exists( 'WP_Author_Stats_Admin' ) ) {

	class WP_Author_Stats_Admin {


		public function plugins_loaded() {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
		}


		public function admin_init() {

		}


	}

}
