#!/bin/sh

if [ "$1" = "travis" ]; then
    psql -U postgres -c "CREATE DATABASE musicnow_test;"
    psql -U postgres -c "CREATE USER musicnow PASSWORD 'musicnow' SUPERUSER;"
else
    sudo -u postgres dropdb --if-exists musicnow
    sudo -u postgres dropdb --if-exists musicnow_test
    sudo -u postgres dropuser --if-exists musicnow
    sudo -u postgres psql -c "CREATE USER musicnow PASSWORD 'musicnow' SUPERUSER;"
    sudo -u postgres createdb -O musicnow musicnow
    sudo -u postgres psql -d musicnow -c "CREATE EXTENSION pgcrypto;" 2>/dev/null
    sudo -u postgres createdb -O musicnow musicnow_test
    sudo -u postgres psql -d musicnow_test -c "CREATE EXTENSION pgcrypto;" 2>/dev/null
    LINE="localhost:5432:*:musicnow:musicnow"
    FILE=~/.pgpass
    if [ ! -f $FILE ]; then
        touch $FILE
        chmod 600 $FILE
    fi
    if ! grep -qsF "$LINE" $FILE; then
        echo "$LINE" >> $FILE
    fi
fi
