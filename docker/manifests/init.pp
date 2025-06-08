class docker {

  # Incluye el repositorio oficial de Docker si aún no está presente
  exec { 'add_docker_repo':
    command => '/bin/dnf config-manager --add-repo=https://download.docker.com/linux/centos/docker-ce.repo',
    path    => ['/bin', '/usr/bin'],
    unless  => '/bin/dnf repolist | grep -q docker-ce',
  }

  # Asegura que los paquetes requeridos estén instalados
  package { ['yum-utils', 'device-mapper-persistent-data', 'lvm2']:
    ensure => installed,
  }

  # Instala Docker si no está presente
  package { 'docker-ce':
    ensure => installed,
    require => Exec['add_docker_repo'],
  }

  # Habilita y arranca el servicio de Docker
  service { 'docker':
    ensure => running,
    enable => true,
    require => Package['docker-ce'],
  }

}
