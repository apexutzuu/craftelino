#!/usr/bin/env bash
# Rulează pe hosting după git pull:
#   cd /home/sro/craftelino-git && git pull && bash opencart_theme_craftelino/deploy-on-server.sh
set -euo pipefail

REPO_DIR="$(cd "$(dirname "$0")/.." && pwd)"
SITE_DIR="${SITE_DIR:-/home/sro/public_html/craftelino.ro}"
SRC="$REPO_DIR/opencart_theme_craftelino/upload"

if [[ ! -d "$SRC/catalog" ]]; then
  echo "Nu găsesc $SRC/catalog"
  exit 1
fi

if [[ ! -d "$SITE_DIR/catalog" ]]; then
  echo "Nu găsesc site-ul OpenCart la $SITE_DIR"
  exit 1
fi

echo "Deploy din: $SRC"
echo "Către:      $SITE_DIR"
cp -a "$SRC/catalog/." "$SITE_DIR/catalog/"
if [[ -d "$SRC/image" ]]; then
  mkdir -p "$SITE_DIR/image"
  cp -a "$SRC/image/." "$SITE_DIR/image/"
fi
if [[ -d "$SRC/system/library/craft" ]]; then
  mkdir -p "$SITE_DIR/system/library/craft"
  cp -a "$SRC/system/library/craft/." "$SITE_DIR/system/library/craft/"
fi
if [[ -d "$SRC/extension/craftelino" ]]; then
  mkdir -p "$SITE_DIR/extension/craftelino"
  cp -a "$SRC/extension/craftelino/." "$SITE_DIR/extension/craftelino/"
fi

if [[ -d "$SITE_DIR/system/storage/cache" ]]; then
  rm -rf "$SITE_DIR/system/storage/cache/"*
  echo "Cache golit."
fi

echo "OK — verific date firmă (fără Omni Trade):"
grep -E "TOP UNDERGROUND|50982610|Ion Nu" "$SITE_DIR/catalog/controller/information/legal.php" \
  "$SITE_DIR/extension/craftelino/system/library/craft/legal_content.php" 2>/dev/null || true
if grep -RInE "OMNITRADE|52691812|Verzisori" "$SITE_DIR/catalog/controller/information" "$SITE_DIR/extension/craftelino" "$SITE_DIR/system/library/craft" 2>/dev/null; then
  echo "ATENȚIE: mai există urme Omni Trade!"
  exit 1
fi
echo "Fără urme Omni Trade."
