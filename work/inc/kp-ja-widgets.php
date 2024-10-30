<?php
/** 
 * Article Widgets
 * 
 * Setup the article widgets
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if the class already exists
if( ! class_exists( 'KP_JA_Widgets' ) ) {

	/** 
     * Class KP_JA_Widgets
     * 
     * The actual class for registering the widgets
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
	class KP_JA_Widgets {

		/** 
         * kp_ja_add_widgets
         * 
         * The method registers the new article widgets
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return void This method does not return anything.
         * 
        */
		public function kp_ja_add_widgets( ) : void {

			// register our widgets
			register_widget( 'KP_JA_Cat_Widget' );
			register_widget( 'KP_JA_Tag_Widget' );
			register_widget( 'KP_JA_Recent_Widget' );
		}

	}

}

// check if the class already exists
if( ! class_exists( 'KP_JA_Recent_Widget' ) ){

	/** 
     * Class KP_JA_Recent_Widget
     * 
     * The actual class for building the latest posts widget
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
	class KP_JA_Recent_Widget extends WP_Widget {

		// fire up our widget functionality
		function __construct( ) {

			// register our widget
			parent::__construct( 'KP_JA_Recent_Widget',
								__( 'JSON Recent Articles', 'kp-json-articles' ),
								array( 'description' => __( 'Displays your recent JSON Articles.', 'kp-json-articles' ) ) );
		}
		
		/** 
         * update
         * 
         * This method updates the fields necessary for the widget
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
		 * @param array $new The array containing the new fields
		 * @param array $old The array containing the pre-existing fields
		 * 
         * @return array Returns an array containing the fields allowed for the widget
         * 
        */
		function update( $new, $old ) : array {
			$_old = $old;

			// set the new title
			$_old['title'] = sanitize_text_field( $new['title'] );
			
			// set the new number of posts
			$_old['numberOfPosts'] = sanitize_text_field( $new['numberOfPosts'] );		
			
			// return
			return $_old;
		}
		
		/** 
         * form
         * 
         * This method builds the form for the widget
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
		 * @param array $inst The array containing the instance of this widget
		 * 
         * @return void This method does not return anything
         * 
        */
		function form( $inst ) : void {

			// set the title
			$title = ( $inst ) ? sanitize_text_field( $inst['title'] ) : '' ;

			// set the number of posts
			$numberOfPosts = ( $inst ) ? sanitize_text_field( $inst['numberOfPosts'] ) : 5 ;

			?>
			<p>
				<label for="<?php _e( esc_attr( $this -> get_field_id( 'numberOfPosts' ) ) ); ?>"><?php _e( 'Title', 'kp-json-articles' ); ?></label>
				<input class="widefat" id="<?php _e( esc_attr( $this -> get_field_id( 'title' ) ) ); ?>" name="<?php _e( esc_attr( $this -> get_field_name( 'title' ) ) ); ?>" type="text" value="<?php _e( sanitize_text_field( $title ), 'kp-json-articles' ); ?>" />
			</p>
			<p>
				<label for="<?php _e( esc_attr( $this -> get_field_id( 'numberOfPosts' ) ) ); ?>"><?php _e( 'Number of Posts', 'kp-json-articles' ); ?></label>
				<select id="<?php _e( esc_attr( $this -> get_field_id( 'numberOfPosts' ) ) ); ?>" name="<?php _e( esc_attr( $this -> get_field_name( 'numberOfPosts' ) ) ); ?>">
				<?php

					// we only need a max of 10, so minimize our loop
					for( $x = 1; $x <= 10; ++$x ) {

						// get the selected

						?>
						<option <?php selected( intval( $numberOfPosts ), $x ); ?>value="<?php _e( $x ); ?>"><?php _e( $x ); ?></option>
						<?php
					}
					?>
				</select>
			</p>
			<?php
		}	
		
		/** 
         * widget
         * 
         * This method processes the displaying of the widget
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
		 * @param array $args Array of arguments for the widget
		 * @param array $inst Array containing the instance of this widget
		 * 
         * @return void This method does not return anything
         * 
        */
		function widget( $args, $inst ) : void {

			// extract our arguments
			extract( $args );

			// pull in the widget global
			global $is_widget;

			// set it to true, because let's face it.... this is a widget...
			$is_widget = true;

			// apply the widget title filter, to our configured title
			$title = apply_filters( 'widget_title', $inst['title'] );

			// get the number of posts
			$num_posts = $inst['numberOfPosts'];

			// write the before widget html
			echo $before_widget;

			// if we have a title
			if( $title ) {

				// write out the before title, the title, and the after title html
				echo $before_title . sanitize_text_field( __( $title, 'kp-json-articles' ) ) . $after_title;
			}

			// now pull our recent articles
			$this -> get_kp_ja_recents( $num_posts );

			// write out the end of the widget
			echo $after_widget;
		}
		
		/** 
         * get_kp_ja_recents
         * 
         * This method gets the actual posts
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
		 * @param int $num_posts The number of posts to pull
		 * 
         * @return void this method does not return anything
         * 
        */
		function get_kp_ja_recents( int $num_posts ) : void {
			
			// hold our query arguments
			$_args = array(
				'post_type' => 'kp_json_art', 
				'posts_per_page' => $num_posts, 
				'orderby' => 'date', 
				'order' => 'DESC'
			);

			// run the query
			$qry = new WP_Query( $_args );

			// get our post from the query
			$rs = $qry -> get_posts( );

			// if we actually have a resultset
			if( $rs ){

				// start our listing
				echo '<ul>';

				// loop over the recordset
				foreach( $rs as $p ) {

					// get the permalink to the article
					$_link = get_the_permalink( $p -> ID );

					// write the list item
					echo '<li>';
					echo '	<a href="' . $_link . '" title="' . __( $p -> post_title, 'kp-json-articles' ) . '">';
					echo __( $p -> post_title, 'kp-json-articles' );
					echo '	</a>';
					echo '</li>';
				}

				// end the listing
				echo '</ul>';
			}

			// reset the query object
			wp_reset_query( );
		}
	}

}

