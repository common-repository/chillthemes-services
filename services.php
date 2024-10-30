<?php

/*
Plugin Name: ChillThemes Services
Plugin URI: http://wordpress.org/plugins/chillthemes-services
Description: Enables a post type to display services for use in any of our Chill Themes.
Version: 1.0
Author: ChillThemes
Author URI: http://chillthemes.net
Author Email: itismattadams@gmail.com
License:

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/* Setup the plugin. */
add_action( 'plugins_loaded', 'chillthemes_services_setup' );

/* Register plugin activation hook. */
register_activation_hook( __FILE__, 'chillthemes_services_activation' );
	
/* Register plugin activation hook. */
register_deactivation_hook( __FILE__, 'chillthemes_services_deactivation' );

/* Plugin setup function. */
function chillthemes_services_setup() {

	/* Define the plugin version. */
	define( 'CHILLTHEMES_SERVICES_VER', '1.0' );

	/* Get the plugin directory URI. */
	define( 'CHILLTHEMES_SERVICES_URI', plugin_dir_url( __FILE__ ) );

	/* Load translations on the backend. */
	if ( is_admin() )
		load_plugin_textdomain( 'chillthemes-services', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/* Register the custom post type. */
	add_action( 'init', 'chillthemes_register_services' );

}

/* Do things on plugin activation. */
function chillthemes_services_activation() {
	flush_rewrite_rules();
}

/* Do things on plugin deactivation. */
function chillthemes_services_deactivation() {
	flush_rewrite_rules();
}

/* Register the post type. */
function chillthemes_register_services() {

	/* Set the post type labels. */
	$services_labels = array(
		'name'					=> __( 'Services', 'ChillThemes' ),
		'singular_name'			=> __( 'Service', 'ChillThemes' ),
		'all_items'				=> __( 'Services', 'ChillThemes' ),
		'add_new_item'			=> __( 'Add New Service', 'ChillThemes' ),
		'edit_item'				=> __( 'Edit Service', 'ChillThemes' ),
		'new_item'				=> __( 'New Service', 'ChillThemes' ),
		'view_item'				=> __( 'View Service', 'ChillThemes' ),
		'search_items'			=> __( 'Search Service', 'ChillThemes' ),
		'not_found'				=> __( 'No services found', 'ChillThemes' ),
		'not_found_in_trash'	=> __( 'No services in trash', 'ChillThemes' )
	);

	/* Define the post type arguments. */
	$services_args = array(
		'can_export'		=> true,
		'capability_type'	=> 'post',
		'has_archive'		=> true,
		'labels'			=> $services_labels,
		'public'			=> true,
		'query_var'			=> 'service',
		'rewrite'			=> array( 'slug' => 'services', 'with_front' => false ),
		'supports'			=> array( 'editor', 'thumbnail', 'title' )
	);

	/* Register the post type. */
	register_post_type( apply_filters( 'chillthemes_services', 'services' ), apply_filters( 'chillthemes_services_args', $services_args ) );

}

/* Sort the order of the posts using AJAX. */
function chillthemes_services_sorting_page() {
	$chillthemes_services_sort = add_submenu_page( 'edit.php?post_type=services', __( 'Sort Services', 'ChillThemes' ), __( 'Sort', 'ChillThemes' ), 'edit_posts', basename( __FILE__ ), 'chillthemes_services_post_sorting_interface' );

	add_action( 'admin_print_scripts-' . $chillthemes_services_sort, 'chillthemes_services_scripts' );
	add_action( 'admin_print_styles-' . $chillthemes_services_sort, 'chillthemes_services_styles' );
}
add_action( 'admin_menu', 'chillthemes_services_sorting_page' );

/* Create the AJAX sorting interface. */
function chillthemes_services_post_sorting_interface() {
   $services_members = new WP_Query(
    	array(
    		'orderby' => 'menu_order',
    		'order' => 'ASC',
    		'posts_per_page' => -1,
    		'post_type' => 'services'
    	)
    );
?>

	<div class="wrap">

		<?php screen_icon( 'tools' ); ?>

		<h2><?php _e( 'Sort Services', 'ChillThemes' ); ?></h2>

		<p><?php _e( 'Drag and drop the items into the order in which you want them to display.', 'ChillThemes' ); ?></p>			

		<ul id="chillthemes-services-list">

			<?php while ( $services_members->have_posts() ) : $services_members->the_post(); if ( get_post_status() == 'publish' ) : ?>

				<li id="<?php the_id(); ?>" class="menu-item">

					<dl class="menu-item-bar">

						<dt class="menu-item-handle">
							<span class="menu-item-title"><?php the_title(); ?></span>
						</dt><!-- .menu-item-handle -->

					</dl><!-- .menu-item-bar -->

					<ul class="menu-item-transport"></ul>

				</li><!-- .menu-item -->

			<?php endif; endwhile; wp_reset_postdata(); ?>

		</ul><!-- #chillthemes-services-list -->

	</div><!-- .wrap -->

<?php }

/* Save the order of the items when it is modified. */
function chillthemes_services_save_sorted_order() {
	global $wpdb;

	$order = explode( ',', $_POST['order'] );
	$counter = 0;

	foreach( $order as $services_id ) {
		$wpdb->update( $wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $services_id ) );
		$counter++;
	}

	die(1);
}
add_action( 'wp_ajax_services_sort', 'chillthemes_services_save_sorted_order' );

/* Load the scripts required for the AJAX sorting. */
function chillthemes_services_scripts() {
	wp_enqueue_script( 'jquery-ui-sortable' );
 	wp_enqueue_script( 'chillthemes-services-sorting', CHILLTHEMES_SERVICES_URI . '/js/sort.js' );
}

/* Load the styles required for the AJAX sorting. */
function chillthemes_services_styles() {
	wp_enqueue_style( 'nav-menu' );
}

?>