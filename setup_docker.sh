#!/bin/bash
set -e

echo "Eliminando versiones antiguas de Docker si existen..."
sudo dnf remove -y docker \
                docker-client \
                docker-client-latest \
                docker-common \
                docker-latest \
                docker-latest-logrotate \
                docker-logrotate \
                docker-engine || true

echo "Instalando dependencias necesarias..."
sudo dnf install -y yum-utils device-mapper-persistent-data lvm2

echo "Añadiendo repositorio oficial de Docker..."
sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo

echo "Instalando Docker Engine y Docker Compose plugin..."
sudo dnf install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

echo "Habilitando y arrancando el servicio Docker..."
sudo systemctl enable docker
sudo systemctl start docker

echo "Agregando usuario $USER al grupo docker para uso sin sudo..."
sudo usermod -aG docker $USER

echo "Instalación completada."
echo "Por favor, cierra la sesión y vuelve a entrar para aplicar los cambios del grupo docker."
echo "Puedes verificar la instalación con estos comandos:"
echo "  docker version"
echo "  docker compose version"