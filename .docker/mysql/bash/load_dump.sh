#!/bin/bash

dumpGzFile="/dump/dump.gz"
dumpSqlFile="/dump/dump.sql"

if [ -e "$dumpGzFile" ]; then
  gunzip < "$dumpGzFile" | mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" --force
elif [ -e "$dumpSqlFile" ]; then
  mysql -v -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < "$dumpSqlFile" 2> /dev/null
else
  printf "Dumps was not found. Path $dumpGzFile and $dumpSqlFile\n"
fi
