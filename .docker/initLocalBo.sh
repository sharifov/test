#!/bin/bash

useLocalBo=$(cat .env | grep 'USE_LOCAL_BO' | cut -d "=" -f 2)
useLocalBoUrl=$(cat .env | grep 'NGINX_CONTAINER_LOCAL_BO' | cut -d "=" -f 2)

boUrlReal=$(cat ../../.env | grep 'COMMON_CONFIG_PARAMS_BACKOFFICE_SERVERURL=' | cut -d "=" -f 2)
boUrlReal2=$(cat ../../.env | grep 'COMMON_CONFIG_PARAMS_BACKOFFICE_SERVERURLV2=' | cut -d "=" -f 2)
boUrlReal3=$(cat ../../.env | grep 'COMMON_CONFIG_PARAMS_BACKOFFICE_SERVERURLV3=' | cut -d "=" -f 2)

boUrlLocal="http://$useLocalBoUrl/api/sync"
boUrlLocal2="http://$useLocalBoUrl/api/v2"
boUrlLocal3="http://$useLocalBoUrl/api/v3"

check=$(echo "$boUrlReal" | grep "$boUrlLocal")

if [ "$useLocalBo" == "true" ]; then
  if [ "$check" == "" ]; then
      printf -v boUrl "%s\t##%s" "$boUrlLocal" "$boUrlReal"
      printf -v boUrl2 "%s\t##%s" "$boUrlLocal2" "$boUrlReal2"
      printf -v boUrl3 "%s\t##%s" "$boUrlLocal3" "$boUrlReal3"

      sed -i "s~$boUrlReal~$boUrl~" ../../.env
      sed -i "s~$boUrlReal2~$boUrl2~" ../../.env
      sed -i "s~$boUrlReal3~$boUrl3~" ../../.env

      printf "Init local env\n"
  fi
else
    if [ "$check" != "" ]; then
      sed -i "s~$boUrlLocal\t##~~" ../../.env
      sed -i "s~$boUrlLocal2\t##~$boUrl2~" ../../.env
      sed -i "s~$boUrlLocal3\t##~$boUrl3~" ../../.env

      printf "Disabled local env\n"
  fi
fi