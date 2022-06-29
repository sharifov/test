#!/bin/bash

dumpFile="/dump/dump.sql"

if [ ! -e "$dumpFile" ]; then
  printf "Dump was not found. Path $dumpFile"
else
  psql -U "$POSTGRES_USER" -d "$POSTGRES_DB" < /dump/dump.sql 2> /dev/null
fi