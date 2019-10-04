<?php
/**
 * Show all our fields under My Account, Checkout, Registration, etc.
 *
 * @since		{{VERSION}}
 *
 * @package PDS
 * @subpackage PDS/woocommerce
 */

defined( 'ABSPATH' ) || die();

final class PDS_WooCommerce_Fields {
	
	/**
	 * PDS_WooCommerce_Fields constructor.
	 *
	 * @since {{VERSION}}
	 */
    function __construct() {
		
		add_action( 'woocommerce_edit_account_form', array( $this, 'add_to_my_account' ) );
		add_action( 'woocommerce_register_form', array( $this, 'add_to_registration' ) );
		add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'add_to_checkout' ) );
		
		add_action( 'woocommerce_save_account_details', array( $this, 'save_data' ) ); // My Account
		add_action( 'woocommerce_created_customer', array( $this, 'save_data' ) ); // Checkout/Register
		
		// After checkout, updates order meta
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_order_meta' ), 10, 2 );
		
		add_filter( 'woocommerce_checkout_posted_data', array( $this, 'add_to_posted_data' ) );
		
	}
	
	/**
	 * Add our fields to the My Account Page
	 * 
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function add_to_my_account() {
		
		$user = wp_get_current_user();
		$customer = new WC_Customer( $user->ID );
		$fields = $this->get_fields();
		
		foreach ( $fields as $key => $args ) {
			
			if ( ! $this->is_field_visible( $args ) ) {
				continue;
			}
			
			$value = '';
			
			if ( strpos( $key, 'acf_' ) === 0 ) {
				
				if ( isset( $args['fallback_value'] ) ) {
					
					$value = $customer->{ $args['fallback_value'] }();
					
				}
				
				$value = ( $acf = get_field( str_replace( 'acf_', '', $key ), "user_$user->ID" ) ) ? $acf : $value;
				
				woocommerce_form_field( $key, $args, $value );
				
			}
			else {
				
				$value = get_user_meta( $user->ID, $key, true );
				
				woocommerce_form_field( $key, $args, $value );
				
			}
			
		}
		
	}
	
	/**
	 * Add Fields to the Registration Page
	 * 
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function add_to_registration() {
		
		$user = wp_get_current_user();
		$customer = new WC_Customer( $user->ID );
		$fields = $this->get_fields();
		
		foreach ( $fields as $key => $args ) {
			
			if ( ! $this->is_field_visible( $args ) ) {
				continue;
			}
			
			$value = '';
			
			if ( strpos( $key, 'acf_' ) === 0 ) {
				
				if ( isset( $args['fallback_value'] ) ) {
					
					$value = $customer->{ $args['fallback_value'] }();
					
				}
				
				$value = ( $acf = get_field( str_replace( 'acf_', '', $key ), "user_$user->ID" ) ) ? $acf : $value;
				
				woocommerce_form_field( $key, $args, $value );
				
			}
			else {
				
				$value = get_user_meta( $user->ID, $key, true );
				
				woocommerce_form_field( $key, $args, $value );
				
			}
			
		}
		
	}
	
	/**
	 * Add the Institution Name field to Checkout
	 * 
	 * @param		array $checkout_fields Checkout Fields
	 *                                         
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		array Checkout Fields
	 */
	public function add_to_checkout( $checkout_fields ) {
		
		$user = wp_get_current_user();
		$customer = new WC_Customer( $user->ID );
		$fields = $this->get_fields();
		
		foreach ( $fields as $key => $args ) {
			
			if ( ! $this->is_field_visible( $args ) ) {
				continue;
			}
			
			$value = '';
			
			if ( strpos( $key, 'acf_' ) === 0 ) {
				
				if ( isset( $args['fallback_value'] ) ) {
					
					$value = $customer->{ $args['fallback_value'] }();
					
				}
				
				$value = ( $acf = get_field( str_replace( 'acf_', '', $key ), "user_$user->ID" ) ) ? $acf : $value;
				
				woocommerce_form_field( $key, $args, $value );
				
			}
			else {
				
				$value = get_user_meta( $user->ID, $key, true );
				
				woocommerce_form_field( $key, $args, $value );
				
			}
			
		}
		
	}
	
	/**
	 * Save our added fields
	 * 
	 * @param		integer $user_id User ID
	 *                               
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function save_data( $user_id ) {
		
		$fields = $this->get_fields();
		
		foreach ( $fields as $key => $args ) {
			
			if ( ! $this->is_field_visible( $args ) ) {
				continue;
			}
			
			$value = isset( $_POST[ $key ] ) ? $_POST[ $key ] : '';
			
			if ( strpos( $key, 'acf_' ) === 0 ) {
				
				$key = str_replace( 'acf_', '', $key );
				
				update_field( $key, $value, "user_$user_id" );
				
			}
			else {
				
				update_user_meta( $user_id, $key, $value );
				
			}
			
		}
		
	}
	
	/**
	 * Saves data from the Checkout Form
	 * 
	 * @param		integer $order_id    Order ID
	 * @param		array   $posted_data $_POST
	 *                              
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function save_order_meta( $order_id, $posted_data ) {
		
		$order = wc_get_order( $order_id );
		$user_id = $order->get_user_id();
		
		$fields = $this->get_fields();
		
		foreach ( $fields as $key => $args ) {
			
			if ( ! $this->is_field_visible( $args ) ) {
				continue;
			}
			
			$value = isset( $posted_data[ $key ] ) ? $posted_data[ $key ] : '';
			
			if ( strpos( $key, 'acf_' ) === 0 ) {
				
				$key = str_replace( 'acf_', '', $key );
				
				update_field( $key, $value, "user_$user_id" );
				
			}
			else {
				
				update_user_meta( $user_id, $key, $value );
				
			}
			
		}
		
	}
	
	/**
	 * WooCommerce needs to know that our data is there
	 * 
	 * @param 		array $data Data to be Posted
	 *                                 
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		array Data to be Posted
	 */
	public function add_to_posted_data( $data ) {
		
		global $_POST;
		
		$fields = $this->get_fields();
		
		foreach ( $fields as $key => $args ) {

			if ( isset( $_POST[ $key ] ) ) {
				$data[ $key ] = $_POST[ $key ];
			}
			
		}
		
		return $data;
		
	}
	
	/**
	 * Get the fields that we're adding to WooCommerce views
	 * 
	 * @access		private
	 * @since		{{VERSION}}
	 * @return		array WooCommerce Fields
	 */
	private function get_fields() {
		
		return apply_filters( 'pds_woocommerce_fields', array(
			'billing_phone' => array(
				'id' => 'billing_phone',
                'label' => __( 'Phone Number' ),
                'hide_in_account' => false,
				'hide_in_admin' => false,
				'hide_in_checkout' => true,
				'hide_in_registration' => false,
                'fallback_value' => 'get_billing_phone', // Method to call on the WC_Customer object
                'class' => array(
                    'woocommerce-form-row',
                    'woocommerce-form-row--wide',
                    'form-row-wide',
                ),
			),
		) );
		
	}
	
	/**
	 * Determine if a Field should not be shown/saved on a certain page
	 * 
	 * @param		array   $field_args Field Args
	 *                                   
	 * @access		private
	 * @since		{{VERSION}}
	 * @return		boolean Show/Hide
	 */
	private function is_field_visible( $field_args ) {
		
		$visible = true;
		$action = filter_input( INPUT_POST, 'action' );
 
		if ( is_admin() && 
			! empty( $field_args['hide_in_admin'] ) ) {
			$visible = false;
		}
		elseif ( ( is_account_page() || $action === 'save_account_details' ) && 
				is_user_logged_in() && 
				! empty( $field_args['hide_in_account'] ) ) {
			$visible = false;
		}
		elseif ( ( is_account_page() || $action === 'save_account_details' ) && 
				! is_user_logged_in() && 
				! empty( $field_args['hide_in_registration'] ) ) {
			$visible = false;
		} elseif ( is_checkout() && 
				  ! empty( $field_args['hide_in_checkout'] ) ) {
			$visible = false;
		}
 
		return $visible;
		
	}
	
}

$instance = new PDS_WooCommerce_Fields();