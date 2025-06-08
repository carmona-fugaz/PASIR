#!/bin/bash

TS="$(hostname)_$(date +%H_%d_%m)"
/usr/bin/docker exec pg_master pg_dumpall -U postgres > /opt/postgres_backups/backup_${TS}.sql
