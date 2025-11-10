#!/bin/bash

if [ -f ".env" ] && grep -q "^_APP_DATABASE_OVERRIDE=" .env; then
    # Variable is present in .env file, do nothing
    :
else
    # Variable not in .env file, set default value
    _APP_DATABASE_OVERRIDE=local_$(date +%Y%m%d%H%M%S)
    export _APP_DATABASE_OVERRIDE
fi

php -S 0.0.0.0:8000 app/http.php