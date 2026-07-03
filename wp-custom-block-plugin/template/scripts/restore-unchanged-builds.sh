#!/bin/bash
#
# After a full webpack build, restore build outputs for blocks whose
# source files haven't changed (compared to git HEAD).
# This prevents unrelated blocks from showing up in git diff.
# Files excluded from builds (.map, *-rtl.css) are never restored.

set -euo pipefail

PLUGIN_DIR="$(cd "$(dirname "$0")/.." && pwd)"
REPO_ROOT="$(git -C "$PLUGIN_DIR" rev-parse --show-toplevel)"
PLUGIN_REL="${PLUGIN_DIR#$REPO_ROOT/}"

SRC_DIR="$PLUGIN_REL/src"
BUILD_DIR="$PLUGIN_REL/build"

restored=0
kept=0

restore_tracked_files() {
  local git_path="$1"
  local files
  files="$(git -C "$REPO_ROOT" ls-tree -r --name-only HEAD -- "$git_path" 2>/dev/null || true)"
  [ -z "$files" ] && return 1

  local to_restore=""
  while IFS= read -r f; do
    case "$f" in *.map|*-rtl.css) continue ;; esac
    to_restore="$to_restore $f"
  done <<< "$files"

  if [ -n "$to_restore" ]; then
    git -C "$REPO_ROOT" checkout HEAD -- $to_restore 2>/dev/null
    return 0
  fi
  return 1
}

for build_block_dir in "$PLUGIN_DIR"/build/my-blocks/*/; do
  block_name="$(basename "$build_block_dir")"
  src_block_dir="$SRC_DIR/my-blocks/$block_name"

  if ! [ -d "$REPO_ROOT/$src_block_dir" ]; then
    continue
  fi

  src_changed="$(git -C "$REPO_ROOT" diff --name-only HEAD -- "$src_block_dir" 2>/dev/null || true)"

  if [ -z "$src_changed" ]; then
    restore_tracked_files "$BUILD_DIR/my-blocks/$block_name/" && {
      restored=$((restored + 1))
    } || true
  else
    kept=$((kept + 1))
  fi
done

# Restore non-block build files (format-types, etc.) if their sources are unchanged
for src_entry_dir in "$PLUGIN_DIR"/src/*/; do
  entry_name="$(basename "$src_entry_dir")"
  [ "$entry_name" = "my-blocks" ] && continue

  src_changed="$(git -C "$REPO_ROOT" diff --name-only HEAD -- "$SRC_DIR/$entry_name/" 2>/dev/null || true)"
  if [ -z "$src_changed" ]; then
    for build_file in "$PLUGIN_DIR"/build/"$entry_name".*; do
      [ -f "$build_file" ] || continue
      filename="$(basename "$build_file")"
      git -C "$REPO_ROOT" checkout HEAD -- "$BUILD_DIR/$filename" 2>/dev/null || true
    done
  fi
done

# Restore blocks-manifest.php if no block.json files changed
block_json_changed="$(git -C "$REPO_ROOT" diff --name-only HEAD -- "$SRC_DIR"/my-blocks/*/block.json 2>/dev/null || true)"
if [ -z "$block_json_changed" ]; then
  git -C "$REPO_ROOT" checkout HEAD -- "$BUILD_DIR/blocks-manifest.php" 2>/dev/null || true
fi

echo "Build cleanup: restored $restored unchanged block(s), kept $kept changed block(s)"
