<?php

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

// Include files
function pd_include_files(){
  if (get_post_type() == 'event')
    wp_enqueue_script('child', get_stylesheet_directory_uri().'/js/child.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'pd_include_files');

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