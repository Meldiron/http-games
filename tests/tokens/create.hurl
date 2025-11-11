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

# Missing payload
POST http://localhost:8000/v1/tokens
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

# Partial payloads
POST http://localhost:8000/v1/tokens
{
  "email": "test@httpgames.com"
}
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

POST http://localhost:8000/v1/tokens
{
  "password": "password"
}
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

# Invalid credentials
POST http://localhost:8000/v1/tokens
{
  "email": "test@httpgames.com",
  "password": "password"
}
HTTP 401
[Asserts]
jsonpath "$.type" == "wrong_credentials"

POST http://localhost:8000/v1/tokens
{
  "email": "{{user_email}}",
  "password": "password2"
}
HTTP 401
[Asserts]
jsonpath "$.type" == "wrong_credentials"

# OK request
POST http://localhost:8000/v1/tokens
{
  "email": "{{user_email}}",
  "password": "password"
}
HTTP 201
[Captures]
user_token: jsonpath "$.secret"
[Asserts]
jsonpath "$.id" not isEmpty
jsonpath "$.id" isString
jsonpath "$.id" != "{{user_id}}"
jsonpath "$.userId" == "{{user_id}}"
jsonpath "$.secret" not isEmpty
jsonpath "$.secret" startsWith "sk_"

# Ensure token secret is valid
GET http://localhost:8000/v1/users/{{user_id}}
Authorization: Bearer {{user_token}}
HTTP 200
[Asserts]
jsonpath "$.id" == "{{user_id}}"
