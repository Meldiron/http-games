# Missing payload
POST http://localhost:8000/v1/users
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

# Partial payloads
POST http://localhost:8000/v1/users
{
  "email": "test@httpgames.com"
}
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

POST http://localhost:8000/v1/users
{
  "email": "test@httpgames.com",
  "password": "password"
}
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

POST http://localhost:8000/v1/users
{
  "email": "test@httpgames.com",
  "password": "password",
  "passwordConfirmation": "password"
}
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

# Invalid email
POST http://localhost:8000/v1/users
{
  "email": "wrongmail",
  "password": "password",
  "passwordConfirmation": "password",
  "nickname": "TestUser"
}
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

# Short nick
POST http://localhost:8000/v1/users
{
  "email": "test@httpgames.com",
  "password": "password",
  "passwordConfirmation": "password",
  "nickname": "Mel"
}
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

# Bad characters in nick
POST http://localhost:8000/v1/users
{
  "email": "test@httpgames.com",
  "password": "password",
  "passwordConfirmation": "password",
  "nickname": "Meldiron the Second"
}
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

# Invalid pass confirmation
POST http://localhost:8000/v1/users
{
  "email": "test@httpgames.com",
  "password": "password",
  "passwordConfirmation": "passwordwrong",
  "nickname": "TestUser"
}
HTTP 400
[Asserts]
jsonpath "$.type" == "passwords_do_not_match"

# Short password
POST http://localhost:8000/v1/users
{
  "email": "test@httpgames.com",
  "password": "pass",
  "passwordConfirmation": "pass",
  "nickname": "TestUser"
}
HTTP 400
[Asserts]
jsonpath "$.type" == "bad_request"

# OK request
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
[Asserts]
jsonpath "$.id" not isEmpty
jsonpath "$.id" isString
jsonpath "$.email" == "{{user_email}}"
jsonpath "$.nickname" == "{{user_nickname}}"

# Duplicate email
POST http://localhost:8000/v1/users
{
  "email": "{{user_email}}",
  "password": "password",
  "passwordConfirmation": "password",
  "nickname": "{{newUuid}}"
}
HTTP 409
[Asserts]
jsonpath "$.type" == "user_already_exists"

# Duplicate nickname
POST http://localhost:8000/v1/users
{
  "email": "{{newUuid}}@httpgames.com",
  "password": "password",
  "passwordConfirmation": "password",
  "nickname": "{{user_nickname}}"
}
HTTP 409
[Asserts]
jsonpath "$.type" == "user_already_exists"

# Duplicate nickname+email
POST http://localhost:8000/v1/users
{
  "email": "{{user_email}}",
  "password": "password",
  "passwordConfirmation": "password",
  "nickname": "{{user_nickname}}"
}
HTTP 409
[Asserts]
jsonpath "$.type" == "user_already_exists"