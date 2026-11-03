# Re-Art Orderformulier

Interactief orderformulier van Re-Art – Special Art productions.

## Bestanden

- `index.html` – het complete formulier in één bestand (opmaak, logo en werking zitten erin). Dit is de bron.
- `logo.png` – het Re-Art logo met transparante achtergrond, los voor hergebruik.
- `re-art-orderformulier.zip` – de WordPress-plugin, klaar om te uploaden.
- `wordpress-plugin/re-art-orderformulier/` – de broncode van die plugin.
- `tools/build-plugin.sh` – bouwt de plugin opnieuw op uit `index.html` na een wijziging aan het formulier.

## Gebruik

Open `index.html` in een browser (voeg `#en` toe aan de link voor de Engelse versie). De klant vult in:

1. **Gegevens** – naam/bedrijfsnaam, datum, e-mailadres (verplicht), telefoonnummer en adres: straat en huisnummer, postcode, plaats, land (optioneel).
2. **Bestelling** – per werk het aantal, de afwerking of dienst en de bijbehorende opties, plus titel en formaat van het kunstwerk. Er kunnen meerdere werken worden toegevoegd.
3. **Opmerkingen** – aanvullende wensen.

Afwerkingen en opties:

| Afwerking | Opties |
|---|---|
| Certified Canvas / Basic Canvas | UV-topcoating (Matt / Satin / Gloss), opspannen, frame aluminium (25/65, 45/65) of hout (20/65, 45/65) |
| Certified Paper | Matte, Photo of Metallic paper (Hahnemühle) |
| Basic Paper | Basic Litho White Matt / Basic Litho Archive Matt |
| HD Finish / HD Decor | White surface (Gloss / Matt) of Metal surface, ophangprofiel geen / 15 mm / 25 mm |
| Cruse Fine Art Scan | LR / Texture / Superscan / 3D, kleurproofing ja / nee |

Bij alle afwerkingen behalve de scan volgt de vraag **Certificaat: ja / nee**. Bij ja vult de klant de naam van de kunstenaar, de titel van het werk en de editie in (verplicht), en eventueel de website en de EA. Daaronder staat een live voorbeeld van het Re-Art certificaat (Certificate of Authenticity) waarin deze gegevens, het formaat, het medium en de totale editie (editie + EA) direct worden ingevuld.

Na **Order controleren & afronden** ziet de klant een bedankscherm met het orderoverzicht.

## WordPress-plugin

De plugin zet het formulier op de website en zorgt dat orders echt binnenkomen:

- Het formulier is tweetalig. Op de Nederlandse pagina is het Nederlands, op de Engelse pagina (Polylang) Engels. Forceren kan met `[reart_orderformulier lang="en"]`.
- Elke aanvraag wordt gemaild naar **info@re-art.com** met als onderwerp *Nieuwe aanvraag* (aan te passen via *Instellingen → Re-Art Orderformulier*). Beantwoorden gaat direct naar de klant.
- De klant krijgt een bevestigingsmail (*Bedankt voor uw aanvraag* / *Thank you for your request*) in de taal van het formulier, met het orderoverzicht.
- Alle orders staan in het menu **Orders** in het WordPress-beheer, met ordernummer (RE-jjmmdd-nr).
- De server controleert alle keuzes en weert spam (verborgen veld, minimale invultijd, max. 5 orders per 10 minuten per bezoeker).
- De opmaak is afgeschermd van het thema van de website.

### Installeren

1. Download `re-art-orderformulier.zip` uit deze repository.
2. WordPress-beheer → *Plugins → Nieuwe plugin → Plugin uploaden* → kies de zip → *Nu installeren* → *Activeren*.
3. Maak een pagina (bijv. "Orderformulier") en voeg het blok *Shortcode* toe met `[reart_orderformulier]`.
4. Installeer **WP Mail SMTP** en koppel die aan de Re-Art mailbox, zodat de mails betrouwbaar aankomen.
5. Stel bij *Instellingen → Privacy* de privacyverklaring in; het formulier linkt er dan automatisch naar.
6. Plaats een paar testorders en controleer de mails en het menu *Orders*.

### Formulier aanpassen

Pas `index.html` aan en draai daarna `bash tools/build-plugin.sh`. Nieuwe keuzes moeten ook worden toegevoegd in `wordpress-plugin/re-art-orderformulier/includes/options.php` en `includes/order.php`, omdat de server alleen bekende keuzes accepteert.
