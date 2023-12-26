#!/bin/sh

sh phpcs.sh

RESULT=$?
if [ $RESULT -ne 0 ]
  then
    echo  "Push was not executed, because PHPCS lint failed"
    exit 1
fi

sh phpstan.sh

RESULT=$?
if [ $RESULT -ne 0 ]
  then
    echo  "Push was not executed, because PHPSTAN lint failed"
    exit 1
fi

exit 0
