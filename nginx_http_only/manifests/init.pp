class nginx_http_only {

  # Asegurar que el directorio exista con permisos correctos
  file { '/var/www/html':
    ensure => directory,
    owner  => 'nginx',
    group  => 'nginx',
    mode   => '0755',
  }

  # Limpiar archivos en /var/www/html solo si hay archivos
  exec { 'clean_html_directory':
    command     => '/bin/rm -f /var/www/html/*',
    onlyif      => '/usr/bin/test "$(ls -A /var/www/html)"',
    path        => ['/bin', '/usr/bin'],
    refreshonly => false,
    require     => File['/var/www/html'],
  }

  # Instalar nginx, php-fpm y php-cli
  package { ['nginx', 'php-fpm', 'php-cli']:
    ensure => installed,
  }

  # Copiar archivos PHP, con dependencia en la limpieza
  file { '/var/www/html/login.php':
    ensure  => file,
    source  => 'puppet:///modules/nginx_http_only/login.php',
    owner   => 'nginx',
    group   => 'nginx',
    mode    => '0644',
    require => Exec['clean_html_directory'],
  }

  file { '/var/www/html/registro.php':
    ensure  => file,
    source  => 'puppet:///modules/nginx_http_only/registro.php',
    owner   => 'nginx',
    group   => 'nginx',
    mode    => '0644',
    require => Exec['clean_html_directory'],
  }

  # Copiar nginx.conf y notificar para recargar nginx
  file { '/etc/nginx/nginx.conf':
    ensure  => file,
    source  => 'puppet:///modules/nginx_http_only/nginx.conf',
    owner   => 'root',
    group   => 'root',
    mode    => '0644',
    require => Package['nginx'],
    notify  => Service['nginx'],
  }

  # Asegurar php-fpm activo y habilitado
  service { 'php-fpm':
    ensure    => running,
    enable    => true,
    require   => Package['php-fpm'],
  }

  # Asegurar nginx activo y habilitado, depende de php-fpm y configuración
  service { 'nginx':
    ensure    => running,
    enable    => true,
    require   => [File['/etc/nginx/nginx.conf'], Service['php-fpm']],
  }
}
