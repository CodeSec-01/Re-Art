=== Re-Art Orderformulier ===
Contributors: re-art
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 1.6.0
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

= 1.6.0 =
* Keuzes blijven bewaard bij wisselen van product (alleen papiertype en framediepte opnieuw kiezen bij andere papiersoort of ander frame).
* Knop "Werk opslaan": controleert het werk en klapt het in tot een overzichtsregel met Bewerken en Verwijderen.
* Melding "Werk verwijderen?" voordat een werk wordt verwijderd.
* "Hulp nodig" en het aanvraagoverzicht blijven in beeld, ook met thema's die dat normaal blokkeren; vaste menubalk wordt herkend.

= 1.5.1 =
* Huisstijlblauw #091925 in formulier, pop-ups, mails en PDF.
* Logo weggehaald bovenaan formulier en PDF (blijft in de "Meer info"-kaders, de mails en het certificaat).
* Startscherm: beide kaarten licht, pas donker bij aanwijzen met de muis.
* "Tijdens kantooruren" verwijderd uit contactbevestiging en teksten.
* Hulpvak "Hulp nodig? Neem contact met ons op" bij de aanvraag (blijft in beeld).
* HD Finish / HD Decor in het Nederlands: "Type afwerking: Wit / Metal", vervolgvraag "Afwerking wit".

= 1.5.0 =
* Startscherm met twee keuzes: "Aanvraag nieuwe klant" en "Aanvraag of bestelling bestaande klant".
* Contactformulier voor nieuwe klanten (gegevens + vraag), direct versturen.
* Mail aan Re-Art: "Nieuw contactverzoek van <naam>"; bevestigingsmail aan de klant (reactie binnen 24 uur).
* Contactverzoeken staan onder "Aanvragen" met het label "Contact" (zonder nummer).
* Startscherm in de vorm van twee keuzekaarten met icoon (ontwerp B).
* Achtergrondkleur huisstijl #F9F9F9 in formulier en mails.

= 1.4.3 =
* Info Cruse Fine Art Scan: onjuiste regel "tot 85 x 450 cm, 400 dpi" verwijderd.

= 1.4.2 =
* Links naar re-art.com ook bij proofing (Na de scan) en bij de papieren.
* Maximaal formaat bij HD Finish (120 x 240 cm) en HD Decor (146 x 296 cm).

= 1.4.1 =
* Diasec-info zonder prijs, tip van Sebastian.
* Links naar re-art.com (nieuw tabblad) bij HD Finish, HD Decor, Cruse Fine Art Scan en Diasec.

= 1.4.0 =
* Nieuw product Diasec (Gloss / Mat / TruLife) met "Meer info over Diasec".
* Bevestigingstekst en klantmail: "offerte of opdrachtbevestiging".

= 1.3.0 =
* Controlescherm "Controleer je aanvraag" voordat de aanvraag wordt verstuurd; overzicht in de huisstijl i.p.v. codelettertype.
* Datum wordt automatisch ingevuld (dag van insturen).
* Aanvraagnummer RE-jjmmdd-0001, per dag oplopend vanaf 0001.
* Overal "je" in plaats van "u" (formulier en klantmail).
* Papier: vraag "Witrand" met "Tip van Re-Art".
* Scan: "Standaard scan" (was LR); Superscan vervallen.

= 1.2.0 =
* Overal "aanvraag" in plaats van "order" (formulier, mails en beheer; menu heet nu "Aanvragen").
* Mails worden verstuurd vanaf het Re-Art adres uit de instellingen (standaard info@re-art.com).
* Papier: blindpreeg, nummeren en afwerking (losse print, opgeplakt met ophanging, ingelijst met lijstkeuze).
* Scan: keuze "Na de scan" (digitaal bestand, digitaal bestand + voorbereid, kleurproofing).
* "Meer info"-pop-ups per product in brochurestijl, met prijzen, tips van het team en afwerkingsfoto's.
* Certificaat-voorbeeld zoals in de brochure: voorbeeldafbeelding, genummerde punten, blindpreeg Certified Art Giclée; nieuw veld "Naam atelier".

= 1.1.0 =
* Formulier en klantmail tweetalig (Nederlands / Engels).
* Adresvelden toegevoegd: straat en huisnummer, postcode, plaats, land (niet verplicht).
* Ordernummer begint met RE- in plaats van RA-.
* Mail aan Re-Art: "Nieuwe aanvraag". Klantmail: "Bedankt voor uw aanvraag".

= 1.0.0 =
* Eerste versie.
