# SETUP: Create user & token
POST http://localhost:8000/v1/users
[Options]
variable: user_email={{newUuid}}@httpgames.com
{
  "email": "{{user_email}}",
  "password": "password",
  "passwordConfirmation": "password",
  "nickname": "{{newUuid}}"
}
HTTP 201
POST http://localhost:8000/v1/tokens
{ "email": "{{user_email}}", "password": "password" }
HTTP 201
[Captures]
token: jsonpath "$.secret"

# Unauthorized
POST http://localhost:8000/v1/games/grid-trap/dungeons
HTTP 401
[Asserts]
jsonpath "$.type" == "unauthorized"

# Empty payload
POST http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
HTTP 201
[Asserts]
jsonpath "$.id" not isEmpty
jsonpath "$.id" isString
jsonpath "$.size" == "4x4"
jsonpath "$.hardcore" == false
jsonpath "$.seed" isInteger
jsonpath "$.status" == "started"

# Custom size
POST http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
{
  "size": "7x7"
}
HTTP 201
[Asserts]
jsonpath "$.size" == "7x7"

# Hardcore enabled
POST http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
{
  "hardcore": true
}
HTTP 201
[Asserts]
jsonpath "$.hardcore" == true

# Custom seed
POST http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
{
  "seed": 123456
}
HTTP 201
[Asserts]
jsonpath "$.seed" == 123456
