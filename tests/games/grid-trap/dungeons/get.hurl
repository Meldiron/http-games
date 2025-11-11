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

# SETUP: Create game 1
POST http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
HTTP 201
[Captures]
game_id: jsonpath "$.id"
game_seed: jsonpath "$.seed"

# SETUP: Create game 2
POST http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
HTTP 201
[Captures]
game2_id: jsonpath "$.id"
game2_seed: jsonpath "$.seed"

# Unauthorized
GET http://localhost:8000/v1/games/grid-trap/dungeons
HTTP 401
[Asserts]
jsonpath "$.type" == "unauthorized"

# Get game 1
GET http://localhost:8000/v1/games/grid-trap/dungeons/{{game_id}}
Authorization: Bearer {{token}}
HTTP 200
[Asserts]
jsonpath "$.id" == "{{game_id}}"
jsonpath "$.seed" == {{game_seed}}

# Get game 2
GET http://localhost:8000/v1/games/grid-trap/dungeons/{{game2_id}}
Authorization: Bearer {{token}}
HTTP 200
[Asserts]
jsonpath "$.id" == "{{game2_id}}"
jsonpath "$.seed" == {{game2_seed}}

# SETUP: Second user & token
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

# SETUP: Create game 3 (as 2nd user)
POST http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token2}}
HTTP 201
[Captures]
game3_id: jsonpath "$.id"

# User 1 cannot see game 3
GET http://localhost:8000/v1/games/grid-trap/dungeons/{{game3_id}}
Authorization: Bearer {{token}}
HTTP 403
[Asserts]
jsonpath "$.type" == "forbidden"

# User 2 can see game 3
GET http://localhost:8000/v1/games/grid-trap/dungeons/{{game3_id}}
Authorization: Bearer {{token2}}
HTTP 200
[Asserts]
jsonpath "$.id" == "{{game3_id}}"

# User 1 can see game 2
GET http://localhost:8000/v1/games/grid-trap/dungeons/{{game2_id}}
Authorization: Bearer {{token}}
HTTP 200
[Asserts]
jsonpath "$.id" == "{{game2_id}}"

# User 2 can see game 2
GET http://localhost:8000/v1/games/grid-trap/dungeons/{{game2_id}}
Authorization: Bearer {{token2}}
HTTP 403
[Asserts]
jsonpath "$.type" == "forbidden"