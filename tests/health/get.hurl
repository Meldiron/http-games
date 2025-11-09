# getHealth basic usage
GET http://localhost:8000/v1/health

HTTP 200
[Asserts]
jsonpath "$.status" == "ok"
