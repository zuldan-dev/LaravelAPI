#!/bin/sh
vendor/bin/phpcbf --extensions=php --standard=./codingstyle ./app ./config ./database ./resources ./routes ./tests --ignore=./codingstyle