// check if the class already exists
if( ! class_exists( 'KP_JA_Tag_Widget' ) ){

	/** 
     * Class KP_JA_Tag_Widget
     * 
     * The actual class for building the tag cloud widget
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
	class KP_JA_Tag_Widget extends WP_Widget {
		
		// fire up our widget functionality
		function __construct( ) {

			// create the actual widget
			parent::__construct( 'KP_JA_Tag_Widget',
								__( 'JSON Article Tags', 'kp-json-articles' ),
								array( 'description' => __( 'Displays your JSON Article Tags Cloud.', 'kp-json-articles' ) ) );
		}
		
		/** 
         * update
         * 
         * This method updates the fields necessary for the widget
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
		 * @param array $new The array containing the new fields
		 * @param arram $old The array containing the pre-existing fields
		 * 
         * @return array Returns an array containing the fields allowed for the widget
         * 
        */
		function update( $new, $old ) : array {
			$_old = $old;

			// set the new title
			$_old['title'] = sanitize_text_field( $new['title'] );

			// return it
			return $_old;
		}
		
		/** 
         * form
         * 
         * This method builds the form for the widget
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
		 * @param array $inst The array containing the instance of this widget
		 * 
         * @return void This method does not return anything
         * 
        */
		function form( $inst ) : void {

			// get our title
			$title = ( $inst ) ? sanitize_text_field( $inst['title'] ) : '' ;
			?>
			<p>
				<label for="<?php _e( esc_attr( $this -> get_field_id( 'title' ) ) ); ?>"><?php _e( 'Title', 'kp-json-articles' ); ?></label>
				<input class="widefat" id="<?php _e( esc_attr( $this -> get_field_id( 'title' ) ) ); ?>" name="<?php _e( esc_attr( $this -> get_field_name( 'title' ) ) ); ?>" type="text" value="<?php _e( sanitize_text_field( $title ), 'kp-json-articles' ); ?>" />
			</p>
			<?php
		}	
		
		/** 
         * widget
         * 
         * This method processes the displaying of the widget
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
		 * @param array $args Array of arguments for the widget
		 * @param array $inst Array containing the instance of this widget
		 * 
         * @return void This method does not return anything
         * 
        */
		function widget( $args, $inst ) : void {

			// extract our arguments
			extract( $args );

			// apply the widget title filter to the new title
			$title = apply_filters( 'widget_title', $inst['title'] );

			// write out the widget start
			echo $before_widget;

			// if we have a title
			if( $title ) {

				// write it out
				echo $before_title . sanitize_text_field( __( $title, 'kp-json-articles' ) ) . $after_title;
			}

			// start writing our tag cloud
			echo '<p>';

			// the tag cloud
			$this -> get_kp_ja_tags( );
			echo '</p>';

			// write out the end of the widget
			echo $after_widget;
		}
		
		/** 
         * get_kp_ja_tags
         * 
         * This method renders the tag cloud
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
		 * 
         * @return void This method does not return anything
         * 
        */
		function get_kp_ja_tags( ) {

			// hook into wp functionality for it
			wp_tag_cloud( array( 'taxonomy' => 'kp_json_art_tags', 'echo' => true ) );
		}
	}

}

