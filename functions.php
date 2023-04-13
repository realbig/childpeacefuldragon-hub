<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Make sure PHP version is correct
if ( ! version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
	wp_die( __( 'ERROR in Theme 2017 theme: PHP version 5.3 or greater is required.' ) );
}

// Make sure no theme constants are already defined (realistically, there should be no conflicts)
if ( defined( 'THEME_VER' ) ||
	defined( 'THEME_URL' ) ||
	defined( 'THEME_DIR' ) ||
	defined( 'THEME_FILE' ) ||
	isset( $theme_fonts ) ) {
	wp_die( __( 'ERROR in Theme 2017 theme: There is a conflicting constant. Please either find the conflict or rename the constant.', 'Constant or Global already in use Error' ) );
}

/**
 * Define Constants based on our Stylesheet Header. Update things only once!
 */
$theme_header = wp_get_theme();

define( 'THEME_VER', $theme_header->get( 'Version' ) );
define( 'THEME_NAME', $theme_header->get( 'Name' ) );
define( 'THEME_URL', get_stylesheet_directory_uri() );
define( 'THEME_DIR', get_stylesheet_directory() );

// Activation email edits

add_filter('wp_mail_from', function() {
	return 'wasentha@peacefuldragonschool.com';
});

add_filter('wp_mail_from_name', function() {
	return 'Peaceful Dragon School';
});

add_filter( 'wpmu_signup_user_notification_subject', function() {
	return 'Activate your Peaceful Dragon User Name.';
}, 10, 4 );

// Show admin bar for logged in users
if ( is_user_logged_in() ) {
	add_filter( 'show_admin_bar', '__return_true' );
}

// Woo phone number
function woo_phone_number() {
	global $woo_options;
	$woo_phone = esc_html( $woo_options['woo_contact_number'] );
	return $woo_phone;
}
// Woo phone number shortcode
add_shortcode('woo_phone', 'woo_phone_number');

// Add contact info to header
add_action('woo_nav_before', 'pd_header_contact');
function pd_header_contact() { ?>
<div class="header-contact">
	<address>1945 Pauline Blvd., Suite B, Ann Arbor, MI 48103</address>
	<h4><?php echo woo_phone_number(); ?></h4>
</div>
<?php }

// Enqueue font stylesheet for Davinci font
function pd_fonts() {
	wp_register_style( 'pd-font', get_stylesheet_directory_uri().'/peacefule-dragon.css' );
	wp_enqueue_style( 'pd-font' );
}
add_action( 'wp_enqueue_scripts', 'pd_fonts' );

// Alter "Gallery" link on the home page boxes
function change_gallery_link($link){
  // Link to page instead of category
  return '/gallery/';
}
add_filter('home_box_gallery_link', 'change_gallery_link');

// Alter "Certifications" link on the home page boxes
function change_certifications_link($link){
  // Link to page instead of category
  return '/certifications/';
}
add_filter('home_box_certifications_link', 'change_certifications_link');

// Alter "Private Sessions" link on the home page boxes
function change_private_sessions_link($link){
  // Link to page instead of category
  return '/private-sessions/';
}
add_filter('home_box_private_sessions_link', 'change_private_sessions_link');

// Alter "Private Sessions" link on the home page boxes
function change_ongoing_classes_link($link){
  // Link to page instead of category
  return '/ongoing-classes/';
}
add_filter('home_box_ongoing_classes_link', 'change_ongoing_classes_link');

