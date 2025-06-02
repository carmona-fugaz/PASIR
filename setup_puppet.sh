#!/bin/bash
set -e

echo "🚀 Instalando Puppet 7 (agente y servidor) en Rocky Linux 9..."

# 1. Eliminar cualquier release anterior
dnf remove -y puppet-release || true

# 2. Instalar el repositorio oficial de Puppet 7
dnf install -y https://yum.puppet.com/puppet7-release-el-9.noarch.rpm

# 3. Limpiar y actualizar la caché
dnf clean all
dnf makecache

# 4. Instalar el agente y el servidor
dnf install -y puppet-agent puppetserver --allowerasing

# 5. Configurar memoria del servidor (ajusta según tu RAM)
echo "🛠️ Configurando memoria del Puppet Server..."
sed -i 's/^JAVA_ARGS=.*/JAVA_ARGS="-Xms512m -Xmx512m"/' /etc/sysconfig/puppetserver

# 6. Configurar puppet.conf para usar el propio servidor
echo "📄 Escribiendo configuración básica en /etc/puppetlabs/puppet/puppet.conf..."
cat << EOF > /etc/puppetlabs/puppet/puppet.conf
[main]
certname = $(hostname -f)
server = $(hostname -f)
environment = production
runinterval = 1h
EOF

# 7. Habilitar y arrancar servicios
echo "🚦 Habilitando y arrancando servicios..."
systemctl enable --now puppetserver
systemctl enable --now puppet

# 8. (Opcional) Abrir puerto 8140 en el firewall
if command -v firewall-cmd &> /dev/null; then
  echo "🌐 Configurando firewall para permitir conexiones en el puerto 8140..."
  firewall-cmd --add-port=8140/tcp --permanent
  firewall-cmd --reload
fi

# 9. Mostrar estado
echo ""
echo "✅ Instalación completa de Puppet 7."
echo "📡 Puppet Server corriendo en: https://$(hostname -f):8140"
echo ""
echo "🔐 Para firmar certificados de agentes usa:"
echo "   /opt/puppetlabs/bin/puppetserver ca list"
echo "   /opt/puppetlabs/bin/puppetserver ca sign --certname NOMBRE_DEL_AGENTE"
