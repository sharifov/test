#!/bin/bash
# Dump postgres database
#
# Example:
#   import-pgsql.sh < /tmp/pgsql.sql
#

dotenv="/var/www/app/.env"
if [ ! -e "$dotenv" ]; then
    echo "Error: cant' locate $dotenv"
    exit 1
fi

hostname=$(grep _DBPOSTGRES_DSN_HOST $dotenv |cut -f 2 -d =)
database=$(grep _DBPOSTGRES_DSN_DBNAME $dotenv |cut -f 2 -d =)
username=$(grep _DBPOSTGRES_USERNAME $dotenv |cut -f 2 -d =)
password=$(grep _DBPOSTGRES_PASSWORD $dotenv |cut -f 2 -d =)

export PGPASSWORD="$password"
sed -e "s/OWNER TO .*;/OWNER TO $username;/" \
    -e "s/SCHEMA public TO .*;/SCHEMA public TO $username;/" |\
    psql -h $hostname -U $username -d $database