add_action( 'gettext', '_pd_registration_title_text' );
function _pd_registration_title_text( $text ) {

	if ( $text == 'Create your account on %s' ) {
		$text = 'Create your account on Peaceful Dragon Schools';
	}

	if ( $text == 'Your account is now activated. <a href="%1$s">Log in</a> or go back to the <a href="%2$s">homepage</a>.' ) {
		$text = sprintf(
			'Your account is now activated. <a href="%1$s">Log in</a> or go back to the <a href="%2$s">homepage</a>.',
			wp_login_url(),
			get_bloginfo( 'url' )
		);
	}

	if ( $text == 'Your account has been activated. You may now <a href="%1$s">log in</a> to the site using your chosen username of &#8220;%2$s&#8221;. Please check your email inbox at %3$s for your password and login instructions. If you do not receive an email, please check your junk or spam folder. If you still do not receive an email within an hour, you can <a href="%4$s">reset your password</a>.' ) {
		$text = sprintf(
			'Your account has been activated. You may now <a href="%1$s">log in</a> to the site using your chosen username. Please check your email inbox for your password and login instructions. If you do not receive an email, please check your junk or spam folder. If you still do not receive an email within an hour, you can <a href="%2$s">reset your password</a>.',
			wp_login_url(),
			get_bloginfo( 'url' ) . '/wp-login.php?action=lostpassword'
		);
	}

	return $text;
}

add_filter( 'manage_users_columns', '_pd_add_user_columns' );
function _pd_add_user_columns( $column ) {
    $column['phone'] = 'Phone';
    return $column;
}

add_filter( 'manage_users_custom_column', '_pd_user_column_data', 10, 3 );
function _pd_user_column_data( $val, $column_name, $user_id ) {
    $user = get_userdata( $user_id );
    switch ($column_name) {
        case 'phone' :
            $phone = get_the_author_meta( 'billing_phone', $user_id );
            return '<a href="tel:1' . preg_replace( '/\D+/', '', $phone ) . '">' . $phone . '</a>';
            break;
        default:
    }
    return $return;
}

add_filter( 'post_type_labels_testimonial', '_pd_testimonial_featured_image_labels' );
function _pd_testimonial_featured_image_labels( $labels ) {
    
    $labels->featured_image  = 'Featured Image (Must be a Square Image)';
    $labels->use_featured_image  = 'Featured Image (Must be a Square Image)';
    
    return $labels;
    
}

/**
 * Register theme files.
 *
 * @since 1.0.0
 */
add_action( 'init', function () {

	global $theme_fonts;

	wp_register_style(
		'parent-theme',
		trailingslashit( get_template_directory_uri() ) . 'style.css'
	);

	// Theme styles
	wp_register_style(
		'pds-theme',
		trailingslashit( THEME_URL ) . 'dist/assets/css/app.css',
		array( 'parent-theme' ),
		defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : THEME_VER
	);

	// Theme script
	wp_register_script(
		'pds-theme',
		THEME_URL . '/js/child.js',
		array( 'jquery' ),
		defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : THEME_VER,
		true
	);
	
	wp_localize_script(
		'pds-theme',
		'pdsTheme',
		apply_filters( 'pds_theme_localize_script', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		) )
	);

	// Theme fonts
	if ( ! empty( $theme_fonts ) ) {
		foreach ( $theme_fonts as $ID => $link ) {
			wp_register_style(
				'pds-theme' . "-font-$ID",
				$link
			);
		}
	}
} );

/**
 * Enqueue theme files.
 *
 * @since 1.0.0
 */
add_action( 'wp_enqueue_scripts', function () {

	global $theme_fonts;

	// Theme styles
	wp_enqueue_style( 'pds-theme' );
	
	if ( get_post_type() == 'event' ) {

		// Theme script
		wp_enqueue_script( 'pds-theme' );
		
	}

	// Theme fonts
	if ( ! empty( $theme_fonts ) ) {
		foreach ( $theme_fonts as $ID => $link ) {
			wp_enqueue_style( 'pds-theme' . "-font-$ID" );
		}
	}

	wp_enqueue_script(
		'fontawesome',
		'//use.fontawesome.com/releases/v5.0.3/js/all.js',
		array(),
		'5.0.3',
		false
	);
	
}, 11 );

if ( class_exists( 'WooCommerce' ) ) {
	require_once __DIR__ . '/includes/woocommerce/add-user-meta.php';
}

add_action( 'woo_header_before', function() {

	if ( function_exists( 'pds_show_inset_alerts' ) ) {
		pds_show_inset_alerts();
	}

} );

/**
 * Add main GTM script
 * 
 * @since	1.0.8
 * @return	void
 */
