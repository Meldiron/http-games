# SETUP: Create user
POST http://localhost:8000/v1/users
[Options]
variable: user_nickname={{newUuid}}
variable: user_email={{user_nickname}}@httpgames.com
{
  "email": "{{user_email}}",
  "password": "password",
  "passwordConfirmation": "password",
  "nickname": "{{user_nickname}}"
}
HTTP 201
[Captures]
user_id: jsonpath "$.id"
user_token: jsonpath "$.token"

# No auth header
GET http://localhost:8000/v1/users/{{user_id}}
HTTP 401
[Asserts]
jsonpath "$.type" == "unauthorized"

# Wrong auth header
GET http://localhost:8000/v1/users/{{user_id}}
Authorization: Bearer wrongToken
HTTP 401
[Asserts]
jsonpath "$.type" == "unauthorized"

# Correct auth header
GET http://localhost:8000/v1/users/{{user_id}}
Authorization: Bearer {{user_token}}
HTTP 200
[Asserts]
jsonpath "$.id" == "{{user_id}}"
jsonpath "$.email" == "{{user_email}}"
jsonpath "$.nickname" == "{{user_nickname}}"
jsonpath "$.token" == "{{user_token}}"

# SETUP: Create second user
POST http://localhost:8000/v1/users
[Options]
variable: user2_nickname={{newUuid}}
variable: user2_email={{user2_nickname}}@httpgames.com
{
  "email": "{{user2_email}}",
  "password": "password",
  "passwordConfirmation": "password",
  "nickname": "{{user2_nickname}}"
}
HTTP 201
[Captures]
user2_id: jsonpath "$.id"
user2_token: jsonpath "$.token"

# User 1 fails to get user 2 account
GET http://localhost:8000/v1/users/{{user2_id}}
Authorization: Bearer {{user_token}}
HTTP 401
[Asserts]
jsonpath "$.type" == "forbidden"

# User 1 OK to get user 1 account
GET http://localhost:8000/v1/users/{{user_id}}
Authorization: Bearer {{user_token}}
HTTP 200
[Asserts]
jsonpath "$.id" == "{{user_id}}"

# User 2 fails to get user 1 account
GET http://localhost:8000/v1/users/{{user_id}}
Authorization: Bearer {{user2_token}}
HTTP 401
[Asserts]
jsonpath "$.type" == "forbidden"

# User 2 OK to get user 2 account
GET http://localhost:8000/v1/users/{{user2_id}}
Authorization: Bearer {{user2_token}}
HTTP 200
[Asserts]
jsonpath "$.id" == "{{user2_id}}"
