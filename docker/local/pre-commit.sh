#!/bin/sh

PROJECT=`php -r "echo dirname(realpath('$0'));"`
STAGED_FILES_CMD=`git diff --cached --name-only --diff-filter=ACMR HEAD | grep \\\\.php`

# Determine if a file list is passed
if [ "$#" -eq 1 ]
then
    oIFS=$IFS
    IFS='
    '
    SFILES="$1"
    IFS=$oIFS
fi
SFILES=${SFILES:-$STAGED_FILES_CMD}

if [ "$SFILES" = "" ]; then
  exit 0
fi

### PHP Linter

#which ./vendor/bin/phplint &> /dev/null
#if [ "$?" == 1 ]; then
#  echo "Please install PHP Lint"
#  exit 1
#fi

echo "---------- Running PHP Lint -------------"

parse_error_count=0
for FILE in $SFILES
do
    echo "#"
    echo "Check: $FILE"
    ./vendor/bin/phplint --configuration=.phplint.yml $PROJECT/$FILE
    if [ $? != 0 ]
    then
        echo "Failed"
        parse_error_count=$((parse_error_count+1))
    else
        echo "Passed"
    fi
    FILES="$FILES $PROJECT/$FILE"
done

if [ "$parse_error_count" != 0 ]; then
    echo "#"
    echo "$parse_error_count PHP Parse error(s) were found!"
    echo "Please fix errors before commit!"
    exit 1
fi

### PHP Code Sniffer

#which ./vendor/bin/phpcs &> /dev/null
#if [[ "$?" == 1 ]]; then
#  echo "Please install PHP Code Sniffer"
#  exit 1
#fi

if [ "$FILES" != "" ]
then
    echo "---------- Running Code Sniffer -------------"
    ./vendor/bin/phpcs $FILES
    if [ $? != 0 ]
    then
        echo "Please fix errors before commit!"
        exit 1
    fi
fi

exit $?
####  ALL project files  ####
#
##PHP Linter
#
#echo "Running PHP Lint..."
#
#./vendor/bin/phplint
#if [ $? != 0 ]
#then
#    echo "Please fix the following errors before commit!"
#    exit 1
#fi
#
##PHP Code Sniffer
#
#echo "Running Code Sniffer..."
#
#./vendor/bin/phpcs
#if [ $? != 0 ]
#then
#    echo "Please fix the following errors before commit!"
#    exit 1
#fi
#
#exit $?