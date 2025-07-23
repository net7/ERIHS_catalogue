#!/bin/bash

# BUILD_CMD="npm install --dev --force && npm run build"
BUILD_CMD="npm install --force && npm run dev"
if [ -n "$1" ]
then
BUILD_ENV=$1
BUILD_CMD="${BUILD_CMD} -- -c ${BUILD_ENV}"
fi
docker run -t -u 0 --rm --name erihs-npm-serve -v "$PWD"/..:/ng -w /ng node:20 sh -c "$BUILD_CMD"