add_action( 'wp_head', function() {

	?>

	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-NRMZKJZ');</script>
	<!-- End Google Tag Manager -->

	<?php

} );

/**
 * Creates an Object Buffer for us to forcibly inject our Google Tag Manager Code inside of later
 * 
 * @param		string Template File
 * 
 * @since		1.0.8
 * @return		string Template File
 */
add_filter( 'template_include', function( $template ) {
	
	if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) return $template;
	
	ob_start();
	
	return $template;
	
}, 99 );

/**
 * Forcibly injects Google Tag Manager code after the opening <body> tag without needing to edit header.php in the Parent Theme
 * 
 * @since		1.0.8
 * @return		string HTML Content
 */
add_action( 'shutdown', function() {
	
	if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) return;

	$content = ob_get_clean();
	
	ob_start();

	wp_body_open();

	$hook = ob_get_clean();

	$content = preg_replace( '#<body([^>]*)>#i', "<body$1>{$hook}", $content );

	echo $content;
	
}, 0 );

/**
 * Add GTM <noscript> tag
 * 
 * @since	1.0.8
 * @return	void
 */
add_action( 'wp_body_open', function() {

	?>

	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NRMZKJZ" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->

	<?php

} );

/**
 * Defers parsing of JS
 * @since 1.0.11
 */

add_filter( 'script_loader_tag', 'pds_defer_js', 10, 3 );

function pds_defer_js( $tag, $handle, $src ) {

	if ( is_admin() ) return $tag;

    if ( $handle == 'jquery' ) return $tag;

	// Ensures stuff like `wp` is available
	// Also contains a fix for WooCommerce Square
	if ( strpos( $src, 'wp-includes' ) !== false || strpos( $src, 'woocommerce-square' ) !== false ) return $tag;
        
    $tag = str_replace( 'src', 'defer="defer" src', $tag );
    return $tag;
	
}

/**
 * Force enable Gutenberg for this site
 * This Multisite has Classic Editor network-activated and I don't want to have to go through and manually activate it for every other site
 * Note: Some CPTs don't have the necessary settings enabled for Gutenberg to show. Example: Testimonials and Slides
 * 
 * @param   boolean  $bool       Enabled/Disabled
 * @param   string   $post_type  Post Type
 *
 * @since	1.0.13
 * @return  boolean              Enabled/Disabled
 */
add_filter( 'use_block_editor_for_post_type', function( $bool, $post_type ) {

    return true;

}, 999, 2 );

