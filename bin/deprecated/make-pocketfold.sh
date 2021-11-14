#!/usr/bin/env bash

die_with() {
  echo "ERROR: $1"
  exit 1
}

# Need to have pandoc here
pandoc -v >/dev/null || die_with "Pandoc not installed"

[[ -f "$1" ]] || die_with "First argument must be a markdown file"

TMPDIR_PAGES="$TMPDIR/md-booklet"
[[ -d "$TMPDIR_PAGES" ]] || mkdir "$TMPDIR_PAGES" || die_with "Could not create $TMPDIR_PAGES"
HR_REGEX="/^---$/"

csplit -s -f "$TMPDIR_PAGES/page" "$1" "$HR_REGEX" "{6}" || die_with "Splitting files failed - are there enough pages?"

cat "html/header.html"

I=1
find "$TMPDIR_PAGES" -type f | sort | while read -r PAGEFILE; do
  echo "<div class=\"page p$I\">"
  I=$((I + 1))
  sed "s$HR_REGEX/" "$PAGEFILE" | pandoc -f markdown -t html5
  echo "</div>"
done

cat "html/footer.html"

rm -fr "$TMPDIR_PAGES"
