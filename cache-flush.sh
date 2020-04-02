#!/bin/bash
# @autor Alex Connor 2019-06-19

# Directories
cur=$(pwd)
scriptName=$(basename "$0")

# Build TLS Certificate
run() {
  echo -e "\e[41;1;5m*** Removing runtime cache directory ***\e[0m"

  echo -e ".... 1. Remove Frontend: \"${cur}/frontend/runtime/cache\""
  rm -rf "${cur}/frontend/runtime/cache"

  echo -e ".... 2. Remove Console: \"${cur}/console/runtime/cache\""
  rm -rf "${cur}/console/runtime/cache"

  echo -e ".... 3. Remove WebApi: \"${cur}/webapi/runtime/cache\""
  rm -rf "${cur}/webapi/runtime/cache"

  echo -e ".... 4. Remove Assets: \"${cur}/frontend/web/assets/*\""
  find ${cur}/frontend/web/assets/* ! -name '.gitignore' -exec rm -rf {} \; -prune

}

run
