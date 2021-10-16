#!/usr/bin/env bash
CMD="$(rm  -Rf backend/vendor/ backend/node_modules/ frontend/node_modules/ .env .docker/database/data/ .docker/app/log/ .docker/nginx/log/ package-lock.json)"
echo $CMD
