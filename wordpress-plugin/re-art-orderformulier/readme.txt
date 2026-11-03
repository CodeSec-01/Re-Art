=== Re-Art Orderformulier ===
Contributors: re-art
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later

Interactief orderformulier van Re-Art – Special Art productions, met live certificaat-voorbeeld.

== Beschrijving ==

* Plaats het formulier op een pagina met de shortcode [reart_orderformulier].
* Tweetalig (NL/EN): de taal volgt Polylang of de taal van de site. Forceren kan met [reart_orderformulier lang="en"] of lang="nl".
* Elke order wordt gemaild naar Re-Art (standaard info@re-art.com, aan te passen via Instellingen → Re-Art Orderformulier).
* De klant krijgt automatisch een bevestigingsmail met het orderoverzicht.
* Alle orders worden bewaard in het menu "Orders" in het WordPress-beheer.
* Spamfilter: verborgen veld, minimale invultijd en maximaal 5 orders per 10 minuten per bezoeker.
* De server controleert elke keuze; alleen geldige opties worden geaccepteerd.

== Installatie ==

1. Plugins → Nieuwe plugin → Plugin uploaden → kies re-art-orderformulier.zip → Nu installeren → Activeren.
2. Maak een pagina aan en voeg het blok "Shortcode" toe met [reart_orderformulier].
3. Installeer een SMTP-plugin (bijv. WP Mail SMTP) zodat de mails betrouwbaar aankomen.
4. Stel bij Instellingen → Privacy de privacyverklaring in; het formulier linkt daar automatisch naar.

== Changelog ==

= 1.1.0 =
* Formulier en klantmail tweetalig (Nederlands / Engels).
* Adresvelden toegevoegd: straat en huisnummer, postcode, plaats, land (niet verplicht).
* Ordernummer begint met RE- in plaats van RA-.
* Mail aan Re-Art: "Nieuwe aanvraag". Klantmail: "Bedankt voor uw aanvraag".

= 1.0.0 =
* Eerste versie.
