#!/bin/bash
SOURCE_DIR=$(dirname $(readlink -f $0))

if [ "$1" == "init" ]; then
    composer dump -o --no-dev
    php -S 0.0.0.0:8000 -t /var/www/html
else
	CONTAINER_NAME="lifetrack-exam-container"
	docker build -t $CONTAINER_NAME .
	docker run --rm \
	    --mount type=bind,src=$SOURCE_DIR,dst=/var/www/html \
	    --name $CONTAINER_NAME \
	    -it $CONTAINER_NAME bash;
fi
