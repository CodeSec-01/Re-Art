import re, sys, os
src, outdir = sys.argv[1], sys.argv[2]
s = open(src, encoding='utf-8').read()
os.makedirs(f'{outdir}/assets', exist_ok=True); os.makedirs(f'{outdir}/templates', exist_ok=True)

css = s[s.index('<style>')+7:s.index('</style>')]
body = s[s.index('<body>')+6:s.index('<script>')]
js = s[s.index('<script>')+8:s.index('</script>')]

# ---------- CSS ----------
def blocks(text):
    """split top-level css into (prelude, body) blocks"""
    out=[]; i=0; n=len(text)
    while i<n:
        j=text.find('{',i)
        if j<0: break
        pre=text[i:j]; depth=1; k=j+1
        while depth:
            if text[k]=='{': depth+=1
            elif text[k]=='}': depth-=1
            k+=1
        out.append((pre.strip(), text[j+1:k-1])); i=k
    return out
def strip_comments(t): return re.sub(r'/\*.*?\*/','',t,flags=re.S)
def prefix_sel(sel):
    parts=[]
    for p in sel.split(','):
        p=p.strip()
        if p in (':root','body'): parts.append('.reart-of')
        elif p=='*': parts.append('.reart-of, .reart-of *')
        elif p.startswith('.reart-of'): parts.append(p)
        else: parts.append('.reart-of '+p)
    return ', '.join(parts)
css=strip_comments(css)
logo = re.search(r'url\("data:image/png;base64,[^"]+"\)', css).group(0)
out=[]
for pre, bod in blocks(css):
    if pre.startswith('@media (prefers-color-scheme') or pre.startswith(':root[data-theme'): continue
    if pre=='html, body': continue
    if pre in ('.logo, .cert-logo', '.logo, .cert-logo, .info-logo'): continue
    bod=bod.replace('var(--logo-img)','url("logo.png")')
    bod=re.sub(r'url\("data:image/png;base64,[^"]+"\)', 'url("seal.png")', bod)
    if pre.startswith('@media'):
        inner=' '.join(f'{prefix_sel(p)} {{{b}}}' for p,b in blocks(bod))
        out.append(f'{pre} {{ {inner} }}')
    else:
        out.append(f'{prefix_sel(pre)} {{{bod}}}')
css_out = '''/* Re-Art Orderformulier – alle regels zijn gescoped onder .reart-of zodat het thema niet botst. */
.reart-of h1, .reart-of h2, .reart-of h3 { letter-spacing: normal; text-transform: none; color: inherit; padding: 0; line-height: 1.2; }
.reart-of input, .reart-of textarea { height: auto; min-height: 0; margin: 0; box-shadow: none; max-width: 100%; }
.reart-of button { text-transform: none; letter-spacing: normal; line-height: 1.3; box-shadow: none; min-height: 0; text-shadow: none; }
.reart-of a { color: var(--accent); }
.reart-of .hp { position: absolute !important; left: -9999px !important; width: 1px; height: 1px; overflow: hidden; }
.reart-of .btn[disabled] { opacity: .6; cursor: progress; }
''' + '\n'.join(out) + '\n'
css_out = css_out.replace('z-index: 10;','z-index: 99999;').replace('position: sticky; top: 20px;','position: sticky; top: var(--reart-sticky-top, 24px);')
css_out = css_out.replace('.reart-of {\n  /*','.reart-of {\n  /*')
open(f'{outdir}/assets/orderformulier.css','w',encoding='utf-8').write(css_out)

# ---------- markup ----------
IDS=['review','reviewTitle','reviewBody','reviewSend','reviewEdit','sendError','thanksBody','sentTo','closeBtn','orderForm','f-naam','naam','f-datum','datum','f-email','email','tel','straat','postcode','plaats','land','lines','addLine','opmerkingen',
     'sumWho','sumLines','sumCount','sumQty','formError','overlay','dlgTitle','orderText','copyBtn','editBtn','newBtn','copied']
