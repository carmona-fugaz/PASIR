#!/bin/bash

# CONFIGURACIÓN - MODIFICA ESTOS VALORES
DOMAIN="dominio"
EMAIL="correo"
REMOTE_SERVERS=("main-1" "main-2" "secondary-1")  # Reemplaza con los otros servidores
CERT_DIR="/etc/letsencrypt/live/$DOMAIN"
REMOTE_CERT_DIR="/etc/ssl/certs"
REMOTE_KEY_DIR="/etc/ssl/private"

# Paso 1: Instalar Certbot si no está
echo "[+] Instalando Certbot..."
sudo dnf install -y epel-release
sudo dnf install -y certbot

# Paso 2: Obtener certificado SSL
echo "[+] Obteniendo certificado SSL para $DOMAIN..."
sudo certbot certonly --standalone --agree-tos --non-interactive -m "$EMAIL" -d "$DOMAIN"
if [ $? -ne 0 ]; then
  echo "[-] Error obteniendo certificado. Verifica dominio/IP y firewall."
  exit 1
fi

# Paso 3: Copiar certificados a servidores remotos
for server in "${REMOTE_SERVERS[@]}"; do
  echo "[+] Copiando certificados a $server..."
  scp "$CERT_DIR/fullchain.pem" "$server:$REMOTE_CERT_DIR/"
  scp "$CERT_DIR/privkey.pem" "$server:$REMOTE_KEY_DIR/"

  # Paso 4: Ajustar permisos en servidor remoto
  ssh "$server" "sudo chmod 600 $REMOTE_KEY_DIR/privkey.pem && sudo chown root:root $REMOTE_KEY_DIR/privkey.pem"
done

# Paso 6: Recordatorio para renovar y redistribuir
echo "[*] NOTA: Los certificados expiran cada 90 días. Usa este comando para renovar:"
echo "    sudo certbot renew --quiet && volver a copiar los archivos a los otros servidores."

echo "[✔] Todo listo."