#!/usr/bin/env bash

# Ekscoder VPS Monitoring Agent
# Server: {{ $server->name }}
# Interval: {{ $server->check_interval }} min(s)

API_URL="{{ $baseUrl }}/api/vps/ping"
TOKEN="{{ $server->auth_token }}"
INTERVAL="{{ $server->check_interval }}"

send_metrics() {
    # 1. CPU Usage (%)
    if command -v mpstat >/dev/null 2>&1; then
        CPU_USAGE=$(mpstat 1 1 | awk '/Average/ {print 100 - $NF}')
    else
        # Fallback using /proc/stat
        CPU_IDLE=$(top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/" | awk '{print $1}')
        if [ -n "$CPU_IDLE" ]; then
            CPU_USAGE=$(awk "BEGIN {print 100 - $CPU_IDLE}")
        else
            CPU_USAGE=0
        fi
    fi

    # 2. RAM Usage (MB)
    RAM_TOTAL=$(free -m | awk '/Mem:/ {print $2}')
    RAM_USED=$(free -m | awk '/Mem:/ {print $3}')

    # 3. Disk Usage (GB on /)
    DISK_TOTAL=$(df -BG / | awk 'NR==2 {print $2}' | tr -d 'G')
    DISK_USED=$(df -BG / | awk 'NR==2 {print $3}' | tr -d 'G')

    # 4. Load Average (1 min)
    LOAD_1M=$(awk '{print $1}' /proc/loadavg 2>/dev/null || echo 0)

    # 5. Uptime (seconds)
    UPTIME_SEC=$(awk '{print int($1)}' /proc/uptime 2>/dev/null || echo 0)

    # 6. OS Info
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        OS_INFO="$NAME $VERSION_ID"
    else
        OS_INFO=$(uname -s -r)
    fi

    # 7. CPU Cores
    CPU_CORES=$(nproc 2>/dev/null || grep -c ^processor /proc/cpuinfo 2>/dev/null || echo 1)

    # Build JSON payload
    PAYLOAD=$(cat <<EOF
{
  "cpu_usage": ${CPU_USAGE:-0},
  "ram_used_mb": ${RAM_USED:-0},
  "ram_total_mb": ${RAM_TOTAL:-1},
  "disk_used_gb": ${DISK_USED:-0},
  "disk_total_gb": ${DISK_TOTAL:-1},
  "load_avg_1m": ${LOAD_1M:-0},
  "uptime_seconds": ${UPTIME_SEC:-0},
  "os_info": "${OS_INFO}",
  "cpu_cores": ${CPU_CORES}
}
EOF
)

    # Send POST request
    curl -s -X POST "$API_URL" \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer $TOKEN" \
        -d "$PAYLOAD" > /dev/null
}

setup_cron() {
    if [ -n "$HOME" ] && [ -w "$HOME" ]; then
        AGENT_DIR="$HOME/.ekscoder"
    else
        AGENT_DIR="/tmp/.ekscoder"
    fi

    mkdir -p "$AGENT_DIR"
    AGENT_PATH="$AGENT_DIR/vps-agent-${TOKEN:0:8}.sh"
    
    echo "[Ekscoder Agent] Installing script to $AGENT_PATH..."
    
    # Save script to local file
    cat <<'AGENT_EOF' > "$AGENT_PATH"

#!/usr/bin/env bash
API_URL="{{ $baseUrl }}/api/vps/ping"
TOKEN="{{ $server->auth_token }}"

RAM_TOTAL=$(free -m | awk '/Mem:/ {print $2}')
RAM_USED=$(free -m | awk '/Mem:/ {print $3}')
DISK_TOTAL=$(df -BG / | awk 'NR==2 {print $2}' | tr -d 'G')
DISK_USED=$(df -BG / | awk 'NR==2 {print $3}' | tr -d 'G')
LOAD_1M=$(awk '{print $1}' /proc/loadavg 2>/dev/null || echo 0)
UPTIME_SEC=$(awk '{print int($1)}' /proc/uptime 2>/dev/null || echo 0)
CPU_CORES=$(nproc 2>/dev/null || echo 1)

if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS_INFO="$NAME $VERSION_ID"
else
    OS_INFO=$(uname -s -r)
fi

# Calculate CPU Usage
CPU_IDLE=$(top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/" | awk '{print $1}')
if [ -n "$CPU_IDLE" ]; then
    CPU_USAGE=$(awk "BEGIN {print 100 - $CPU_IDLE}")
else
    CPU_USAGE=0
fi

curl -s -X POST "$API_URL" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $TOKEN" \
    -d "{\"cpu_usage\": ${CPU_USAGE:-0}, \"ram_used_mb\": ${RAM_USED:-0}, \"ram_total_mb\": ${RAM_TOTAL:-1}, \"disk_used_gb\": ${DISK_USED:-0}, \"disk_total_gb\": ${DISK_TOTAL:-1}, \"load_avg_1m\": ${LOAD_1M:-0}, \"uptime_seconds\": ${UPTIME_SEC:-0}, \"os_info\": \"${OS_INFO}\", \"cpu_cores\": ${CPU_CORES}}" > /dev/null
AGENT_EOF

    chmod +x "$AGENT_PATH"

    CRON_CMD="*/${INTERVAL} * * * * $AGENT_PATH >/dev/null 2>&1"

    # Remove existing cron entry for this agent if present, then add new schedule
    (crontab -l 2>/dev/null | grep -v "$AGENT_PATH"; echo "$CRON_CMD") | crontab -
    echo "[Ekscoder Agent] Cron job updated to run every $INTERVAL minute(s)."


    # Trigger first metric ping
    echo "[Ekscoder Agent] Running initial health check..."
    bash "$AGENT_PATH"
    echo "[Ekscoder Agent] Installation complete! VPS Link Status is now active."
}

# Run execution
if [ "$1" == "--ping-only" ]; then
    send_metrics
else
    setup_cron
fi
