<?php

/**
 * @param \MemberOrder $morder
 *
 * @return false|int
 */
function pmpro_euinv_hasInvoice( $morder ) {
	global $wpdb;

	$invoice_id = $wpdb->get_var( "SELECT id FROM $wpdb->pmpro_invoices WHERE membership_order_id = $morder->id" );

	if ( $invoice_id ) {
		return intval( $invoice_id );
	}

	return false;
}

/**
 * @param \MemberOrder $morder
 *
 * @return object|null
 */
function pmpro_euinv_getInvoice( $morder ) {
	global $wpdb;

	$invoice_data = $wpdb->get_row( "SELECT * FROM $wpdb->pmpro_invoices WHERE membership_order_id = $morder->id LIMIT 1" );

	if ( $invoice_data ) {
		return $invoice_data;
	}

	return null;
}

/**
 * @param \MemberOrder $morder
 * @param int $invoice_datetime
 *
 * @return object|null
 */
function pmpro_euinv_addInvoice( $morder, $invoice_datetime = null ) {
	global $wpdb;

	$curr_year      = date( 'Y' );
	$sequential_num = pmpro_euinv_internalSequence_getNextNumber( true );

	$prefix       = 'A';
	$invoice_code = $prefix . $curr_year . '-' . $sequential_num;

	$invoice_timestamp = date( 'Y-m-d H:i:s', $invoice_datetime ? $invoice_datetime : time() );

	pmpro_insert_or_replace(
		$wpdb->pmpro_invoices,
		array(
			'membership_order_id' => $morder->id,
			'code'                => $invoice_code,
			'date'                => $invoice_timestamp,
			'notes'               => '',
		),
		array(
			'%d', //membership_order_id
			'%s', //code
			'%s', //date
			'%s', //notes
		)
	);

	return pmpro_euinv_getInvoice( $morder );
}

/**
 * @param int $invoice_id
 *
 * @return false|string the path to the pdf file or false on failure
 */
function pmpro_euinv_generateInvoicePdf( $invoice_id ) {
	global $wpdb;

	$order_id = $wpdb->get_var( $q = "SELECT membership_order_id FROM $wpdb->pmpro_invoices WHERE id = '" . esc_sql( $invoice_id ) . "' LIMIT 1" );
	if ( ! $order_id ) {
		return false;
	}

	$morder = new MemberOrder();
	$morder->getMemberOrderByID( $order_id );
	if ( empty( $morder->id ) ) {
		return false;
	}

	return pmpropdf_invoice_regenerate_for_order( $morder );
}
