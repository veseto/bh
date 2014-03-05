#!/bin/bash

date=`date -I`

mysqldump -h localhost -u root -pqwerty fefe_bethelper | gzip > /home/backups/backup-$date.sql.gz

