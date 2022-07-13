#!/bin/bash

dirName=$(dirname "$0")
currentDir=$(cd "$dirName" && pwd)

printf "Rename env COMMUNICATION - start\n"

old="COMMON_CONFIG_MAIN_COMPONENTS_COMMUNICATION_HOST"
new="COMMON_CONFIG_MAIN_COMPONENTS_COMMS_HOST"

sed -i -e "s/$old/$new/g" .env
sed -i -e "s/$old/$new/g" "$currentDir"/common/config/main-local.php

old="COMMON_CONFIG_MAIN_COMPONENTS_COMMUNICATION_URL"
new="COMMON_CONFIG_MAIN_COMPONENTS_COMMS_URL"

sed -i -e "s/$old/$new/g" .env
sed -i -e "s/$old/$new/g" "$currentDir"/common/config/main-local.php

old="COMMON_CONFIG_MAIN_COMPONENTS_COMMUNICATION_USERNAME"
new="COMMON_CONFIG_MAIN_COMPONENTS_COMMS_USERNAME"

sed -i -e "s/$old/$new/g" .env
sed -i -e "s/$old/$new/g" "$currentDir"/common/config/main-local.php

old="COMMON_CONFIG_MAIN_COMPONENTS_COMMUNICATION_PASSWORD"
new="COMMON_CONFIG_MAIN_COMPONENTS_COMMS_PASSWORD"

sed -i -e "s/$old/$new/g" .env
sed -i -e "s/$old/$new/g" "$currentDir"/common/config/main-local.php

old="COMMON_CONFIG_MAIN_COMPONENTS_COMMUNICATION_VOIPAPIUSERNAME"
new="COMMON_CONFIG_MAIN_COMPONENTS_COMMS_VOIPAPIUSERNAME"

sed -i -e "s/$old/$new/g" .env
sed -i -e "s/$old/$new/g" "$currentDir"/common/config/main-local.php

old="COMMON_CONFIG_MAIN_COMPONENTS_COMMUNICATION_XACCELREDIRECTCOMMUNICATIONURL"
new="COMMON_CONFIG_MAIN_COMPONENTS_COMMS_XACCELREDIRECTCOMMSURL"

sed -i -e "s/$old/$new/g" .env
sed -i -e "s/$old/$new/g" "$currentDir"/common/config/main-local.php

old="'xAccelRedirectCommunicationUrl'"
new="'xAccelRedirectCommsUrl'"

sed -i -e "s/$old/$new/g" .env
sed -i -e "s/$old/$new/g" "$currentDir"/common/config/main-local.php

printf "Rename env COMMUNICATION - finish\n"



printf "Rename env BACKOFFICE - start\n"

old="COMMON_CONFIG_PARAMS_BACKOFFICE_SERVERURL"
new="COMMON_CONFIG_PARAMS_BACKOFFICE_URL"

sed -i -e "s/$old/$new/g" .env
sed -i -e "s/$old/$new/g" "$currentDir"/common/config/params-local.php

old="COMMON_CONFIG_PARAMS_BACKOFFICE_SERVERURL"
new="COMMON_CONFIG_PARAMS_BACKOFFICE_URL"

sed -i -e "s/$old/$new/g" .env
sed -i -e "s/$old/$new/g" "$currentDir"/common/config/params-local.php

old="COMMON_CONFIG_PARAMS_BACKOFFICE_SERVERURLV2"
new="COMMON_CONFIG_PARAMS_BACKOFFICE_URLV2"

sed -i -e "s/$old/$new/g" .env
sed -i -e "s/$old/$new/g" "$currentDir"/common/config/params-local.php

old="COMMON_CONFIG_PARAMS_BACKOFFICE_SERVERURLV3"
new="COMMON_CONFIG_PARAMS_BACKOFFICE_URLV3"

sed -i -e "s/$old/$new/g" .env
sed -i -e "s/$old/$new/g" "$currentDir"/common/config/params-local.php

old="'serverUrl'"
new="'url'"

sed -i -e "s/$old/$new/g" "$currentDir"/common/config/params-local.php

old="'serverUrlV2'"
new="'urlV2'"

sed -i -e "s/$old/$new/g" "$currentDir"/common/config/params-local.php

old="'serverUrlV3'"
new="'urlV3'"

sed -i -e "s/$old/$new/g" "$currentDir"/common/config/params-local.php

printf "Rename env BACKOFFICE - finish\n"