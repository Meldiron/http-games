# TODO: Test listDungeons (only see mine, can paginate, can set limit, total matches, cursor provided)

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
GET http://localhost:8000/v1/games/grid-trap/dungeons
HTTP 401
[Asserts]
jsonpath "$.type" == "unauthorized"

# Empty list
GET http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
HTTP 200
[Asserts]
jsonpath "$.total" == 0
jsonpath "$.dungeons" count == 0

# Empty list unaffected by page limit
GET http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
[Query]
limit: 1
HTTP 200
[Asserts]
jsonpath "$.total" == 0
jsonpath "$.dungeons" count == 0

# Empty list with wrong cursor is 404
GET http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
[Query]
cursor: after:some-random-id
HTTP 404
[Asserts]
jsonpath "$.type" == "cursor_not_found"

# SETUP: Create 3 games
POST http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
HTTP 201
[Captures]
game_id: jsonpath "$.id"

POST http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
{
    "size": "7x7"
}
HTTP 201
[Captures]
game2_id: jsonpath "$.id"

POST http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
{
    "size": "7x7",
    "hardcore": true
}
HTTP 201
[Captures]
game3_id: jsonpath "$.id"

# List of 3 games
GET http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
HTTP 200
[Asserts]
jsonpath "$.total" == 3
jsonpath "$.cursorNext" not isEmpty
jsonpath "$.cursorNext" isString
jsonpath "$.cursorPrevious" not isEmpty
jsonpath "$.cursorPrevious" isString
jsonpath "$.dungeons" count == 3
jsonpath "$.dungeons[0].id" not isEmpty
jsonpath "$.dungeons[0].id" isString
jsonpath "$.dungeons[0].seed" isInteger
jsonpath "$.dungeons[0].seed" > 0
jsonpath "$.dungeons[0].size" not isEmpty
jsonpath "$.dungeons[0].size" isString
jsonpath "$.dungeons[0].hardcore" isBoolean

# List of 3 games with limit 2
GET http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
[Query]
limit: 2
HTTP 200
[Asserts]
jsonpath "$.total" == 3
jsonpath "$.dungeons" count == 2

# List of 3 games with limit 1
GET http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
[Query]
limit: 1
HTTP 200
[Captures]
cursor_next: jsonpath "$.cursorNext"
cursor_previous: jsonpath "$.cursorPrevious"
[Asserts]
jsonpath "$.total" == 3
jsonpath "$.dungeons" count == 1

# List of 3 games, cursor next (after)
GET http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
[Query]
cursor: {{cursor_next}}
HTTP 200
[Captures]
cursor2_previous: jsonpath "$.cursorPrevious"
[Asserts]
jsonpath "$.total" == 3
jsonpath "$.dungeons" count == 2

# List of 3 games, cursor previous (before)
GET http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
[Query]
cursor: {{cursor_previous}}
HTTP 200
[Asserts]
jsonpath "$.total" == 3
jsonpath "$.dungeons" count == 0

# List of 3 games, cursor previous (before), last game
GET http://localhost:8000/v1/games/grid-trap/dungeons
Authorization: Bearer {{token}}
[Query]
cursor: {{cursor2_previous}}
HTTP 200
[Asserts]
jsonpath "$.total" == 3
jsonpath "$.dungeons" count == 1

# TODO: Test queries (hardcore, size - comma-separated)