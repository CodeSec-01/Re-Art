# Re-Art Aanvraagformulier – instructies voor Claude

## Over het bedrijf
Re-Art – Special Art productions, Almere. Fine art printing, Cruse-scans, opplakken (aluminium/Dibond), inlijsten en coatings. Website: www.re-art.com (WordPress, tweetalig via Polylang).

## Werkafspraken
- Communiceer in het **Nederlands**. Sebastian is geen developer: leg kort en zonder jargon uit wat je doet en waarom.
- Werk in stappen: eerst een plan voorleggen, dan pas bouwen. Laat de wijzigingen zien voor je commit.
- Nooit force-pushen. **GitHub Pages niet aanzetten.**
- Geen klantgegevens, wachtwoorden of SMTP-gegevens in de repo.
- Houd het simpel: geen frameworks of build-tools toevoegen zonder overleg.

## Structuur (zie ook README.md)
- `index.html` – **de bron**: het complete formulier in één bestand (NL + EN, `#en` voor Engels).
- `wordpress-plugin/re-art-orderformulier/` – plugin-broncode (shortcode `[reart_orderformulier]`).
- `re-art-orderformulier.zip` – gebouwde plugin om in WordPress te uploaden.
- `pdf/Re-Art_Aanvraagformulier.pdf` – invulbare PDF om per e-mail te sturen.
- `tools/` – `build-plugin.sh`, `split.py`, `build_pdf.py`.

## Na een wijziging aan het formulier
1. Pas `index.html` aan.
2. Nieuwe keuzes/opties óók toevoegen in `includes/options.php` en `includes/order.php` (de server accepteert alleen bekende keuzes).
3. `bash tools/build-plugin.sh` → bouwt de plugin en de zip opnieuw.
4. Indien relevant: `python3 tools/build_pdf.py` → nieuwe PDF.
5. Versienummer ophogen in `re-art-orderformulier.php` (en `readme.txt`), README bijwerken.
6. Beide talen (NL en EN) controleren.

## Huisstijl
- Hoofdkleur (donkerblauw): `--accent: #12285a`; zacht accent `#e8ecf4`.
- Tekst `#15171c`, achtergrond `#f9f9f9` (huisstijl Re-Art), randen `#d5d9df`. Er is ook een donkere modus.
- Lettertype huisstijl: Aptos. Logo: "RE-ART – Special Art productions" (`logo.png`).
- Stijl van de pop-ups en het certificaat volgt de Re-Art brochure.
