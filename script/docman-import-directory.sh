#!/bin/bash

set -e

DIR="$(dirname "$(readlink -f "$0")")"
cd "$DIR/.."

php artisan docman:importdirectory

chown -R root:www-data storage/app/documents
chmod -R g+w storage