// check if the class already exists
if( ! class_exists( 'KP_JA_Cat_Widget' ) ){

	/** 
     * Class KP_JA_Cat_Widget
     * 
     * The actual class for building the category widget
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
	class KP_JA_Cat_Widget extends WP_Widget {
		
		// fire up our widget functionality
		function __construct( ) {

			// create the actual widget
			parent::__construct( 'KP_JA_Cat_Widget',
								__( 'JSON Article Categories', 'kp-json-articles' ),
								array( 'description' => __( 'Displays your JSON Article Categories.', 'kp-json-articles' ) ) );
		}
		
		/** 
         * update
         * 
         * This method updates the fields necessary for the widget
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
		 * @param array $new The array containing the new fields
		 * @param array $old The array containing the pre-existing fields
		 * 
         * @return array Returns an array containing the fields allowed for the widget
         * 
        */
		function update( $new, $old ) : array {
			$_old = $old;

			// set the new title
			$_old['title'] = sanitize_text_field( ( $new['title'] ) ?? '' );

			// set the new hierarchal flag
			$_old['hierarchical'] = ! empty( $new['hierarchical'] ) ? 1 : 0;

			// set the count display
			$_old['count'] = ! empty( $new['count'] ) ? 1 : 0;

			// set the dropdown display
			$_old['dropdown'] = ! empty( $new['dropdown'] ) ? 1 : 0;

			// return our updated fields
			return $_old;
		}
		
		/** 
         * form
         * 
         * This method builds the form for the widget
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
		 * @param array $inst The array containing the instance of this widget
		 * 
         * @return void This method does not return anything
         * 
        */
		function form( $inst ) : void {

			// get the values
			$hierarchical = filter_var( ( $inst['hierarchical'] ) ?? false, FILTER_VALIDATE_BOOLEAN );
			$showcount = filter_var( ( $inst['count'] ) ?? false, FILTER_VALIDATE_BOOLEAN );
			$dropdown = filter_var( ( $inst['dropdown'] ) ?? false, FILTER_VALIDATE_BOOLEAN );
			$title = ( $inst ) ? sanitize_text_field( $inst['title'] ) : '' ;

			?>
			<p>
				<label for="<?php _e( esc_attr( $this -> get_field_id( 'title' ) ) ); ?>"><?php _e( 'Title', 'kp-json-articles' ); ?></label>
				<input class="widefat" id="<?php _e( esc_attr( $this -> get_field_id( 'title' ) ) ); ?>" name="<?php _e( esc_attr( $this -> get_field_name( 'title' ) ), 'kp-json-articles' ); ?>" type="text" value="<?php _e( sanitize_text_field( $title ) ); ?>" />
			</p>
			<div class="all-options">
				<input type="checkbox" class="checkbox" id="<?php _e( esc_attr( $this -> get_field_id( 'count' ) ) ); ?>" name="<?php _e( esc_attr( $this -> get_field_name( 'count' ) ) ); ?>"<?php checked( $showcount ); ?> />
				<label for="<?php _e( esc_attr( $this -> get_field_id( 'count' ) ) ); ?>"><?php _e( 'Show Post Counts', 'kp-json-articles' ); ?></label><br />
				
				<input type="checkbox" class="checkbox" id="<?php _e( esc_attr( $this -> get_field_id( 'hierarchical' ) ) ); ?>" name="<?php _e( esc_attr( $this -> get_field_name( 'hierarchical' ) ) ); ?>"<?php checked( $hierarchical ); ?> />
				<label for="<?php _e( esc_attr( $this -> get_field_id( 'hierarchical' ) ) ); ?>"><?php _e( 'Show Hierarchy', 'kp-json-articles' ); ?></label><br/>
				
				<input type="checkbox" class="checkbox" id="<?php _e( esc_attr( $this -> get_field_id( 'dropdown' ) ) ); ?>" name="<?php _e( esc_attr( $this -> get_field_name( 'dropdown' ) ) ); ?>"<?php checked( $dropdown ); ?> />
				<label for="<?php _e( esc_attr( $this -> get_field_id( 'dropdown' ) ) ); ?>"><?php _e( 'Display as Dropdown', 'kp-json-articles' ); ?></label><br /><br />
			</div>
			<?php
		}	
		
		/** 
         * widget
         * 
         * This method processes the displaying of the widget
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
		 * @param array $args Array of arguments for the widget
		 * @param array $inst Array containing the instance of this widget
		 * 
         * @return void This method does not return anything
         * 
        */
		function widget( $args, $inst ) : void {

			// extract our arguments
			extract( $args );

			// apply the widget title filter to the new title
			$title = apply_filters( 'widget_title', $inst['title'] );

			// write the beginning of the widget
			echo $before_widget;

			// if there is a new title
			if( $title ) {

				// write it out
				echo $before_title . __( $title, 'kp-json-articles' ) . $after_title;
			}

			// write out the categories
			$this -> get_kp_ja_cats( 0, $inst );

			// write out the end of the widget
			echo $after_widget;
		}
		
		/** 
         * get_kp_ja_cats
         * 
         * This method gets the actual post categories
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
		 * @param int $_parent The parent category ID
		 * @param array $inst The array that holds the widgets instance
		 * 
         * @return void this method does not return anything
         * 
        */
		function get_kp_ja_cats( int $_parent, array $inst ) : void {

			// get the settings
			$hierarchical = filter_var( ( $inst['hierarchical'] ) ?? false, FILTER_VALIDATE_BOOLEAN );
			$showcount = filter_var( ( $inst['count'] ) ?? false, FILTER_VALIDATE_BOOLEAN );
			$dropdown = filter_var( ( $inst['dropdown'] ) ?? false, FILTER_VALIDATE_BOOLEAN );

			// get the json article permalink base
			$_ja_rewrite = ( empty( get_ja_option( 'json_permalink' ) ) ) ? 'our-articles' : get_ja_option( 'json_permalink' );

			// we'll also need the set permalink slug from the plugin options
			$_cat_rewrite = ( empty( get_ja_option( 'json_cat_permalink' ) ) ) ? 'category' : get_ja_option( 'json_cat_permalink' );

			// if the drop-down was selected
			if( $dropdown ) {

				// setup our arguments
				$args = array(
					'orderby' => 'name',
					'order' => 'ASC', 
					'show_option_all'    => false,
					'show_option_none'   => '',
					'show_count'         => $showcount,
					'echo'               => 1,
					'hierarchical'       => $hierarchical,
					'class'              => 'form-control',
					'depth'              => 0,
					'taxonomy'           => 'kp_json_art_cats',
					'hide_if_empty'      => true, 
					'parent' => $_parent,
					'show_option_none' => 'Select Category',
					'value_field' => 'slug',
				);

				?>
				<form action="<?php echo get_bloginfo( 'url' ); ?>" method="get">
					<?php 

					// write out our dropdown categories
					wp_dropdown_categories( $args ); 
					
					// we need a bit of extra javascript in here so this performs as expected
					?>
					<script type="text/javascript">
						var dropdown = document.getElementById("cat");
						function onCatChange() {
							if ( dropdown.options[dropdown.selectedIndex].value != -1 ) {
								location.href = "/<?php  _e( $_ja_rewrite ); ?>/<?php  _e( $_cat_rewrite ); ?>/" + dropdown.options[dropdown.selectedIndex].value;
							}
						}
						dropdown.onchange = onCatChange;
					</script>
				</form>
				<?php
			} else {

				// setup our arguments
				$args = array(
					'orderby' => 'name',
					'order' => 'ASC', 
					'show_option_all'    => false,
					'show_option_none'   => '',
					'show_count'         => $showcount,
					'echo'               => 1,
					'hierarchical'       => $hierarchical,
					'class'              => 'form-control',
					'depth'              => 0,
					'taxonomy'           => 'kp_json_art_cats',
					'hide_if_empty'      => true, 
					'parent' => $_parent
				);

				// get the categories
				$terms = get_terms( $args );

				// start our list
				?>
				<ul class="kp_ja-cats-widget-list">
					<?php

					// if we have any results
					if( $terms ) {

						// loop over them
						foreach( $terms as $term ) {

							// get the link
							$term_link = get_term_link( $term );

							// if there isnt a link, just skip this record
							if ( is_wp_error( $term_link ) ) {
								continue;
							}

							// write out the list item
							echo '<li>';
							echo '	<a href="' . esc_url( $term_link ) . '">';
							echo __( $term -> name, 'kp-json-articles' );
							echo '	</a>';
							echo ' (' . $term -> count . ')';

							// now get the children
							$this -> get_kp_ja_cats( $term -> term_id, $inst );

							// end our list
							echo '</li>';
						}

					}
					?>
				</ul>			
				<?php
			}
		}
		
	}

}
