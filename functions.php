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
	
	ob_start();
	
	return $template;
	
}, 99 );

/**
 * Forcibly injects Google Tag Manager code after the opening <body> tag without needing to edit header.php in the Parent Theme
 * 
 * @since		1.0.8
 * @return		string HTML Content
 */
add_filter( 'shutdown', function() {

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
 * @since 1.0.9
 */

add_filter( 'script_loader_tag', 'pds_defer_js', 10, 3 );

function pds_defer_js( $tag, $handle, $src ) {

	if ( strpos( $handle, 'jquery' ) === false ) {

		$tag = str_replace( 'src', 'defer="defer" src', $tag );

	}

    return $tag;
}