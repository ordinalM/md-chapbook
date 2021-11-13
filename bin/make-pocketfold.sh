#!/usr/bin/env bash

die_with() {
  echo "$1"
  exit 1
}

# Need to have pandoc here
pandoc -v >/dev/null || die_with "Pandoc not installed"

[[ -f "$1" ]] || die_with "First argument must be a markdown file"

TMPDIR=".tmp"
[[ -d "$TMPDIR" ]] || mkdir "$TMPDIR" || die_with "Could not create $TMPDIR"
HR_REGEX="/^---$/"

csplit -s -f "$TMPDIR/page" "$1" "$HR_REGEX" "{6}" || die_with "Splitting files failed - are there enough pages?"

OUTFILE="output/$(basename "$1" | sed -E "s/\.(txt|md|markdown)$//").html"
cp "html/header.html" "$OUTFILE"

I=1
find "$TMPDIR" -type f | sort | while read -r PAGEFILE; do
  {
    echo "<div class=\"page p$I\">"
    I=$((I+1))
    sed "s$HR_REGEX/" "$PAGEFILE" | pandoc -f markdown -t html5
    echo '</div>'
  } >>"$OUTFILE"
done

cat "html/footer.html" >> "$OUTFILE"

rm -fr "$TMPDIR"

echo "Created $OUTFILE"