#!/data/data/com.termux/files/usr/bin/bash
S="$HOME/snap-data/creds.jsonl"
[ -f "$S" ] || { echo "no hits yet"; exit; }
awk -F'\t' '{printf "%-9s %-15s %-5s %s\n", $1, $2, $3, $4}' "$S"
