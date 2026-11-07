#!/data/data/com.termux/files/usr/bin/bash
for f in server tunnel; do
  [ -f "$HOME/snap-data/$f.pid" ] && kill "$(cat "$HOME/snap-data/$f.pid")" 2>/dev/null
  rm -f "$HOME/snap-data/$f.pid"
done
pkill -f 'php -S 0.0.0.0:8080' 2>/dev/null
pkill -f cloudflared 2>/dev/null
termux-wake-unlock
echo "[+] stopped"
