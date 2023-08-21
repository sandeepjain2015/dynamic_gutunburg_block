<?php
/**
 * Plugin Name:       Dynamic gutunburg block 
 * Description:       Dynamic gutunburg block.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            sandeepjainlive
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       dynamic-block
 *
 * @package           dynamic-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_dynamic_block_init() {
	register_block_type(
		__DIR__ . '/build',
		array(
			'render_callback' => 'dynamic_block_recent_posts',
		)
	);
}
add_action( 'init', 'create_dynamic_block_init' );
/**
 * Generate a dynamic block for displaying recent posts.
 *
 * @param array $attributes The attributes for the block.
 * @return string The generated HTML for the dynamic block.
 */
function dynamic_block_recent_posts( $attributes ) {

	$args = array(
		'posts_per_page'      => $attributes['postsToShow'],
		'post_status'         => 'publish',
		'order'               => $attributes['order'],
		'orderby'             => $attributes['orderBy'],
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	$query        = new WP_Query();
	$latest_posts = $query->query( $args );

	$li_html = '';

	foreach ( $latest_posts as $post ) {
		$post_link = esc_url( get_permalink( $post ) );
		$title     = get_the_title( $post );

		if ( ! $title ) {
			$title = __( '(no title)', 'dynamic-block' );
		}

		$li_html .= '<li>';

		$li_html .= sprintf(
			'<a class="dynamic-block-recent-posts__post-title" href="%1$s">%2$s</a>',
			esc_url( $post_link ),
			$title
		);

		$li_html .= '</li>';

	}

	$classes = array( 'dynamic-block-recent-posts' );

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	$heading = $attributes['showHeading'] ? $attributes['heading'] : '';

	return sprintf(
		'%1$s<ul %2$s>%3$s</ul>',
		$heading,
		$wrapper_attributes,
		$li_html
	);
}
