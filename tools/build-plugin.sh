#!/usr/bin/env bash
# Bouwt de WordPress-plugin opnieuw op uit index.html en maakt re-art-orderformulier.zip.
# Gebruik: bash tools/build-plugin.sh   (vanuit de hoofdmap van de repository)
set -euo pipefail
P=wordpress-plugin/re-art-orderformulier
python3 tools/split.py index.html "$P"
cp logo.png "$P/assets/logo.png"
python3 - "$P/assets/seal.png" <<'PY'
import re, base64, sys
html = open("index.html", encoding="utf-8").read()
m = re.search(r'\.seal-img \{[^}]*?url\("data:image/png;base64,([^"]+)"\)', html, re.S)
open(sys.argv[1], "wb").write(base64.b64decode(m.group(1)))
PY
rm -f re-art-orderformulier.zip
(cd wordpress-plugin && zip -qr ../re-art-orderformulier.zip re-art-orderformulier -x '*.DS_Store')
echo "Klaar: re-art-orderformulier.zip"
