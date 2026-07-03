#!/usr/bin/env bash
#
# Scaffold a new multi-block WordPress plugin from the skill template.
#
# Usage:
#   scaffold.sh <plugin-slug> <target-parent-dir> [first-block-slug]
#
# Example:
#   scaffold.sh my-new-blocks /path/to/wp-content/plugins hero-box

set -euo pipefail

usage() {
	echo "Usage: $(basename "$0") <plugin-slug> <target-parent-dir> [first-block-slug]" >&2
	echo "Example: $(basename "$0") my-new-blocks ./plugins hero-box" >&2
	exit 1
}

[ $# -lt 2 ] && usage

PLUGIN_SLUG="$1"
TARGET_PARENT="$2"
FIRST_BLOCK="${3:-sample-box}"
PLUGIN_UNDERSCORE="${PLUGIN_SLUG//-/_}"

if [[ ! "$PLUGIN_SLUG" =~ ^[a-z][a-z0-9-]*$ ]]; then
	echo "Error: plugin-slug must be lowercase letters, numbers, and hyphens (start with a letter)." >&2
	exit 1
fi

if [[ ! "$FIRST_BLOCK" =~ ^[a-z][a-z0-9-]*$ ]]; then
	echo "Error: first-block-slug must be lowercase letters, numbers, and hyphens (start with a letter)." >&2
	exit 1
fi

SKILL_DIR="$(cd "$(dirname "$0")/.." && pwd)"
TEMPLATE_DIR="$SKILL_DIR/template"
DEST="$(cd "$TARGET_PARENT" && pwd)/$PLUGIN_SLUG"

if [ -e "$DEST" ]; then
	echo "Error: destination already exists: $DEST" >&2
	exit 1
fi

if [ ! -d "$TEMPLATE_DIR" ]; then
	echo "Error: template not found at $TEMPLATE_DIR" >&2
	exit 1
fi

cp -R "$TEMPLATE_DIR" "$DEST"
rm -f "$DEST/package-lock.json"

mv "$DEST/my-blocks.php" "$DEST/$PLUGIN_SLUG.php"
mv "$DEST/src/my-blocks" "$DEST/src/$PLUGIN_SLUG"

if [ "$FIRST_BLOCK" != "sample-box" ]; then
	mv "$DEST/src/$PLUGIN_SLUG/sample-box" "$DEST/src/$PLUGIN_SLUG/$FIRST_BLOCK"
fi

PLUGIN_TITLE="$(printf '%s' "$PLUGIN_SLUG" | tr '-' ' ' | awk '{
	for (i = 1; i <= NF; i++) {
		$i = toupper(substr($i, 1, 1)) substr($i, 2)
	}
	print
}')"

replace_in_files() {
	local search="$1"
	local replace="$2"
	find "$DEST" -type f \
		! -path '*/node_modules/*' \
		! -name 'package-lock.json' \
		-print0 | while IFS= read -r -d '' file; do
		if grep -q "$search" "$file" 2>/dev/null; then
			if sed --version >/dev/null 2>&1; then
				sed -i "s/${search}/${replace}/g" "$file"
			else
				sed -i '' "s/${search}/${replace}/g" "$file"
			fi
		fi
	done
}

# Order matters: longer / more specific tokens first.
replace_in_files 'create_block_my_blocks' "create_block_${PLUGIN_UNDERSCORE}"
replace_in_files 'my_blocks' "$PLUGIN_UNDERSCORE"
replace_in_files 'my-blocks' "$PLUGIN_SLUG"
replace_in_files 'sample-box' "$FIRST_BLOCK"
replace_in_files 'My Blocks' "$PLUGIN_TITLE"

chmod +x "$DEST/scripts/restore-unchanged-builds.sh"

echo "Scaffolded plugin at: $DEST"
echo "Next steps:"
echo "  cd $DEST"
echo "  npm install"
echo "  npm run build"
