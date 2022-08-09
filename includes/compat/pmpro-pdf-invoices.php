<?php

/**
 * Change exported pdf invoice filename, using the invoice code generated
 *
 * @param string $invoice_name
 * @param string $order_code
 *
 * @return mixed|string
 */
function pmpro_euinv_pmpro_pdf_invoice_name( $invoice_name, $order_code ) {
	$morder = new MemberOrder();
	$morder->getMemberOrderByCode( $order_code );

	$invoice = pmpro_euinv_getInvoice( $morder );
	if ( $invoice ) {
		$invoice_name = $invoice->code . ".pdf";
	}

	return $invoice_name;
}

add_filter( 'pmpro_pdf_invoice_name', 'pmpro_euinv_pmpro_pdf_invoice_name', 10, 2 );

/**
 * Add the invoice id to the invoice template variables
 *
 * @param array $variables
 * @param \WP_User $user
 * @param object $order_data
 *
 * @return array
 */
function pmpro_euinv_pdf_invoice_add_custom_variables( $variables, $user, $order_data ) {
	$morder = new MemberOrder();
	$morder->getMemberOrderByCode( $order_data->code );

	$invoice = pmpro_euinv_getInvoice( $morder );
	if ( $invoice ) {
		$variables["{{invoice_id}}"] = $invoice->code;
	}

	return $variables;
}

add_filter( 'pmpro_pdf_invoice_custom_variables', 'pmpro_euinv_pdf_invoice_add_custom_variables', 10, 3 );
