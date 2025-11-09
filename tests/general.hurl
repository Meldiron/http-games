# Any path gives 404
GET http://localhost:8000/v1/non-existing

HTTP 404
[Asserts]
jsonpath "$.type" == "path_not_found"
