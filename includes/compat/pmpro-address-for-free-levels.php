<?php
/**
 * Stop adding billing fields to everywhere
 */
function pmpro_euinv_pmproaffl_init() {
	remove_action( 'init', 'pmpro_euinv_pmproaffl_init_include_address_fields_at_checkout', 30 );
}
add_action( 'init', 'pmpro_euinv_pmproaffl_init', 29 );

/**
 * Add billing fields only if level is not free
 */
function pmpro_euinv_pmproaffl_remove_free_billing() {
	global $pmpro_level;
	
	if ( ! pmpro_isLevelFree( $pmpro_level ) ) {
		add_filter( 'pmpro_include_billing_address_fields', '__return_true' );
	} else {
		remove_action( 'pmpro_checkout_boxes', 'pmproaffl_pmpro_checkout_boxes_require_address' );
		remove_filter( 'pmpro_checkout_order_free', 'pmproaffl_pmpro_checkout_order_free' );
	}
}
add_action( 'pmpro_checkout_boxes', 'pmpro_euinv_pmproaffl_remove_free_billing', 9 );
