
#!/usr/bin/env python3

import json
import subprocess
import requests
import paho.mqtt.client as mqtt
from typing import Dict, Any

# =========================================================
# MQTT CONFIGURATION
# =========================================================
MQTT_BROKER = "84.46.248.97"
MQTT_PORT = 1883
MQTT_TOPIC_CMD = "obzora/cmd"
MQTT_TOPIC_RESP = "obzora/resp"
MQTT_USER = "mqttobzora"
MQTT_PASS = "meemtel@123"

# =========================================================
# OBZORA / LIBRENMS CONFIGURATION
# =========================================================
LNMS_PATH = "/opt/obzora/lnms"

API_BASE_URL = "http://127.0.0.1/api/v0"
API_TOKEN = "30a73479020952c489e7f270279585f0"
API_HEADERS = {
    "X-Auth-Token": API_TOKEN,
    "Accept": "application/json",
    "Content-Type": "application/json",
}

# =========================================================
# SECURITY WHITELISTS
# =========================================================

# Allowed lnms CLI commands
ALLOWED_LNMS_COMMANDS = {
    "about": True,
    "device:poll": True,
    "device:discover": True,
}

# Allowed API endpoints and methods (Alerts related)
ALLOWED_API_ENDPOINTS = {
    "/alerts": ["GET"],
    "/alerts/{id}": ["GET", "PUT"],

    "/alert-rules": ["GET", "POST"],
    "/alert-rules/{id}": ["GET", "PUT", "DELETE"],

    "/alert-schedules": ["GET", "POST"],
    "/alert-schedules/{id}": ["GET", "PUT", "DELETE"],
}


# =========================================================
# HELPER FUNCTIONS
# =========================================================

def is_allowed_endpoint(endpoint: str, method: str) -> bool:
    """Match endpoint against whitelist (supports {id})."""
    for pattern, methods in ALLOWED_API_ENDPOINTS.items():
        if method not in methods:
            continue

        if "{id}" in pattern:
            base = pattern.replace("/{id}", "")
            if endpoint.startswith(base + "/"):
                return True
        elif endpoint == pattern:
            return True

    return False


def run_lnms(command: str, args: str = "") -> str:
    """Execute lnms command securely."""
    cmd = [LNMS_PATH, command]
    if args:
        cmd.append(args)

    result = subprocess.run(
        cmd,
        capture_output=True,
        text=True
    )

    return result.stdout.strip() or result.stderr.strip()


def run_api(method: str, endpoint: str, params: Dict[str, Any]) -> str:
    """Execute LibreNMS API call."""
    url = API_BASE_URL + endpoint

    response = requests.request(
        method=method,
        url=url,
        headers=API_HEADERS,
        json=params if method in ["POST", "PUT"] else None,
        params=params if method == "GET" else None,
        timeout=15
    )

    return response.text


def publish_response(client, request_id: str, status: str, output: str):
    """Send response back via MQTT."""
    payload = {
        "request_id": request_id,
        "status": status,
        "output": output
    }
    client.publish(MQTT_TOPIC_RESP, json.dumps(payload))


# =========================================================
# MQTT CALLBACK
# =========================================================

def on_message(client, userdata, msg):
    try:
        payload = json.loads(msg.payload.decode())
        request_id = payload.get("request_id", "unknown")
        msg_type = payload.get("type")

        # -------------------------------
        # LNMS COMMAND HANDLING
        # -------------------------------
        if msg_type == "command":
            command = payload.get("command")
            args = payload.get("args", "")

            if command not in ALLOWED_LNMS_COMMANDS:
                raise ValueError(f"Unauthorized lnms command: {command}")

            if args and not ALLOWED_LNMS_COMMANDS[command]:
                raise ValueError(f"Arguments not allowed for command: {command}")

            output = run_lnms(command, args)
            publish_response(client, request_id, "ok", output)

        # -------------------------------
        # API CALL HANDLING
        # -------------------------------
        elif msg_type == "api":
            method = payload.get("method", "GET").upper()
            endpoint = payload.get("endpoint")
            params = payload.get("params", {})

            if not is_allowed_endpoint(endpoint, method):
                raise ValueError(f"Unauthorized API call: {method} {endpoint}")

            output = run_api(method, endpoint, params)
            publish_response(client, request_id, "ok", output)

        else:
            raise ValueError("Invalid message type")

    except Exception as e:
        publish_response(
            client,
            payload.get("request_id", "unknown") if "payload" in locals() else "unknown",
            "error",
            str(e)
        )


# =========================================================
# MAIN
# =========================================================

def main():
    client = mqtt.Client()
    client.username_pw_set(MQTT_USER, MQTT_PASS)

    client.on_message = on_message
    client.connect(MQTT_BROKER, MQTT_PORT)

    client.subscribe(MQTT_TOPIC_CMD)
    print(f"Listening on MQTT topic: {MQTT_TOPIC_CMD}")

    client.loop_forever()


if __name__ == "__main__":
    main()

