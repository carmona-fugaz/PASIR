class postgre {

  # 1. Elimina todos los contenedores Docker existentes
  exec { 'remove_all_containers':
    command => '/usr/bin/docker rm -f $(/usr/bin/docker ps -aq) || true',
    path    => ['/usr/bin', '/bin'],
  }

  # 2. Crea red de Docker "postgre_net" si no existe
  exec { 'create_postgre_network':
    command => '/usr/bin/docker network create --driver bridge postgre_net',
    path    => ['/usr/bin', '/bin'],
    unless  => '/usr/bin/docker network ls --format "{{.Name}}" | grep -q "^postgre_net$"',
    require => Exec['remove_all_containers'],
  }

  # 3. Crea volumen persistente para datos de PostgreSQL
  exec { 'create_pg_master_volume':
    command => '/usr/bin/docker volume create pg_master_data',
    path    => ['/usr/bin', '/bin'],
    unless  => '/usr/bin/docker volume ls -q | grep -q "^pg_master_data$"',
    require => Exec['create_postgre_network'],
  }

  # 4. Abre puerto 5432 con iptables si no está abierto
  exec { 'open_port_5432_iptables':
    command => '/usr/sbin/iptables -I INPUT -p tcp --dport 5432 -j ACCEPT',
    unless  => '/usr/sbin/iptables -C INPUT -p tcp --dport 5432 -j ACCEPT',
    path    => ['/usr/sbin', '/usr/bin', '/bin'],
    require => Exec['create_pg_master_volume'],
  }

  # 5. Ejecuta el contenedor PostgreSQL
  exec { 'run_pg_master':
    command => '/usr/bin/docker run -d --name pg_master --network postgre_net -e POSTGRES_PASSWORD=postgres -v pg_master_data:/var/lib/postgresql/data -p 5432:5432 postgres:15',
    path    => ['/usr/bin', '/bin'],
    unless  => '/usr/bin/docker ps -a --format "{{.Names}}" | grep -q "^pg_master$"',
    require => Exec['open_port_5432_iptables'],
  }

  # 6. Espera hasta que PostgreSQL esté listo
  exec { 'wait_for_pg_master':
    command => 'bash -c "for i in {1..30}; do /usr/bin/docker exec pg_master pg_isready -U postgres && exit 0; sleep 1; done; exit 1"',
    path    => ['/usr/bin', '/bin'],
    require => Exec['run_pg_master'],
  }

  # 7. Crea la tabla users si no existe
  exec { 'create_user_table':
    command => '/usr/bin/docker exec pg_master psql -U postgres -c "CREATE TABLE IF NOT EXISTS users (id SERIAL PRIMARY KEY, username TEXT, password TEXT);"',
    path    => ['/usr/bin', '/bin'],
    require => Exec['wait_for_pg_master'],
  }

  # 8. Inserta un usuario
  exec { 'insert_user_carmona':
    command => 'docker exec pg_master psql -U postgres -c "INSERT INTO users (username, password) SELECT \'carmona\', \'funciona\' WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = \'carmona\');"',
    path    => ['/usr/bin', '/bin'],
    require => Exec['create_user_table'],
  }


  # 9. Crea carpeta para backups
  file { '/opt/postgres_backups':
    ensure => 'directory',
    owner  => 'root',
    group  => 'root',
    mode   => '0755',
  }

  # 10. Copia el script de backup desde la carpeta "files"
  file { '/usr/local/bin/pg_backup.sh':
    ensure  => 'file',
    mode    => '0755',
    source  => 'puppet:///modules/postgre/pg_backup.sh',
    require => File['/opt/postgres_backups'],
  }

  # 11. Cron que ejecuta el backup cada 30 minutos
  cron { 'postgres_backup':
    ensure  => 'present',
    command => '/usr/local/bin/pg_backup.sh',
    user    => 'root',
    minute  => '*/30',
    require => File['/usr/local/bin/pg_backup.sh'],
  }

}