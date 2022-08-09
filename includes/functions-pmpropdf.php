<?php
/**
 * Some useful function which should be available in PMPro PDF Invoices Add On
 */

if ( ! function_exists( 'pmpropdf_invoice_delete_for_order' ) ) {
	/**
	 * Delete the invoice's pdf file for an order
	 *
	 * @param MemberOrder $morder
	 *
	 * @return bool true on success (or if nothing to do) or false on failure
	 */
	function pmpropdf_invoice_delete_for_order( $morder ) {
		$invoice_dir = pmpropdf_get_invoice_directory_or_url();

		$invoice_name = pmpropdf_generate_invoice_name( $morder->code );
		$invoice_path = $invoice_dir . $invoice_name;

		if ( file_exists( $invoice_path ) ) {
			return unlink( $invoice_path );
		}

		return true;
	}
}

if ( ! function_exists( 'pmpropdf_invoice_exists_for_order' ) ) {
	/**
	 * Check if an invoice's pdf file exists for an order
	 *
	 * @param MemberOrder $morder
	 *
	 * @return bool
	 */
	function pmpropdf_invoice_exists_for_order( $morder ) {
		$invoice_dir = pmpropdf_get_invoice_directory_or_url();

		$invoice_name = pmpropdf_generate_invoice_name( $morder->code );
		$invoice_path = $invoice_dir . $invoice_name;

		return file_exists( $invoice_path );
	}
}

if ( ! function_exists( 'pmpropdf_invoice_regenerate_for_order' ) ) {
	/**
	 * Regenerate an invoice's pdf file for an order
	 *
	 * @param MemberOrder $morder
	 *
	 * @return false|string the path to the pdf file or false on failure
	 */
	function pmpropdf_invoice_regenerate_for_order( $morder ) {
		global $wpdb;

		pmpropdf_invoice_delete_for_order( $morder );

		// simulate pmpro-pdf-invoices behaviour
		$order_data = $wpdb->get_row( "SELECT * FROM $wpdb->pmpro_membership_orders WHERE id = '" . esc_sql( $morder->id ) . "' LIMIT 1" );

		return pmpropdf_generate_pdf( $order_data );
	}
}

