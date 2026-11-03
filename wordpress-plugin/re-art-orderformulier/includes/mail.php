<?php
/**
 * E-mails naar Re-Art en naar de klant.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Leesbare datum, bijv. "3 november 2026".
 */
function reart_of_format_date( $ymd ) {
	$ts = strtotime( $ymd . ' 12:00:00' );
	return $ts ? wp_date( 'j F Y', $ts ) : $ymd;
}

/**
 * Orderoverzicht als HTML-tabel (ook gebruikt in het beheer).
 */
function reart_of_summary_html( array $order ) {
	$blue = '#12285a';
	$row  = function ( $label, $value ) {
		return '<tr><td style="padding:3px 16px 3px 0;color:#5b616d;vertical-align:top;white-space:nowrap">' . esc_html( $label ) . '</td>'
			. '<td style="padding:3px 0;color:#15171c">' . nl2br( esc_html( $value ) ) . '</td></tr>';
	};

	$h  = '<table role="presentation" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.5;border-collapse:collapse">';
	$h .= $row( 'Ordernummer', $order['no'] );
	$h .= $row( 'Datum', reart_of_format_date( $order['datum'] ) );
	$h .= $row( 'Naam / bedrijf', $order['naam'] );
	$h .= $row( 'E-mail', $order['email'] );
	if ( '' !== $order['tel'] ) {
		$h .= $row( 'Telefoon', $order['tel'] );
	}
	$h .= '</table>';

	foreach ( $order['lines'] as $i => $line ) {
		$h .= '<h3 style="font-size:15px;margin:22px 0 6px;color:' . $blue . ';border-top:1px solid #dcdfe3;padding-top:12px">Werk ' . ( $i + 1 ) . ' &nbsp;·&nbsp; ' . (int) $line['aantal'] . ' ×</h3>';
		$h .= '<table role="presentation" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.5;border-collapse:collapse">';
		foreach ( $line['details'] as $d ) {
			$h .= $row( $d[0], $d[1] );
		}
		$h .= '</table>';
	}

	if ( '' !== $order['opmerkingen'] ) {
		$h .= '<h3 style="font-size:15px;margin:22px 0 6px;color:' . $blue . ';border-top:1px solid #dcdfe3;padding-top:12px">Opmerkingen</h3>';
		$h .= '<p style="font-size:14px;line-height:1.5;margin:0">' . nl2br( esc_html( $order['opmerkingen'] ) ) . '</p>';
	}
	return $h;
}

/**
 * Omhulsel voor de e-mail in Re-Art stijl.
 */
function reart_of_email_wrap( $title, $intro_html, $body_html ) {
	return '<!DOCTYPE html><html lang="nl"><body style="margin:0;background:#f3f4f2;font-family:Aptos,Segoe UI,Helvetica,Arial,sans-serif;color:#15171c">'
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
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	if ( ! empty( $reply_to['email'] ) && is_email( $reply_to['email'] ) ) {
		$name      = str_replace( array( "\r", "\n", '"', '<', '>' ), '', (string) $reply_to['name'] );
		$headers[] = 'Reply-To: "' . $name . '" <' . $reply_to['email'] . '>';
	}
	return $headers;
}

/**
 * Mail naar Re-Art. Antwoorden gaan direct naar de klant.
 */
function reart_of_mail_admin( array $order ) {
	$subject = 'Nieuwe order ' . $order['no'] . ' – ' . $order['naam'];
	$intro   = '<p style="font-size:14px;line-height:1.5;margin:0 0 18px">Er is een nieuwe order binnengekomen via het orderformulier. Beantwoord deze mail om direct naar de klant te mailen.</p>';
	$html    = reart_of_email_wrap( 'Nieuwe order ' . $order['no'], $intro, reart_of_summary_html( $order ) );
	return wp_mail( reart_of_recipient(), $subject, $html, reart_of_html_headers( array( 'email' => $order['email'], 'name' => $order['naam'] ) ) );
}

/**
 * Bevestiging aan de klant met dezelfde tekst als het bedankscherm.
 */
function reart_of_mail_customer( array $order ) {
	$subject = 'Uw order bij Re-Art (' . $order['no'] . ')';
	$intro   = '<p style="font-size:14px;line-height:1.6;margin:0 0 12px">Bedankt voor het invullen van het orderformulier.</p>'
		. '<p style="font-size:14px;line-height:1.6;margin:0 0 18px">Wij verwerken uw order in ons systeem en sturen u een opdrachtbevestiging met daarin de kosten en de levertijd. Mochten wij nog vragen hebben, dan nemen wij per e-mail contact met u op.</p>';
	$html    = reart_of_email_wrap( 'Bedankt voor uw order', $intro, reart_of_summary_html( $order ) );
	return wp_mail( $order['email'], $subject, $html, reart_of_html_headers( array( 'email' => reart_of_recipient(), 'name' => 'Re-Art' ) ) );
}
