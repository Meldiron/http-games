# SETUP: Create user & token & dungeon
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
POST http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
HTTP 201
[Captures]
dungeon_id: jsonpath "$.id"

# Unauthorized
GET http://localhost:8000/v1/games/grid-trap/dungeons/some-dungeon/cartographer
HTTP 401
[Asserts]
jsonpath "$.type" == "unauthorized"

# Invalid dungeon IDs
GET http://localhost:8000/v1/games/grid-trap/dungeons/bad/cartographer
Authorization: Bearer {{token}}
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

GET http://localhost:8000/v1/games/grid-trap/dungeons/valid-but-nonexisting/cartographer
Authorization: Bearer {{token}}
HTTP 404
[Asserts]
jsonpath "$.type" == "dungeon_not_found"

# OK resquest
GET http://localhost:8000/v1/games/grid-trap/dungeons/{{dungeon_id}}/cartographer
Authorization: Bearer {{token}}
HTTP 200
[Asserts]
jsonpath "$.x" >= 1
jsonpath "$.x" <= 4
jsonpath "$.y" == 1
jsonpath "$.trapped" == false

# TODO: Move up and ensure position changed, ensure y=y+1
# TODO: Get trapped, ensure trapped=true

# SETUP: second user & token & dungeon
POST http://localhost:8000/v1/users
[Options]
variable: user2_email={{newUuid}}@httpgames.com
{
  "email": "{{user2_email}}",
  "password": "password",
  "passwordConfirmation": "password",
  "nickname": "{{newUuid}}"
}
HTTP 201
POST http://localhost:8000/v1/tokens
{ "email": "{{user2_email}}", "password": "password" }
HTTP 201
[Captures]
token2: jsonpath "$.secret"
POST http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token2}}
HTTP 201
[Captures]
dungeon2_id: jsonpath "$.id"

# User 1 cannot see user 2 cartographer
GET http://localhost:8000/v1/games/grid-trap/dungeons/{{dungeon2_id}}/cartographer
Authorization: Bearer {{token}}
HTTP 403
[Asserts]
jsonpath "$.type" == "forbidden"

# User 2 can see user 1 cartographer
GET http://localhost:8000/v1/games/grid-trap/dungeons/{{dungeon_id}}/cartographer
Authorization: Bearer {{token2}}
HTTP 403
[Asserts]
jsonpath "$.type" == "forbidden"

# User 2 can see user 2 cartographer
GET http://localhost:8000/v1/games/grid-trap/dungeons/{{dungeon2_id}}/cartographer
Authorization: Bearer {{token2}}
HTTP 200

# User 1 can see user 1 cartographer
GET http://localhost:8000/v1/games/grid-trap/dungeons/{{dungeon_id}}/cartographer
Authorization: Bearer {{token}}
HTTP 200
