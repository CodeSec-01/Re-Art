<?php
/**
 * E-mails naar Re-Art (altijd Nederlands) en naar de klant (in de taal van het formulier).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Vertaling van labels en waarden naar het Engels voor de klantmail.
 * De order wordt intern in het Nederlands opgeslagen.
 */
function reart_of_en( $text ) {
	static $map = array(
		// Labels.
		'Aanvraagnummer'  => 'Request number',
		'Datum'           => 'Date',
		'Naam / bedrijf'  => 'Name / company',
		'E-mail'          => 'Email',
		'Telefoon'        => 'Phone',
		'Adres'           => 'Address',
		'Werk'            => 'Artwork',
		'Opmerkingen'     => 'Comments',
		'Product'         => 'Product',
		'Afwerking'       => 'Finishing',
		'Blindpreeg'      => 'Blind embossing',
		'Nummeren'        => 'Numbering',
		'Opplakken op'    => 'Mounted on',
		'Ophanging'       => 'Hanging',
		'Lijst'           => 'Frame',
		'Witrand'         => 'White border',
		'Uitvoering'      => 'Finish',
		'Mat'             => 'Matte',
		'Na de scan'      => 'After the scan',
		'Atelier'         => 'Studio',
		'UV-topcoating'   => 'UV top coating',
		'Type coating'    => 'Coating type',
		'Opspannen'       => 'Stretching',
		'Type frame'      => 'Frame type',
		'Diepte frame'    => 'Frame depth',
		'Papiersoort'     => 'Paper category',
		'Papiertype'      => 'Paper type',
		'Ophangprofiel'   => 'Hanging profile',
		'Type scan'       => 'Scan type',
		'Kleurproofing'   => 'Colour proofing',
		'Titel & formaat' => 'Title & size',
		'Certificaat'     => 'Certificate',
		'Kunstenaar'      => 'Artist',
		'Titel werk'      => 'Artwork title',
		'Editie'          => 'Edition',
		'Totale editie'   => 'Total edition',
		// Waarden.
		'Ja'              => 'Yes',
		'Nee'             => 'No',
		'Geen'            => 'None',
		'Hout'            => 'Wood',
		'Losse print'     => 'Loose print',
		'Opgeplakt'       => 'Mounted',
		'Ingelijst'       => 'Framed',
		'1 mm Aluminium'  => '1 mm aluminium',
		'15 mm ophangprofiel'   => '15 mm hanging profile',
		'25 mm ophangprofiel'   => '25 mm hanging profile',
		'Witte lijst met nerf'  => 'White frame with wood grain',
		'Zwarte lijst met nerf' => 'Black frame with wood grain',
		'Blanke lijst'          => 'Natural wood frame',
		'Digitaal bestand'      => 'Digital file',
		'Digitaal bestand + voorbereid' => 'Digital file + print-ready',
		'Kleurproofing'         => 'Colour proofing',
		'Standaard scan'        => 'Standard scan',
	);
	return isset( $map[ $text ] ) ? $map[ $text ] : $text;
}

/**
 * Leesbare datum, bijv. "3 november 2026" of "3 November 2026".
 */
function reart_of_format_date( $ymd, $lang = 'nl' ) {
	$ts = strtotime( $ymd . ' 12:00:00' );
	if ( ! $ts ) {
		return $ymd;
	}
	if ( 'en' === $lang ) {
		return gmdate( 'j F Y', $ts );
	}
	$maanden = array( 1 => 'januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december' );
	return gmdate( 'j', $ts ) . ' ' . $maanden[ (int) gmdate( 'n', $ts ) ] . ' ' . gmdate( 'Y', $ts );
}

/**
 * Het adres op één regel, of leeg.
 */
function reart_of_address( array $order ) {
	$city  = trim( ( isset( $order['postcode'] ) ? $order['postcode'] : '' ) . ' ' . ( isset( $order['plaats'] ) ? $order['plaats'] : '' ) );
	$parts = array_filter( array( isset( $order['straat'] ) ? $order['straat'] : '', $city, isset( $order['land'] ) ? $order['land'] : '' ), 'strlen' );
	return implode( ', ', $parts );
}

/**
 * Aanvraagoverzicht als HTML-tabel (ook gebruikt in het beheer).
 *
 * @param array  $order Order.
 * @param string $lang  'nl' of 'en'.
 */
function reart_of_summary_html( array $order, $lang = 'nl' ) {
	$tr   = function ( $s ) use ( $lang ) {
		return 'en' === $lang ? reart_of_en( $s ) : $s;
	};
	$blue = '#12285a';
	$row  = function ( $label, $value ) use ( $tr ) {
		return '<tr><td style="padding:3px 16px 3px 0;color:#5b616d;vertical-align:top;white-space:nowrap">' . esc_html( $tr( $label ) ) . '</td>'
			. '<td style="padding:3px 0;color:#15171c">' . nl2br( esc_html( $tr( $value ) ) ) . '</td></tr>';
	};

	$h  = '<table role="presentation" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.5;border-collapse:collapse">';
	$h .= $row( 'Aanvraagnummer', $order['no'] );
	$h .= $row( 'Datum', reart_of_format_date( $order['datum'], $lang ) );
	$h .= $row( 'Naam / bedrijf', $order['naam'] );
	$h .= $row( 'E-mail', $order['email'] );
	if ( '' !== $order['tel'] ) {
		$h .= $row( 'Telefoon', $order['tel'] );
	}
	$address = reart_of_address( $order );
	if ( '' !== $address ) {
		$h .= $row( 'Adres', $address );
	}
	$h .= '</table>';

	foreach ( $order['lines'] as $i => $line ) {
		$h .= '<h3 style="font-size:15px;margin:22px 0 6px;color:' . $blue . ';border-top:1px solid #dcdfe3;padding-top:12px">' . esc_html( $tr( 'Werk' ) ) . ' ' . ( $i + 1 ) . ' &nbsp;·&nbsp; ' . (int) $line['aantal'] . ' ×</h3>';
		$h .= '<table role="presentation" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.5;border-collapse:collapse">';
		foreach ( $line['details'] as $d ) {
			$h .= $row( $d[0], $d[1] );
		}
		$h .= '</table>';
	}

	if ( '' !== $order['opmerkingen'] ) {
		$h .= '<h3 style="font-size:15px;margin:22px 0 6px;color:' . $blue . ';border-top:1px solid #dcdfe3;padding-top:12px">' . esc_html( $tr( 'Opmerkingen' ) ) . '</h3>';
		$h .= '<p style="font-size:14px;line-height:1.5;margin:0">' . nl2br( esc_html( $order['opmerkingen'] ) ) . '</p>';
	}
	return $h;
}

