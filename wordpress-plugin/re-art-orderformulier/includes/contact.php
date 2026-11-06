<?php
/**
 * Contactformulier voor nieuwe klanten: controleren, bewaren en mailen.
 * Een contactverzoek krijgt geen nummer.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bouwt een schoon contactverzoek op uit de ingezonden gegevens.
 */
function reart_of_build_contact( array $data, array &$errors ) {
	$text = function ( $key, $max = 200 ) use ( $data ) {
		$v = isset( $data[ $key ] ) && is_scalar( $data[ $key ] ) ? sanitize_text_field( (string) $data[ $key ] ) : '';
		return mb_substr( $v, 0, $max );
	};
	$contact = array(
		'naam'     => $text( 'naam' ),
		'datum'    => wp_date( 'Y-m-d' ),
		'email'    => sanitize_email( $text( 'email' ) ),
		'tel'      => $text( 'tel', 40 ),
		'straat'   => $text( 'straat' ),
		'postcode' => $text( 'postcode', 20 ),
		'plaats'   => $text( 'plaats', 100 ),
		'land'     => $text( 'land', 100 ),
		'bericht'  => isset( $data['bericht'] ) && is_scalar( $data['bericht'] ) ? mb_substr( trim( sanitize_textarea_field( (string) $data['bericht'] ) ), 0, 5000 ) : '',
		'lang'     => ( isset( $data['lang'] ) && 'en' === $data['lang'] ) ? 'en' : 'nl',
	);
	if ( '' === $contact['naam'] ) {
		$errors[] = 'naam of bedrijfsnaam ontbreekt';
	}
	if ( ! is_email( $contact['email'] ) ) {
		$errors[] = 'e-mailadres is ongeldig';
	}
	if ( '' === $contact['bericht'] ) {
		$errors[] = 'de vraag ontbreekt';
	}
	return $contact;
}

/**
 * Bewaart het contactverzoek onder "Aanvragen", herkenbaar aan de titel "Contactverzoek – naam".
 */
function reart_of_save_contact( array $contact ) {
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'reart_order',
			'post_status' => 'publish',
			'post_title'  => 'Contactverzoek – ' . $contact['naam'],
		),
		true
	);
	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return 0;
	}
	update_post_meta( $post_id, '_reart_of_type', 'contact' );
	update_post_meta( $post_id, '_reart_of_contact', $contact );
	return $post_id;
}

/**
 * Overzicht van het contactverzoek als HTML-tabel (mails en beheer).
 */
function reart_of_contact_html( array $contact, $lang = 'nl' ) {
	$en  = 'en' === $lang;
	$row = function ( $label, $value ) {
		return '<tr><td style="padding:3px 16px 3px 0;color:#5b616d;vertical-align:top;white-space:nowrap">' . esc_html( $label ) . '</td>'
			. '<td style="padding:3px 0;color:#15171c">' . esc_html( $value ) . '</td></tr>';
	};
	$h  = '<table role="presentation" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.5;border-collapse:collapse">';
	$h .= $row( $en ? 'Date' : 'Datum', reart_of_format_date( $contact['datum'], $lang ) );
	$h .= $row( $en ? 'Name / company' : 'Naam / bedrijf', $contact['naam'] );
	$h .= $row( $en ? 'Email' : 'E-mail', $contact['email'] );
	if ( '' !== $contact['tel'] ) {
		$h .= $row( $en ? 'Phone' : 'Telefoon', $contact['tel'] );
	}
	$address = reart_of_address( $contact );
	if ( '' !== $address ) {
		$h .= $row( $en ? 'Address' : 'Adres', $address );
	}
	$h .= '</table>';
	$h .= '<h3 style="font-size:15px;margin:22px 0 6px;color:#091925;border-top:1px solid #dcdfe3;padding-top:12px">' . ( $en ? 'Your question' : 'Vraag' ) . '</h3>';
	$h .= '<p style="font-size:14px;line-height:1.6;margin:0">' . nl2br( esc_html( $contact['bericht'] ) ) . '</p>';
	return $h;
}

/**
 * Mail naar Re-Art: "Nieuw contactverzoek van <naam>". Beantwoorden gaat direct naar de klant.
 */
function reart_of_mail_contact_admin( array $contact ) {
	$subject = 'Nieuw contactverzoek van ' . $contact['naam'];
	$intro   = '<p style="font-size:14px;line-height:1.5;margin:0 0 18px">Er is een nieuw contactverzoek binnengekomen van een nieuwe klant. Beantwoord deze mail om direct naar de klant te mailen.'
		. ( 'en' === $contact['lang'] ? ' <strong>Let op: de klant heeft het Engelstalige formulier gebruikt.</strong>' : '' ) . '</p>';
	$html    = reart_of_email_wrap( 'Nieuw contactverzoek van ' . $contact['naam'], $intro, reart_of_contact_html( $contact, 'nl' ), 'nl' );
	return wp_mail( reart_of_recipient(), $subject, $html, reart_of_html_headers( array( 'email' => $contact['email'], 'name' => $contact['naam'] ) ) );
}

/**
 * Bevestiging aan de klant, in de taal van het formulier.
 */
function reart_of_mail_contact_customer( array $contact ) {
	if ( 'en' === $contact['lang'] ) {
		$subject = 'Thank you for your message to Re-Art';
		$title   = 'Thank you for your message';
		$intro   = '<p style="font-size:14px;line-height:1.6;margin:0 0 12px">Thank you for getting in touch with Re-Art. We have received your contact request.</p>'
			. '<p style="font-size:14px;line-height:1.6;margin:0 0 18px">We aim to reply within 24 hours. Below you will find a copy of your message.</p>';
	} else {
		$subject = 'Bedankt voor je bericht aan Re-Art';
		$title   = 'Bedankt voor je bericht';
		$intro   = '<p style="font-size:14px;line-height:1.6;margin:0 0 12px">Bedankt voor je bericht aan Re-Art. We hebben je contactverzoek goed ontvangen.</p>'
			. '<p style="font-size:14px;line-height:1.6;margin:0 0 18px">We streven ernaar je binnen 24 uur te antwoorden. Hieronder vind je een kopie van je bericht.</p>';
	}
	$html = reart_of_email_wrap( $title, $intro, reart_of_contact_html( $contact, $contact['lang'] ), $contact['lang'] );
	return wp_mail( $contact['email'], $subject, $html, reart_of_html_headers( array( 'email' => reart_of_recipient(), 'name' => 'Re-Art' ) ) );
}

/**
 * Verwerkt een contactverzoek (na de spamcontrole in reart_of_handle_order).
 */
function reart_of_handle_contact( array $data, $en ) {
	$errors  = array();
	$contact = reart_of_build_contact( $data, $errors );
	if ( $errors ) {
		return reart_of_error( $en ? 'Some details are missing or invalid. Please check the form.' : 'Niet alles is goed ingevuld: ' . implode( '; ', $errors ) . '.', 400 );
	}
	$post_id = reart_of_save_contact( $contact );
	if ( ! $post_id ) {
		return reart_of_error( $en ? 'Your message could not be saved.' : 'Je bericht kon niet worden opgeslagen.', 500 );
	}
	$sent_admin = reart_of_mail_contact_admin( $contact );
	$sent_cust  = reart_of_mail_contact_customer( $contact );
	update_post_meta( $post_id, '_reart_of_mail', array( 'admin' => $sent_admin, 'klant' => $sent_cust ) );
	return new WP_REST_Response( array( 'ok' => true ), 200 );
}
