#!/bin/bash

dumpFile="/dump/dump.sql"

if [ ! -e "$dumpFile" ]; then
  printf "Dump was not found. Path $dumpFile"
else
  mysql -v -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < "$dumpFile" 2> /dev/null
fi