m=body
# taalkeuze-knoppen alleen in de losse versie; op WordPress bepaalt de site de taal
m=re.sub(r'\s*<nav class="lang-switch".*?</nav>','',m,flags=re.S)
for i in IDS:
    m=m.replace(f'id="{i}"',f'id="ro-{i}"').replace(f'for="{i}"',f'for="ro-{i}"').replace(f'aria-labelledby="{i}"',f'aria-labelledby="ro-{i}"')
# honeypot
m=m.replace('  </form>','''    <div class="hp" aria-hidden="true">
      <label for="ro-website">Laat dit veld leeg</label>
      <input type="text" id="ro-website" name="website" tabindex="-1" autocomplete="off">
    </div>
  </form>''')
# footer privacy
m=m.replace('''  <footer>
    <span>Re-Art · Almere</span>''','''  <footer>
    <span>Re-Art · Almere<?php if ( $reart_of_privacy ) : ?> · <?php echo 'en' === $reart_of_lang ? 'We only use your details to process your request.' : 'We gebruiken je gegevens alleen om je aanvraag te verwerken.'; ?> <a href="<?php echo esc_url( $reart_of_privacy ); ?>"><?php echo 'en' === $reart_of_lang ? 'Privacy policy' : 'Privacyverklaring'; ?></a><?php endif; ?></span>''')
tpl = '''<?php
/**
 * Template van het Re-Art orderformulier. Wordt geladen door de shortcode [reart_orderformulier].
 *
 * @var string $reart_of_privacy URL van de privacyverklaring (kan leeg zijn).
 * @var string $reart_of_lang    Taal van het formulier: 'nl' of 'en'.
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="reart-of" id="reart-of">
''' + m.strip('\n') + '\n</div>\n'
open(f'{outdir}/templates/form.php','w',encoding='utf-8').write(tpl)

# ---------- JS ----------
j='\n'.join(x for x in js.split('\n') if '// @standalone' not in x)
for i in IDS+['sumWho']:
    j=j.replace(f'document.getElementById("{i}")',f'$("{i}")')
j=j.replace('const v = id => document.getElementById(id).value.trim();','const v = id => $(id).value.trim();')
j=j.replace('document.getElementById(id)','$(id)')   # alle statische veld-id's
j=j.replace('const first = document.querySelector(".invalid");','const first = root.querySelector(".invalid");')
j=j.replace('window.scrollTo({ top: 0 });','root.scrollIntoView({ behavior: "smooth", block: "start" });')
# verzenden: de functie tussen // @send-start en // @send-end wordt vervangen door de echte versie
a=j.index('// @send-start'); b=j.index('// @send-end')+len('// @send-end')
j=j[:a]+"""async function sendRequest() {
  const payload = {
    naam: $("naam").value, email: $("email").value, tel: $("tel").value,
    straat: $("straat").value, postcode: $("postcode").value, plaats: $("plaats").value, land: $("land").value, lang: LANG,
    opmerkingen: $("opmerkingen").value, website: $("website").value, elapsed: Date.now() - startedAt,
    lines: lines.map(l => { const o = Object.assign({}, l); delete o.id; return o; })
  };
  const res = await fetch(CFG.restUrl, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(payload) });
  const data = await res.json().catch(() => ({}));
  if (!res.ok || !data.ok) throw new Error(data.message || t("send_fail"));
  orderNo = data.orderNo || "";
}"""+j[b:]
js_out='''/* Re-Art Orderformulier */
(function () {
"use strict";
const root = document.getElementById("reart-of");
if (!root) return;
const CFG = window.REARTOF || { restUrl: "/wp-json/reart/v1/order", contact: "info@re-art.com", lang: "nl" };
const $ = id => document.getElementById("ro-" + id);
'''+j.strip('\n')+'\n})();\n'
open(f'{outdir}/assets/orderformulier.js','w',encoding='utf-8').write(js_out)
print('ok')
