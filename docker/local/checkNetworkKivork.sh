#!/bin/bash

if [ -z $(docker network ls -f name=kivork-proxy -q) ]; then
    printf "\nStart - Crete kivork-proxy network \n\n"
    docker network create kivork-proxy
    printf "\nDone - Crete kivork-proxy network \n\n"
else
    printf "\nKivork-proxy network is exists\n\n"
fi