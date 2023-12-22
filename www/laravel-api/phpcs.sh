#!/bin/sh
vendor/bin/phpcs --extensions=php --standard=./codingstyle ./app ./config ./database ./resources ./routes ./tests --ignore=./codingstyle
