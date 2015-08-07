<?php

if ( ! defined( 'ABSPATH' ) ) die( 'restricted access' );

if ( ! class_exists( 'My_Plugin_Name_Admin' ) ) {

	class My_Plugin_Name_Admin {


		public function plugins_loaded() {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
		}


		public function admin_init() {

		}


	}

}
