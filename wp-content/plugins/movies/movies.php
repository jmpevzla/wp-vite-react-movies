<?php
/**
* Plugin Name: Ejemplo de movies
* Plugin URI: http://wp-movies.org
* Description: Este plugin es para el tutorial de freeCodeCamp con react
* Version: 1.0.0
* Author: Jose Perez
* Author URI: https://github.com/jmpevzla
* Requires at least: 5.0
* Tested up to: 5.9
*
* Text Domain: movies
* Domain path: /
*/
defined( 'ABSPATH' ) or die( '¡sin trampas!' );

function movies_post_type() {

	$labels = array(
		'name'                  => __( 'Movies', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => __( 'Movie', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Movies', 'text_domain' ),
		'name_admin_bar'        => __( 'Movie', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Movie', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Movie', 'text_domain' ),
		'edit_item'             => __( 'Edit Movie', 'text_domain' ),
		'update_item'           => __( 'Update Movie', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);

	$args = array(
		'label'                 => __( 'Movie', 'text_domain' ),
		'description'           => __( 'Our featured films.', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail'),
		'taxonomies'            => array( '' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_in_rest'          => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);

  register_post_type( 'movies', $args );
  
}

add_action( 'init', 'movies_post_type', 0 );

function change_title_text( $title ){
    $screen = get_current_screen();
    if ( 'movies' == $screen->post_type ) {
      $title = 'Enter movie name here';
    }
    return $title;
}
  
add_filter( 'enter_title_here', 'change_title_text' );

function genre_meta_box_callback() {
    global $post;
    $custom = get_post_custom($post->ID);
    $genre = $custom["movie_category"][0];
    ?>
    <input style="width:100%" name="movie_category" value="<?php 
    echo $genre; ?>" />
    <?php
};

function genre_meta_box() {
    add_meta_box(
      'global-notice',
      __( 'Category', 'sitepoint' ),
      'genre_meta_box_callback',
      'movies',
      'side',
      'low'
    );
  }

function save_genre(){
    global $post;
    update_post_meta($post->ID, "movie_category", $_POST["movie_category"]);
};

add_action( 'add_meta_boxes', 'genre_meta_box' );
add_action( 'save_post', 'save_genre' );

function get_genre_meta_field( $object, $field_name, $value ) {
    return get_post_meta($object['id'])[$field_name][0];
};

function register_genre_as_rest_field() {
    register_rest_field(
      'movies',
      'movie_category',
      array(
        'get_callback' => 'get_genre_meta_field',
        'update_callback' => null,
        'schema' => null,
      )
    );
};

add_action( 'rest_api_init', 'register_genre_as_rest_field' );

function post_featured_image_json( $data, $post, $context ) {
    $featured_image_id = $data->data['featured_media'];
    $featured_image_url = wp_get_attachment_image_src( $featured_image_id, 'original' );
  
    if( $featured_image_url ) {
      $data->data['featured_image_url'] = $featured_image_url[0];
    }
  
    return $data;
}
  
add_filter( 'rest_prepare_movies', 'post_featured_image_json', 10, 3 );

function filter_rest_data( $data, $post, $request ) {
    $_data = $data->data;
    $params = $request->get_params();
    if ( ! isset( $params['id'] ) ) {
      unset( $_data['date'] );
      unset( $_data['slug'] );
      unset( $_data['date_gmt'] );
      unset( $_data['modified'] );
      unset( $_data['modified_gmt'] );
      unset( $_data['guid'] );
      unset( $_data['type'] );
    };
    $data->data = $_data;
    return $data;
};
  
add_filter( 'rest_prepare_movies', 'filter_rest_data', 10, 3 );