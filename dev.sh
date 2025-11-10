#!/bin/bash

if [ -z "$_APP_DATABASE_OVERRIDE" ]; then
    _APP_DATABASE_OVERRIDE=local_$(date +%Y%m%d%H%M%S)
    export _APP_DATABASE_OVERRIDE
fi

php -S 0.0.0.0:8000 app/http.php