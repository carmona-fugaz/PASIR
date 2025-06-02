#!/bin/bash
set -e

echo "🧹 Iniciando limpieza del sistema Rocky Linux 9..."

# 1. Detener y desinstalar Puppet (opcional)
echo "🔧 Eliminando Puppet (si está instalado)..."
systemctl stop puppet || true
systemctl stop puppetserver || true
dnf remove -y puppet-agent puppetserver puppet-release || true
rm -rf /etc/puppetlabs /opt/puppetlabs /var/log/puppetlabs

# 2. Limpiar cache de paquetes
echo "🧼 Limpiando caché de DNF..."
dnf clean all
rm -rf /var/cache/dnf

# 3. Eliminar paquetes huérfanos
echo "🗑️ Eliminando paquetes huérfanos..."
dnf autoremove -y

# 4. Limpiar archivos temporales
echo "🧊 Borrando archivos temporales..."
rm -rf /tmp/*
rm -rf /var/tmp/*

# 5. Limpiar logs
echo "🧾 Borrando logs del sistema..."
find /var/log -type f -exec truncate -s 0 {} \;

# 6. (Opcional) Limpiar historial de comandos
echo "🕳️ Limpiando historial del shell..."
unset HISTFILE
rm -f ~/.bash_history
rm -f /root/.bash_history

# 7. Reiniciar servicios afectados
echo "♻️ Reiniciando servicios básicos..."
systemctl daemon-reexec
systemctl daemon-reload

echo "✅ Sistema limpio. Puedes reiniciar si lo deseas."
