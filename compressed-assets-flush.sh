#!/bin/bash

# Directories
cur=$(pwd)
#scriptName=$(basename "$0")

# Build TLS Certificate
run() {
  echo -e "\e[41;1;5m*** Removing compressed assets ***\e[0m"

  echo -e ".... 1. Remove all shared assets: \"${cur}/frontend/web/all_shared/build/*\""
  find ${cur}/frontend/web/all_shared/build/* ! -exec rm -rf {} \; -prune

  echo -e ".... 2. Remove fontawesome assets: \"${cur}/frontend/web/fontawesome/build/*\""
  find ${cur}/frontend/web/fontawesome/build/* ! -exec rm -rf {} \; -prune

  echo -e ".... 3. Remove client chat assets: \"${cur}/frontend/web/client_chat/build/*\""
  find ${cur}/frontend/web/client_chat/build/* ! -exec rm -rf {} \; -prune

  echo -e ".... 4. Remove phone widget assets: \"${cur}/frontend/web/web_phone/build/*\""
  find ${cur}/frontend/web/web_phone/build/* ! -exec rm -rf {} \; -prune
}

run