if ( ! function_exists( 'woothemes_get_testimonials' ) ) {

	/**
	 * Reimplements woothemes_get_testimonials() from the WooThemes Testimonials plugin
	 * 
	 * @param  string/array $args  Arguments.
	 * 
	 * @since  1.1.0
	 * @return array/boolean       Array if true, boolean if false
	 */
	function woothemes_get_testimonials( $args = '' ) {

		$defaults = array(
			'limit' => 5,
			'orderby' => 'menu_order',
			'order' => 'DESC',
			'id' => 0,
			'category' => 0
		);

		$args = wp_parse_args( $args, $defaults );

		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'woothemes_get_testimonials_args', $args );

		// The Query Arguments.
		$query_args = array();
		$query_args['post_type'] = 'testimonial';
		$query_args['numberposts'] = $args['limit'];
		$query_args['orderby'] = $args['orderby'];
		$query_args['order'] = $args['order'];
		$query_args['suppress_filters'] = false;

		$ids = explode( ',', $args['id'] );

		if ( 0 < intval( $args['id'] ) && 0 < count( $ids ) ) {
			$ids = array_map( 'intval', $ids );
			if ( 1 == count( $ids ) && is_numeric( $ids[0] ) && ( 0 < intval( $ids[0] ) ) ) {
				$query_args['p'] = intval( $args['id'] );
			} else {
				$query_args['ignore_sticky_posts'] = 1;
				$query_args['post__in'] = $ids;
			}
		}

		// Whitelist checks.
		if ( ! in_array( $query_args['orderby'], array( 'none', 'ID', 'author', 'title', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order', 'meta_value', 'meta_value_num' ) ) ) {
			$query_args['orderby'] = 'date';
		}

		if ( ! in_array( $query_args['order'], array( 'ASC', 'DESC' ) ) ) {
			$query_args['order'] = 'DESC';
		}

		if ( ! in_array( $query_args['post_type'], get_post_types() ) ) {
			$query_args['post_type'] = 'testimonial';
		}

		$tax_field_type = '';

		// If the category ID is specified.
		if ( is_numeric( $args['category'] ) && 0 < intval( $args['category'] ) ) {
			$tax_field_type = 'id';
		}

		// If the category slug is specified.
		if ( ! is_numeric( $args['category'] ) && is_string( $args['category'] ) ) {
			$tax_field_type = 'slug';
		}

		// Setup the taxonomy query.
		if ( '' != $tax_field_type ) {
			$term = $args['category'];
			if ( is_string( $term ) ) { $term = esc_html( $term ); } else { $term = intval( $term ); }
			$query_args['tax_query'] = array( array( 'taxonomy' => 'testimonial-category', 'field' => $tax_field_type, 'terms' => array( $term ) ) );
		}

		// The Query.
		$query = get_posts( $query_args );

		// The Display.
		if ( ! is_wp_error( $query ) && is_array( $query ) && count( $query ) > 0 ) {
			foreach ( $query as $k => $v ) {
				$meta = get_post_custom( $v->ID );

				// Get the image.
				$query[$k]->image = pds_get_woothemes_testimonial_image( $v->ID, $args['size'] );

				foreach ( (array)pds_get_woothemes_get_custom_fields_settings() as $i => $j ) {
					if ( isset( $meta['_' . $i] ) && ( '' != $meta['_' . $i][0] ) ) {
						$query[$k]->$i = $meta['_' . $i][0];
					} else {
						$query[$k]->$i = $j['default'];
					}
				}
			}
		} else {
			$query = false;
		}

		return $query;

	}

}

if ( ! has_action( 'woothemes_testimonials', 'woothemes_testimonials' ) ) {
	add_action( 'woothemes_testimonials', 'woothemes_testimonials' );
}

if ( ! function_exists( 'woothemes_testimonials' ) ) {

	/**
	 * Reimplements woothemes_testimonials() from the WooThemes Testimonials plugin
	 * 
	 * @param  string/array $args  Arguments.
	 * @since  1.1.0
	 * @return void
	 */
	function woothemes_testimonials( $args = '' ) {

		global $post, $more;
	
		$defaults = apply_filters( 'woothemes_testimonials_default_args', array(
			'limit' 			=> 5,
			'per_row' 			=> null,
			'orderby' 			=> 'menu_order',
			'order' 			=> 'DESC',
			'id' 				=> 0,
			'display_author' 	=> true,
			'display_avatar' 	=> true,
			'display_url' 		=> true,
			'effect' 			=> 'fade', // Options: 'fade', 'none'
			'pagination' 		=> false,
			'echo' 				=> true,
			'size' 				=> 50,
			'title' 			=> '',
			'before' 			=> '<div class="widget widget_woothemes_testimonials">',
			'after' 			=> '</div>',
			'before_title' 		=> '<h2>',
			'after_title' 		=> '</h2>',
			'category' 			=> 0,
		) );
	
		$args = wp_parse_args( $args, $defaults );
	
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'woothemes_testimonials_args', $args );
		$html = '';
	
		do_action( 'woothemes_testimonials_before', $args );
	
			// The Query.
			$query = woothemes_get_testimonials( $args );
	
			// The Display.
			if ( ! is_wp_error( $query ) && is_array( $query ) && count( $query ) > 0 ) {
	
				$class = '';
	
				if ( is_numeric( $args['per_row'] ) ) {
					$class .= ' columns-' . intval( $args['per_row'] );
				}
	
				if ( 'none' != $args['effect'] ) {
					$class .= ' effect-' . $args['effect'];
				}
	
				$html .= $args['before'] . "\n";
				if ( '' != $args['title'] ) {
					$html .= $args['before_title'] . esc_html( $args['title'] ) . $args['after_title'] . "\n";
				}
				$html .= '<div class="testimonials component' . esc_attr( $class ) . '">' . "\n";
	
				$html .= '<div class="testimonials-list">' . "\n";
	
				// Begin templating logic.
				$tpl = '<div id="quote-%%ID%%" class="%%CLASS%%" itemprop="review" itemscope itemtype="http://schema.org/Review"><blockquote class="testimonials-text" itemprop="reviewBody">%%TEXT%%</blockquote>%%AVATAR%% %%AUTHOR%%</div>';
				$tpl = apply_filters( 'woothemes_testimonials_item_template', $tpl, $args );
	
				$count = 0;
				foreach ( $query as $post ) { $count++;
					$template = $tpl;
	
					$css_class = 'quote';
					if ( ( is_numeric( $args['per_row'] ) && ( $args['per_row'] > 0 ) && ( 0 == ( $count - 1 ) % $args['per_row'] ) ) || 1 == $count ) { $css_class .= ' first'; }
					if ( ( is_numeric( $args['per_row'] ) && ( $args['per_row'] > 0 ) && ( 0 == $count % $args['per_row'] ) ) || count( $query ) == $count ) { $css_class .= ' last'; }
	
					// Add a CSS class if no image is available.
					if ( isset( $post->image ) && ( '' == $post->image ) ) {
						$css_class .= ' no-image';
					}
	
					setup_postdata( $post );
	
					$author = '';
					$author_text = '';
	
					// If we need to display the author, get the data.
					if ( ( get_the_title( $post ) != '' ) && true == $args['display_author'] ) {
						$author .= '<cite class="author" itemprop="author" itemscope itemtype="http://schema.org/Person">';
	
						$author_name = '<span itemprop="name">' . get_the_title( $post ) . '</span>';
	
						$author .= $author_name;
	
						if ( isset( $post->byline ) && '' != $post->byline ) {
							$author .= ' <span class="title" itemprop="jobTitle">' . $post->byline . '</span><!--/.title-->' . "\n";
						}
	
						if ( true == $args['display_url'] && '' != $post->url ) {
							$author .= ' <span class="url"><a href="' . esc_url( $post->url ) . '" itemprop="url">' . apply_filters( 'woothemes_testimonials_author_link_text', $text = esc_url( $post->url ) ) . '</a></span><!--/.excerpt-->' . "\n";
						}
	
						$author .= '</cite><!--/.author-->' . "\n";
	
						// Templating engine replacement.
						$template = str_replace( '%%AUTHOR%%', $author, $template );
					} else {
						$template = str_replace( '%%AUTHOR%%', '', $template );
					}
	
					// Templating logic replacement.
					$template = str_replace( '%%ID%%', get_the_ID(), $template );
					$template = str_replace( '%%CLASS%%', esc_attr( $css_class ), $template );
	
					if ( isset( $post->image ) && ( '' != $post->image ) && true == $args['display_avatar'] && ( '' != $post->url ) ) {
						$template = str_replace( '%%AVATAR%%', '<a href="' . esc_url( $post->url ) . '" class="avatar-link">' . $post->image . '</a>', $template );
					} elseif ( isset( $post->image ) && ( '' != $post->image ) && true == $args['display_avatar'] ) {
						$template = str_replace( '%%AVATAR%%', $post->image, $template );
					} else {
						$template = str_replace( '%%AVATAR%%', '', $template );
					}
	
					// Remove any remaining %%AVATAR%% template tags.
					$template 	= str_replace( '%%AVATAR%%', '', $template );
					$real_more 	= $more;
					$more      	= 0;
					$content 	= apply_filters( 'woothemes_testimonials_content', apply_filters( 'the_content', get_the_content( __( 'Read full testimonial...', 'our-team-by-woothemes' ) ) ), $post );
					$more      	= $real_more;
					$template 	= str_replace( '%%TEXT%%', $content, $template );
	
					// Assign for output.
					$html .= $template;
	
					if( is_numeric( $args['per_row'] ) && ( $args['per_row'] > 0 ) && ( 0 == $count % $args['per_row'] ) ) {
						$html .= '<div class="fix"></div>' . "\n";
					}
				}
	
				wp_reset_postdata();
	
				$html .= '</div><!--/.testimonials-list-->' . "\n";
	
				if ( $args['pagination'] == true && count( $query ) > 1 && $args['effect'] != 'none' ) {
					$html .= '<div class="pagination">' . "\n";
					$html .= '<a href="#" class="btn-prev">' . apply_filters( 'woothemes_testimonials_prev_btn', '&larr; ' . __( 'Previous', 'woothemes-testimonials' ) ) . '</a>' . "\n";
					$html .= '<a href="#" class="btn-next">' . apply_filters( 'woothemes_testimonials_next_btn', __( 'Next', 'woothemes-testimonials' ) . ' &rarr;' ) . '</a>' . "\n";
					$html .= '</div><!--/.pagination-->' . "\n";
				}
					$html .= '<div class="fix"></div>' . "\n";
				$html .= '</div><!--/.testimonials-->' . "\n";
				$html .= $args['after'] . "\n";
			}
	
			// Allow child themes/plugins to filter here.
			$html = apply_filters( 'woothemes_testimonials_html', $html, $query, $args );
	
			if ( $args['echo'] != true ) { return $html; }
	
			// Should only run is "echo" is set to true.
			echo $html;
	
			do_action( 'woothemes_testimonials_after', $args ); // Only if "echo" is set to true.

	}

}

