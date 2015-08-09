<?php

if ( ! defined( 'ABSPATH' ) ) die( 'restricted access' );

if ( ! class_exists( 'WP_Author_Stats_Admin' ) ) {

	class WP_Author_Stats_Admin {


		public function plugins_loaded() {
			add_action( 'admin_menu', function() {
				add_submenu_page( 'tools.php', 'WP Author Stats', 'WP Author Stats', 'publish_posts', WP_Author_Stats_Common::PLUGIN_NAME, array( $this, 'author_stats_page' ) );
			} );

			add_action( 'admin_init', array( $this, 'handle_download' ) );

		}


		public function handle_download() {
			if ( __( 'Download' ) === filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING ) && wp_verify_nonce( $_GET['_nonce'], WP_Author_Stats_Common::PLUGIN_NAME ) ) {
				$this->send_csv();
			}
		}



		private function parse_search_args() {
			$args = array(
				'date_query'     => array(
					'after'         => date( 'm/d/Y', current_time( 'timestamp') -  ( DAY_IN_SECONDS * 365 ) ),
					'before'        => date( 'm/d/Y', current_time( 'timestamp') ),
					)
				);

			if ( ! empty( filter_input( INPUT_GET, 'from', FILTER_SANITIZE_STRING ) ) ) {
				$args['date_query']['after'] = trim( filter_input( INPUT_GET, 'from', FILTER_SANITIZE_STRING ) );
			}

			if ( ! empty( filter_input( INPUT_GET, 'to', FILTER_SANITIZE_STRING ) ) ) {
				$args['date_query']['before'] = trim( filter_input( INPUT_GET, 'to', FILTER_SANITIZE_STRING ) );
			}

			return $args;

		}


		public function author_stats_page() {

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-datepicker' );

			wp_enqueue_style( 'wp-author-stats-jquery-ui', 'https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css' );

			$args = $this->parse_search_args();

			$author_stats = $this->get_author_stats( $args );

			?>
			<div class="wrap wp-author-stats">
				<h2><?php echo esc_html( get_bloginfo( 'name' ) ); ?> <?php _e( 'Author Stats', WP_Author_Stats_Common::PLUGIN_NAME ); ?></h2>

				<form method="get" action="<?php echo admin_url( 'tools.php' ); ?>">
					<input type="hidden" name="page" value="<?php echo esc_attr( WP_Author_Stats_Common::PLUGIN_NAME ); ?>" />
					<input type="hidden" name="_nonce" value="<?php echo esc_attr( wp_create_nonce( WP_Author_Stats_Common::PLUGIN_NAME ) ); ?>" />

					From: <input type="text" name="from" class="from datepicker" value="<?php echo esc_attr( $args['date_query']['after'] ); ?>"  />
					To: <input type="text" name="to" class="to datepicker" value="<?php echo esc_attr( $args['date_query']['before'] ); ?>"  />
					<input type="submit" name="action" class="button primary" value="<?php _e( 'Search' ); ?>" />
					<input type="submit" name="action" class="button download" value="<?php _e( 'Download' ); ?>" />

					<table class="wp-list-table widefat fixed striped pages">
						<thead>
							<tr>
								<th><?php _e( 'Name' ); ?></th>
								<th><?php _e( 'Posts' ); ?></th>
								<th><?php _e( 'Total Views' ); ?></th>
								<th><?php _e( 'Avg. Views' ); ?></th>
								<th><?php _e( 'Total Word Count' ); ?></th>
								<th><?php _e( 'Avg. Word Count' ); ?></th>
							</tr>

						</thead>
						<tbody>
							<?php $this->output_rows( $author_stats ); ?>
						</tbody>
					</table>

				</form>


			</div>

			<script>

				jQuery( document ).ready( function() {
					jQuery( '.wp-author-stats .datepicker').datepicker();
				} );

			</script>
			<?php

		}

		private function send_csv( ) {

			$args = $this->parse_search_args();
			$stats = $this->get_author_stats( $args );

			$from = date( 'Y-m-d', strtotime( $args['date_query']['after'] ) );
			$to = date( 'Y-m-d', strtotime( $args['date_query']['before'] ) );

			header( 'Content-Type: application/csv' );
			header( 'Content-disposition: attachment; filename=' . sanitize_key( get_bloginfo( 'name' ) ) . '-' . $from . '-' . $to . '.csv' );

			echo '"Name","Posts","Total Views","Avg. Views","Total Word Count","Avg. Word Count"';
			echo PHP_EOL;
			$this->output_rows( $stats, 'csv' );
			die();

		}


		private function output_rows( $author_stats, $format = 'html' ) {

			foreach ( $author_stats as $author_id => $data ) {
				switch ( $format ) {
					case 'html';
						?>
							<tr>
								<td>
									<?php echo esc_html( $data['display_name'] ); ?>
								</td>
								<td>
									<?php echo esc_html( number_format( $data['post_count'] ) ); ?>
								</td>
								<td>
									<?php echo esc_html( number_format( $data['total_pageviews'] ) ); ?>
								</td>
								<td>
									<?php echo esc_html( number_format( $data['total_pageviews'] / $data['post_count'], 1 ) ); ?>
								</td>
								<td>
									<?php echo esc_html( number_format( $data['total_wordcount'] ) ); ?>
								</td>
								<td>
									<?php echo esc_html( number_format( $data['total_wordcount'] / $data['post_count'], 1 ) ); ?>
								</td>
							</tr>
						<?php
						break;

					case 'csv';
						$this->echo_csv_value( $data['display_name'] );
						$this->echo_csv_value( $data['post_count'] );
						$this->echo_csv_value( $data['total_pageviews'] );
						$this->echo_csv_value( number_format( $data['total_pageviews'] / $data['post_count'], 1, '.', '' ) );
						$this->echo_csv_value( $data['total_wordcount'] );
						$this->echo_csv_value( number_format( $data['total_wordcount'] / $data['post_count'], 1, '.' , '' ), false );
						echo PHP_EOL;
						break;

				}
			}

		}

		private function echo_csv_value( $value, $comma = true ) {
			echo '"' . str_replace( '"', '""', $value ) . '"';
			if ( $comma ) {
				echo ',';
			}
		}


		private function get_author_stats( $args ) {

			$args = wp_parse_args( $args, array(
				'post_status'             => 'publish',
				'post_type'               => 'post',
				'posts_per_page'          => -1,
				'update_post_meta_cache'  => false,
				'update_post_term_cache'  => false,
				'no_found_rows'           => true,
				)
			);

			$authors = array();
			$posts = array();

			global $post;
			$query = new WP_Query( $args );
			while ( $query->have_posts() ) {
				$query->the_post();
				$posts[] = $post;
			}

			wp_reset_postdata();

			foreach ( $posts as $p ) {
				if ( ! array_key_exists( $p->post_author, $authors ) ) {
					$author = new WP_User( $p->post_author );
					$authors[ $p->post_author ] = array(
						'display_name'    => $author->display_name,
						'post_count'      => 0,
						'total_pageviews' => 0,
						'total_wordcount' => 0,
						);
				}

				$authors[ $p->post_author ]['post_count']++;
				$authors[ $p->post_author ]['total_wordcount'] += str_word_count( strip_tags( $p->post_content ) );


				if ( function_exists( 'stats_get_csv' ) ) {

					$args = array(
						'days'      => -1,
						'limit'     => -1,
						'post_id'   => $p->ID,
					);


					$result = stats_get_csv('postviews', $args);
					if ( ! empty( $result ) ) {
						$authors[ $p->post_author ]['total_pageviews'] += absint( $result[0]['views'] );
					}

				}

			}

			//$diff = date_diff( new DateTime( $args['date_query']['after'] ), new DateTime( $args['date_query']['before'] ) );



			return $authors;

		}


	}

}
