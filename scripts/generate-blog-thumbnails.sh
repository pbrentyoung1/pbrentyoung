#!/usr/bin/env bash
set -euo pipefail

root="$(cd "$(dirname "$0")/.." && pwd)"
source_dir="$root/assets/img/blog"
thumb_dir="$source_dir/thumbs"

mkdir -p "$thumb_dir"

for source in "$source_dir"/*.jpg; do
  [ -f "$source" ] || continue
  slug="$(basename "$source" .jpg)"
  magick "$source" \
    -resize '600x315^' \
    -gravity center \
    -extent 600x315 \
    -strip \
    -interlace Plane \
    -quality 82 \
    "$thumb_dir/$slug.jpg"
done

echo "Generated blog thumbnails in $thumb_dir"
