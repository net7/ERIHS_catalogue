#!/bin/bash
BUILD_CMD="npm install --force && npm run build"
docker run -t -u 0 --rm --name erihs-npm-build -v "$PWD"/..:/ng -w /ng node:20 sh -c "$BUILD_CMD"
