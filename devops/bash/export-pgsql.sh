#!/bin/bash
# Dump postgres database
#
# Example:
#   export-pgsql.sh > /tmp/crm-pgsql.sql
#

dotenv="/var/www/crm/.env"
if [ ! -e "$dotenv" ]; then
    echo "Error: cant' locate $dotenv"
    exit 1
fi

hostname=$(grep _DBPOSTGRES_DSN_HOST $dotenv |cut -f 2 -d =)
database=$(grep _DBPOSTGRES_DSN_DBNAME $dotenv |cut -f 2 -d =)
username=$(grep _DBPOSTGRES_USERNAME $dotenv |cut -f 2 -d =)
password=$(grep _DBPOSTGRES_PASSWORD $dotenv |cut -f 2 -d =)

export PGPASSWORD="$password"
pg_dump -h $hostname -U $username -d $database --if-exists --clean

