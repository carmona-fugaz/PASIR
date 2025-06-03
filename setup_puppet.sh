#!/bin/bash

# Comprobación de permisos
if [[ $EUID -ne 0 ]]; then
    echo "Este script debe ejecutarse como root."
    exit 1
fi

# IPs NTP públicas personalizadas para el servicio chrony
NTP_IP1=""
NTP_IP2=""
NTP_IP3=""

# Añadir repositorio de Puppet
echo "Configurando repositorio Puppet..."
dnf install -y https://yum.puppet.com/puppet-release-el-9.noarch.rpm
dnf update -y

echo "¿Qué deseas instalar?"
echo "1) Puppet Server"
echo "2) Puppet Agent"
read -rp "Selecciona una opción [1-2]: " opcion

abrir_puerto_8140_iptables() {
    echo "Configurando iptables para permitir el puerto 8140..."
    iptables -C INPUT -p tcp --dport 8140 -j ACCEPT 2>/dev/null || iptables -I INPUT -p tcp --dport 8140 -j ACCEPT
    service iptables save 2>/dev/null || iptables-save > /etc/sysconfig/iptables
}

configurar_chrony() {
    echo "Instalando y configurando chrony..."
    dnf install -y chrony

    echo "Configurando NTP con servidores personalizados..."
    cp /etc/chrony.conf /etc/chrony.conf.bak

    # Limpia servidores previos y configura los nuevos
    sed -i '/^server /d' /etc/chrony.conf
    sed -i '/^pool /d' /etc/chrony.conf

    echo "server $NTP_IP1 iburst" >> /etc/chrony.conf
    echo "server $NTP_IP2 iburst" >> /etc/chrony.conf
    echo "server $NTP_IP3 iburst" >> /etc/chrony.conf

    systemctl enable --now chronyd
    echo "Verificando estado de sincronización:"
    chronyc sources
}

if [[ "$opcion" == "1" ]]; then
    echo "Instalando Puppet Server..."
    dnf install -y puppetserver

    echo "Configurando Puppet Server..."
    sed -i 's/^JAVA_ARGS=.*$/JAVA_ARGS="-Xms512m -Xmx512m"/' /etc/sysconfig/puppetserver

    read -rp "¿Deseas habilitar autosign para cualquier agente? (s/n): " autosign
    if [[ "$autosign" == "s" || "$autosign" == "S" ]]; then
        echo "*" > /etc/puppetlabs/puppet/autosign.conf
    else
        echo "Introduce los FQDN permitidos para los agentes (uno por línea, finaliza con línea vacía):"
        > /etc/puppetlabs/puppet/autosign.conf
        while true; do
            read -rp "FQDN: " fqdn
            [[ -z "$fqdn" ]] && break
            echo "$fqdn" >> /etc/puppetlabs/puppet/autosign.conf
        done
    fi

    systemctl enable --now puppetserver
    abrir_puerto_8140_iptables
    configurar_chrony

    echo "✅ Puppet Server instalado con NTP sincronizado."

elif [[ "$opcion" == "2" ]]; then
    echo "Instalando Puppet Agent..."
    dnf install -y puppet-agent

    read -rp "Introduce el FQDN del servidor Puppet: " puppet_server
    /opt/puppetlabs/bin/puppet config set server "$puppet_server" --section main
    /opt/puppetlabs/bin/puppet config set environment production --section main

    hostname_fqdn=$(hostname -f)
    echo "FQDN detectado: $hostname_fqdn"
    read -rp "¿Usar este FQDN como nombre del agente? (s/n): " usar_fqdn
    if [[ "$usar_fqdn" != "s" && "$usar_fqdn" != "S" ]]; then
        read -rp "Introduce el nuevo FQDN para este agente: " nuevo_fqdn
        hostnamectl set-hostname "$nuevo_fqdn"
    fi

    systemctl enable --now puppet
    configurar_chrony

    echo "Forzando primer contacto con el servidor Puppet..."
    /opt/puppetlabs/bin/puppet agent --test

    echo "✅ Puppet Agent instalado, sincronizado con NTP y configurado."

else
    echo "❌ Opción no válida."
    exit 1
fi