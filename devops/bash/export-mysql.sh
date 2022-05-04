#!/bin/bash
# Dump mysql database
#
# Example:
#   export-mysql.sh > /tmp/mysql.sql
#

dotenv="/var/www/app/.env"
if [ ! -e "$dotenv" ]; then
    echo "Error: cant' locate $dotenv"
    exit 1
fi

hostname=$(grep _DB_DSN_HOST $dotenv |cut -f 2 -d =)
database=$(grep _DB_DSN_DBNAME $dotenv |cut -f 2 -d =)
username=$(grep _DB_USERNAME $dotenv |cut -f 2 -d =)
password=$(grep _DB_PASSWORD $dotenv |cut -f 2 -d =)

# Disable GTID_PURGED
mysqldump -h $hostname -u $username -p$password $database --set-gtid-purged=OFF

