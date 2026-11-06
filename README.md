# Re-Art Aanvraagformulier

Interactief aanvraagformulier van Re-Art – Special Art productions (Nederlands en Engels).

## Bestanden

- `index.html` – het complete formulier in één bestand (opmaak, logo en werking zitten erin). Dit is de bron.
- `logo.png` – het Re-Art logo met transparante achtergrond, los voor hergebruik.
- `re-art-orderformulier.zip` – de WordPress-plugin, klaar om te uploaden.
- `wordpress-plugin/re-art-orderformulier/` – de broncode van die plugin.
- `tools/build-plugin.sh` – bouwt de plugin opnieuw op uit `index.html` na een wijziging aan het formulier.
- `pdf/Re-Art_Aanvraagformulier.pdf` – invulbare PDF-versie voor Adobe Acrobat (Reader); `tools/build_pdf.py` maakt hem opnieuw.

## Gebruik

Open `index.html` in een browser (voeg `#en` toe aan de link voor de Engelse versie). Eerst verschijnt een **startscherm** met twee keuzes:

- **Aanvraag nieuwe klant** – een kort contactformulier: gegevens en de vraag waarover de klant informatie wil. Wordt direct verstuurd (zonder nummer). Re-Art ontvangt "Nieuw contactverzoek van <naam>", de klant krijgt een bevestiging (reactie binnen 24 uur).
- **Aanvraag of bestelling bestaande klant** – het volledige aanvraagformulier hieronder.

In het aanvraagformulier vult de klant in:

1. **Gegevens** – naam/bedrijfsnaam en e-mailadres (verplicht), telefoonnummer en adres (optioneel). De datum wordt automatisch ingevuld.
2. **Bestelling** – per werk het aantal, de afwerking of dienst en de bijbehorende opties, plus titel en formaat van het kunstwerk. Er kunnen meerdere werken worden toegevoegd.
3. **Opmerkingen** – aanvullende wensen.

Afwerkingen en opties:

| Afwerking | Opties |
|---|---|
| Certified Canvas / Basic Canvas | UV-topcoating (Matt / Satin / Gloss), opspannen, frame aluminium (25/65, 45/65) of hout (20/65, 45/65) |
| Certified Paper | Matte, Photo of Metallic paper (Hahnemühle), blindpreeg, nummeren, witrand, afwerking: losse print / opgeplakt (1 mm aluminium of 3 mm Dibond, met ophanging) / ingelijst (witte of zwarte lijst met nerf, blanke lijst) |
| Basic Paper | Basic Litho White Matt / Basic Litho Archive Matt, blindpreeg, nummeren, afwerking zoals bij Certified Paper |
| HD Finish / HD Decor | Type afwerking: Wit (Gloss / Matt) of Metal (EN: White / Metal surface), ophangprofiel geen / 15 mm / 25 mm |
| Diasec | Gloss / Mat / TruLife |
| Cruse Fine Art Scan | Standaard scan / Texture / 3D, na de scan: digitaal bestand / digitaal bestand + voorbereid / kleurproofing |

Bij alle afwerkingen behalve de scan volgt de vraag **Certificaat: ja / nee**. Bij ja vult de klant de naam van de kunstenaar, de titel van het werk en de editie in (verplicht), en eventueel de website en de EA. Daaronder staat een live voorbeeld van het Re-Art certificaat zoals in de brochure, met genummerde punten (klikbaar) en de blindpreeg Certified Art Giclée. Ook een optionele atelier-naam kan worden ingevuld.

Bij elk product staat een knop **Meer info** met een pop-up in de stijl van de brochure: uitleg, prijzen, tips van het team en foto's van de afwerkingen.

Bij "02 Je aanvraag" staat een hulpvak **"Hulp nodig? Neem contact met ons op"** met telefoon en e-mail; het blijft in beeld tijdens het invullen.

Na **Aanvraag controleren & afronden** verschijnt eerst het scherm **Controleer je aanvraag** met het volledige overzicht. Pas na **Aanvraag versturen** wordt de aanvraag verstuurd en volgt het bedankscherm met het aanvraagnummer (RE-jjmmdd-0001, per dag oplopend).

## WordPress-plugin

De plugin zet het formulier op de website en zorgt dat orders echt binnenkomen:

- Het formulier is tweetalig. Op de Nederlandse pagina is het Nederlands, op de Engelse pagina (Polylang) Engels. Forceren kan met `[reart_orderformulier lang="en"]`.
- Elke aanvraag wordt gemaild naar **info@re-art.com** met als onderwerp *Nieuwe aanvraag* (aan te passen via *Instellingen → Re-Art Aanvraagformulier*); de mails worden ook vanaf dat adres verstuurd. Beantwoorden gaat direct naar de klant.
- De klant krijgt een bevestigingsmail (*Bedankt voor uw aanvraag* / *Thank you for your request*) in de taal van het formulier, met het orderoverzicht.
- Alle aanvragen staan in het menu **Aanvragen** in het WordPress-beheer, met aanvraagnummer (RE-jjmmdd-0001, per dag oplopend vanaf 0001).
- De server controleert alle keuzes en weert spam (verborgen veld, minimale invultijd, max. 5 orders per 10 minuten per bezoeker).
- De opmaak is afgeschermd van het thema van de website.

### Installeren

1. Download `re-art-orderformulier.zip` uit deze repository.
2. WordPress-beheer → *Plugins → Nieuwe plugin → Plugin uploaden* → kies de zip → *Nu installeren* → *Activeren*.
3. Maak een pagina (bijv. "Aanvraagformulier"; bij Polylang ook een Engelse pagina) en voeg het blok *Shortcode* toe met `[reart_orderformulier]`.
4. Installeer **WP Mail SMTP** en koppel die aan de Re-Art mailbox, zodat de mails betrouwbaar aankomen.
5. Stel bij *Instellingen → Privacy* de privacyverklaring in; het formulier linkt er dan automatisch naar.
6. Plaats een paar testorders en controleer de mails en het menu *Orders*.

### Formulier aanpassen

Pas `index.html` aan en draai daarna `bash tools/build-plugin.sh`. Nieuwe keuzes moeten ook worden toegevoegd in `wordpress-plugin/re-art-orderformulier/includes/options.php` en `includes/order.php`, omdat de server alleen bekende keuzes accepteert.
