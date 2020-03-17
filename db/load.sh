#!/bin/sh

BASE_DIR=$(dirname "$(readlink -f "$0")")
if [ "$1" != "test" ]; then
    psql -h localhost -U musicnow -d musicnow < $BASE_DIR/musicnow.sql
fi
psql -h localhost -U musicnow -d musicnow_test < $BASE_DIR/musicnow.sql