/**
 * Omhulsel voor de e-mail in Re-Art stijl.
 */
function reart_of_email_wrap( $title, $intro_html, $body_html, $lang = 'nl' ) {
	return '<!DOCTYPE html><html lang="' . esc_attr( $lang ) . '"><body style="margin:0;background:#f3f4f2;font-family:Aptos,Segoe UI,Helvetica,Arial,sans-serif;color:#15171c">'
		. '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f2"><tr><td align="center" style="padding:24px 12px">'
		. '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background:#ffffff;border:1px solid #dcdfe3">'
		. '<tr><td style="background:#12285a;color:#ffffff;padding:18px 28px;font-size:20px;font-weight:700;letter-spacing:.02em">RE-ART <span style="font-weight:400;font-size:12px;letter-spacing:.14em;text-transform:uppercase;opacity:.85">&nbsp;Special Art productions</span></td></tr>'
		. '<tr><td style="padding:28px">'
		. '<h1 style="font-size:20px;margin:0 0 12px;color:#12285a">' . esc_html( $title ) . '</h1>'
		. $intro_html
		. $body_html
		. '</td></tr>'
		. '<tr><td style="padding:14px 28px;border-top:1px solid #dcdfe3;font-size:12px;color:#5b616d">Re-Art · The Netherlands · <a href="https://www.re-art.com" style="color:#12285a">www.re-art.com</a></td></tr>'
		. '</table></td></tr></table></body></html>';
}

function reart_of_html_headers( array $reply_to = array() ) {
	// Afzender: het Re-Art adres uit de instellingen (standaard info@re-art.com) in plaats van wordpress@re-art.com.
	$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: Re-Art <' . reart_of_recipient() . '>' );
	if ( ! empty( $reply_to['email'] ) && is_email( $reply_to['email'] ) ) {
		$name      = str_replace( array( "\r", "\n", '"', '<', '>' ), '', (string) $reply_to['name'] );
		$headers[] = 'Reply-To: "' . $name . '" <' . $reply_to['email'] . '>';
	}
	return $headers;
}

/**
 * Mail naar Re-Art (Nederlands). Antwoorden gaan direct naar de klant.
 */
function reart_of_mail_admin( array $order ) {
	$subject = 'Nieuwe aanvraag ' . $order['no'] . ' – ' . $order['naam'];
	$intro   = '<p style="font-size:14px;line-height:1.5;margin:0 0 18px">Er is een nieuwe aanvraag binnengekomen via het aanvraagformulier. Beantwoord deze mail om direct naar de klant te mailen.'
		. ( 'en' === $order['lang'] ? ' <strong>Let op: de klant heeft het Engelstalige formulier gebruikt.</strong>' : '' ) . '</p>';
	$html    = reart_of_email_wrap( 'Nieuwe aanvraag ' . $order['no'], $intro, reart_of_summary_html( $order, 'nl' ), 'nl' );
	return wp_mail( reart_of_recipient(), $subject, $html, reart_of_html_headers( array( 'email' => $order['email'], 'name' => $order['naam'] ) ) );
}

/**
 * Bevestiging aan de klant, in de taal van het formulier.
 */
function reart_of_mail_customer( array $order ) {
	if ( 'en' === $order['lang'] ) {
		$subject = 'Your request at Re-Art (' . $order['no'] . ')';
		$title   = 'Thank you for your request';
		$intro   = '<p style="font-size:14px;line-height:1.6;margin:0 0 12px">Thank you for filling in the request form.</p>'
			. '<p style="font-size:14px;line-height:1.6;margin:0 0 18px">We will process your request in our system and send you a quote or order confirmation including the costs and delivery time. If we have any questions, we will contact you by email.</p>';
	} else {
		$subject = 'Je aanvraag bij Re-Art (' . $order['no'] . ')';
		$title   = 'Bedankt voor je aanvraag';
		$intro   = '<p style="font-size:14px;line-height:1.6;margin:0 0 12px">Bedankt voor het invullen van het aanvraagformulier.</p>'
			. '<p style="font-size:14px;line-height:1.6;margin:0 0 18px">We verwerken je aanvraag in ons systeem en sturen je een offerte of opdrachtbevestiging met daarin de kosten en de levertijd. Hebben we nog vragen, dan nemen we per e-mail contact met je op.</p>';
	}
	$html = reart_of_email_wrap( $title, $intro, reart_of_summary_html( $order, $order['lang'] ), $order['lang'] );
	return wp_mail( $order['email'], $subject, $html, reart_of_html_headers( array( 'email' => reart_of_recipient(), 'name' => 'Re-Art' ) ) );
}
