#!/bin/bash

# Salir si ocurre un error
set -e

# Colores para salida
GREEN="\033[0;32m"
NC="\033[0m" # sin color

echo -e "${GREEN}Actualizando sistema...${NC}"
dnf -y update
dnf -y upgrade

echo -e "${GREEN}Instalando dependencias...${NC}"
dnf -y install epel-release
dnf -y install google-authenticator git openvpn keepalived iptables-services easy-rsa

# Activar e iniciar servicios necesarios
systemctl enable --now iptables
systemctl enable --now keepalived

# Configurar IPtables para permitir solo conexiones SSH desde ciertas IPs
echo -e "${GREEN}Configurando iptables para restringir acceso SSH...${NC}"

# Limpiar reglas actuales
iptables -F
iptables -X

# Política predeterminada: permitir
iptables -P INPUT ACCEPT
iptables -P FORWARD ACCEPT
iptables -P OUTPUT ACCEPT

# Permitir conexiones locales y establecidas
iptables -A INPUT -i lo -j ACCEPT
iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT

# Permitir SSH solo desde IPs autorizadas
iptables -A INPUT -p tcp --dport 22 -s "ip1" -j ACCEPT
iptables -A INPUT -p tcp --dport 22 -s "ip2" -j ACCEPT
iptables -A INPUT -p tcp --dport 22 -s "ip3" -j ACCEPT
iptables -A INPUT -p tcp --dport 22 -s "ip4" -j ACCEPT
iptables -A INPUT -p tcp --dport 22 -s "ip5" -j ACCEPT
# Bloquear todo el resto para SSH
iptables -A INPUT -p tcp --dport 22 -j DROP

# Guardar reglas
echo -e "${GREEN}Guardando reglas de iptables...${NC}"
iptables-save > /etc/sysconfig/iptables

# Reiniciar iptables para aplicar reglas
systemctl restart iptables

echo -e "${GREEN}Configuración completada exitosamente.${NC}"