/**
 * Reimplements Woothemes_Testimonials::get_image()
 * 
 * @param  int 				$id   Post ID.
 * @param  string/array/int $size Image dimension.
 * 
 * @since  1.1.0
 * @return string       	<img> tag
 */
function pds_get_woothemes_testimonial_image( $id, $size ) {

	$response = '';

	if ( has_post_thumbnail( $id ) ) {
		// If not a string or an array, and not an integer, default to 150x9999.
		if ( ( is_int( $size ) || ( 0 < intval( $size ) ) ) && ! is_array( $size ) ) {
			$size = array( intval( $size ), intval( $size ) );
		} elseif ( ! is_string( $size ) && ! is_array( $size ) ) {
			$size = array( 50, 50 );
		}
		$response = get_the_post_thumbnail( intval( $id ), $size, array( 'class' => 'avatar' ) );
	} else {
		$gravatar_email = get_post_meta( $id, '_gravatar_email', true );
		if ( '' != $gravatar_email && is_email( $gravatar_email ) ) {
			$response = get_avatar( $gravatar_email, $size );
		}
	}

	return $response;

}

/**
 * Reimplements Woothemes_Testimonials::get_custom_fields_settings()
 * 
 * @since  1.1.0
 * @return array
 */
function pds_get_woothemes_get_custom_fields_settings() {

	$fields = array();

	$fields['gravatar_email'] = array(
		'name' => __( 'Gravatar E-mail Address', 'woothemes-testimonials' ),
		'description' => sprintf( __( 'Enter in an e-mail address, to use a %sGravatar%s, instead of using the "Featured Image".', 'woothemes-testimonials' ), '<a href="' . esc_url( 'http://gravatar.com/' ) . '" target="_blank">', '</a>' ),
		'type' => 'text',
		'default' => '',
		'section' => 'info'
	);

	$fields['byline'] = array(
		'name' => __( 'Byline', 'woothemes-testimonials' ),
		'description' => __( 'Enter a byline for the customer giving this testimonial (for example: "CEO of WooThemes").', 'woothemes-testimonials' ),
		'type' => 'text',
		'default' => '',
		'section' => 'info'
	);

	$fields['url'] = array(
		'name' => __( 'URL', 'woothemes-testimonials' ),
		'description' => __( 'Enter a URL that applies to this customer (for example: http://woothemes.com/).', 'woothemes-testimonials' ),
		'type' => 'url',
		'default' => '',
		'section' => 'info'
	);

	return $fields;

}

// Prevents the Parent Theme from being upset that this class doesn't exist
if ( ! class_exists( 'Woothemes_Testimonials ' ) ) {
	class Woothemes_Testimonials {

	}

}
