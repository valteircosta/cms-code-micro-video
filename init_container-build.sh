#/bin/bash
export UID=${UID}
export GID=${GID}
docker-compose up -d --build
