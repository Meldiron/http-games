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
[Asserts]
jsonpath "$.token" not isEmpty
jsonpath "$.token" startsWith "sk_"

# Missing payload
POST http://localhost:8000/v1/sessions
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

# Partial payloads
POST http://localhost:8000/v1/sessions
{
  "email": "test@httpgames.com"
}
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

POST http://localhost:8000/v1/sessions
{
  "password": "password"
}
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

# Invalid credentials
POST http://localhost:8000/v1/sessions
{
  "email": "test@httpgames.com",
  "password": "password"
}
HTTP 401
[Asserts]
jsonpath "$.type" == "wrong_credentials"

POST http://localhost:8000/v1/sessions
{
  "email": "{{user_email}}",
  "password": "password2"
}
HTTP 401
[Asserts]
jsonpath "$.type" == "wrong_credentials"

# OK request
POST http://localhost:8000/v1/sessions
{
  "email": "{{user_email}}",
  "password": "password"
}
HTTP 201
[Asserts]
jsonpath "$.id" == "{{user_id}}"
jsonpath "$.email" == "{{user_email}}"
jsonpath "$.nickname" == "{{user_nickname}}"
jsonpath "$.token" == "{{user_token}}"
