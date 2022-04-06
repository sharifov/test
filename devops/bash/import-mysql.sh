#!/bin/bash
# Import mysql database
#
# Example:
#   import-mysql.sh < /tmp/crm-mysql.sql
#

dotenv="/var/www/crm/.env"
if [ ! -e "$dotenv" ]; then
    echo "Error: cant' locate $dotenv"
    exit 1
fi

hostname=$(grep _DB_DSN_HOST $dotenv |cut -f 2 -d =)
database=$(grep _DB_DSN_DBNAME $dotenv |cut -f 2 -d =)
username=$(grep _DB_USERNAME $dotenv |cut -f 2 -d =)
password=$(grep _DB_PASSWORD $dotenv |cut -f 2 -d =)

# Use -f flag to forcefully privilege related errors
sed "s/DEFINER=\`.*\`@\`/DEFINER=\`$username\`@\`/" |\
    mysql -f -h $hostname -u $username -p$password $database
