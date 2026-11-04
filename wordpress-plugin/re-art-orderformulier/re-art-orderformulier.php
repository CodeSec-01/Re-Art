<?php
/**
 * Plugin Name:       Re-Art Aanvraagformulier
 * Description:       Interactief aanvraagformulier (NL/EN) van Re-Art met live certificaat-voorbeeld. Plaats het formulier met de shortcode [reart_orderformulier] (of [reart_orderformulier lang="en"]). Aanvragen worden gemaild en bewaard onder "Aanvragen".
 * Version:           1.2.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Re-Art
 * Author URI:        https://www.re-art.com
 * Text Domain:       reart-orderformulier
 * License:           GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

define( 'REART_OF_VERSION', '1.2.0' );
define( 'REART_OF_FILE', __FILE__ );
define( 'REART_OF_DEFAULT_RECIPIENT', 'info@re-art.com' );

require_once __DIR__ . '/includes/options.php';
require_once __DIR__ . '/includes/order.php';
require_once __DIR__ . '/includes/mail.php';
require_once __DIR__ . '/includes/admin.php';

/**
 * Het e-mailadres waar orders naartoe gaan.
 */
function reart_of_recipient() {
	$email = get_option( 'reart_of_recipient', REART_OF_DEFAULT_RECIPIENT );
	return is_email( $email ) ? $email : REART_OF_DEFAULT_RECIPIENT;
}

/* -------------------------------------------------------------------------
 * Front-end: assets en shortcode
 * ---------------------------------------------------------------------- */

add_action( 'wp_enqueue_scripts', 'reart_of_register_assets' );
function reart_of_register_assets() {
	$url = plugin_dir_url( REART_OF_FILE ) . 'assets/';
	wp_register_style( 'reart-orderformulier', $url . 'orderformulier.css', array(), REART_OF_VERSION );
	wp_register_script( 'reart-orderformulier', $url . 'orderformulier.js', array(), REART_OF_VERSION, true );
}

/**
 * Taal van het formulier: 'nl' of 'en'. Volgorde: shortcode-attribuut, Polylang, taal van de site.
 */
function reart_of_detect_lang( $requested = '' ) {
	$requested = strtolower( (string) $requested );
	if ( in_array( $requested, array( 'nl', 'en' ), true ) ) {
		return $requested;
	}
	if ( function_exists( 'pll_current_language' ) ) {
		$pll = pll_current_language( 'slug' );
		if ( $pll ) {
			return 0 === strpos( $pll, 'nl' ) ? 'nl' : 'en';
		}
	}
	return 0 === strpos( determine_locale(), 'nl' ) ? 'nl' : 'en';
}

add_shortcode( 'reart_orderformulier', 'reart_of_shortcode' );
function reart_of_shortcode( $atts = array() ) {
	$atts          = shortcode_atts( array( 'lang' => '' ), $atts, 'reart_orderformulier' );
	$reart_of_lang = reart_of_detect_lang( $atts['lang'] );

	wp_enqueue_style( 'reart-orderformulier' );
	wp_enqueue_script( 'reart-orderformulier' );
	wp_add_inline_script(
		'reart-orderformulier',
		'window.REARTOF = ' . wp_json_encode(
			array(
				'restUrl' => esc_url_raw( rest_url( 'reart/v1/order' ) ),
				'contact' => reart_of_recipient(),
				'lang'    => $reart_of_lang,
			)
		) . ';',
		'before'
	);

	$reart_of_privacy = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';

	ob_start();
	include __DIR__ . '/templates/form.php';
	return ob_get_clean();
}

/* -------------------------------------------------------------------------
 * REST-endpoint: POST /wp-json/reart/v1/order
 * ---------------------------------------------------------------------- */

add_action( 'rest_api_init', 'reart_of_register_route' );
function reart_of_register_route() {
	register_rest_route(
		'reart/v1',
		'/order',
		array(
			'methods'             => 'POST',
			'callback'            => 'reart_of_handle_order',
			'permission_callback' => '__return_true',
		)
	);
}

/**
 * Verwerkt een ingezonden order: spamcontrole, validatie, opslaan en mailen.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function reart_of_handle_order( WP_REST_Request $request ) {
	$data = $request->get_json_params();
	if ( ! is_array( $data ) ) {
		return reart_of_error( 'De aanvraag kon niet worden gelezen.', 400 );
	}
	$en = isset( $data['lang'] ) && 'en' === $data['lang'];

	// Spam: verborgen veld ingevuld of binnen 3 seconden verstuurd. Doe alsof het gelukt is.
	$honeypot = isset( $data['website'] ) ? trim( (string) $data['website'] ) : '';
	$elapsed  = isset( $data['elapsed'] ) ? (int) $data['elapsed'] : 0;
	if ( '' !== $honeypot || $elapsed < 3000 ) {
		return new WP_REST_Response( array( 'ok' => true, 'orderNo' => '' ), 200 );
	}

	// Maximaal 5 orders per 10 minuten per bezoeker.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'onbekend';
	$key = 'reart_of_rl_' . md5( $ip );
	$hit = (int) get_transient( $key );
	if ( $hit >= 5 ) {
		return reart_of_error( $en ? 'Too many requests were sent in a short time. Please try again in a few minutes.' : 'Er zijn te veel aanvragen achter elkaar verstuurd. Probeer het over enkele minuten opnieuw.', 429 );
	}

	$errors = array();
	$order  = reart_of_build_order( $data, $errors );
	if ( $errors ) {
		return reart_of_error( $en ? 'Some details are missing or invalid. Please check the form.' : 'Niet alles is goed ingevuld: ' . implode( '; ', array_slice( $errors, 0, 3 ) ) . '.', 400 );
	}

	set_transient( $key, $hit + 1, 10 * MINUTE_IN_SECONDS );

	$post_id = reart_of_save_order( $order );
	if ( ! $post_id ) {
		return reart_of_error( $en ? 'The request could not be saved.' : 'De aanvraag kon niet worden opgeslagen.', 500 );
	}
	$order['no'] = get_post_meta( $post_id, '_reart_of_no', true );

	$sent_admin = reart_of_mail_admin( $order );
	$sent_cust  = reart_of_mail_customer( $order );
	update_post_meta( $post_id, '_reart_of_mail', array( 'admin' => $sent_admin, 'klant' => $sent_cust ) );

	return new WP_REST_Response( array( 'ok' => true, 'orderNo' => $order['no'] ), 200 );
}

function reart_of_error( $message, $status ) {
	return new WP_REST_Response( array( 'ok' => false, 'message' => $message ), $status );
}
