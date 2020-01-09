define({ "api": [
  {
    "type": "get, post",
    "url": "/v1/app/test",
    "title": "API Test action",
    "version": "0.1.0",
    "name": "TestApp",
    "group": "App",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"message\": \"Server Name: api.host.test\",\n    \"code\": 0,\n    \"date\": \"2018-05-30\",\n    \"time\": \"16:01:17\",\n    \"ip\": \"127.0.0.1\",\n    \"get\": [],\n    \"post\": [],\n    \"files\": [],\n    \"headers\": {\n        \"Accept-Language\": \"ru,en-US;q=0.9,en;q=0.8,zh;q=0.7,zh-TW;q=0.6,zh-CN;q=0.5,ko;q=0.4,de;q=0.3\",\n        \"Accept-Encoding\": \"gzip, deflate\",\n        \"Dnt\": \"1\",\n        \"Accept\": \"*\\/*\",\n        \"Postman-Token\": \"6ce239ad-5e05-cc88-13d1-ba2ff5538720\",\n        \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViYQ==\",\n        \"Cache-Control\": \"no-cache\",\n        \"User-Agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36\",\n        \"Connection\": \"keep-alive\",\n        \"Host\": \"api.bookair.zeit.test\"\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/AppController.php",
    "groupTitle": "App"
  },
  {
    "type": "post",
    "url": "/v1/communication/email",
    "title": "Communication Email",
    "version": "0.1.0",
    "name": "CommunicationEmail",
    "group": "Communication",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"type\": \"update_email_status\",\n    \"eq_id\": 127,\n    \"eq_status_id\": 5,\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/CommunicationController.php",
    "groupTitle": "Communication"
  },
  {
    "type": "post",
    "url": "/v1/communication/sms",
    "title": "Communication SMS",
    "version": "0.1.0",
    "name": "CommunicationSms",
    "group": "Communication",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"type\": \"update_sms_status\",\n    \"sq_id\": 127,\n    \"sq_status_id\": 5,\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/CommunicationController.php",
    "groupTitle": "Communication"
  },
  {
    "type": "post",
    "url": "/v1/communication/voice",
    "title": "Communication Voice",
    "version": "0.1.0",
    "name": "CommunicationVoice",
    "group": "Communication",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"type\": \"update_sms_status\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/CommunicationController.php",
    "groupTitle": "Communication"
  },
  {
    "type": "post",
    "url": "/v2/department-phone-project/get",
    "title": "Get Department Phone Project",
    "version": "0.2.0",
    "name": "GetDepartmentPhoneProject",
    "group": "DepartmentPhoneProject",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "project_id",
            "description": "<p>Project ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "allowedValues": [
              "Sales",
              "Exchange",
              "Support"
            ],
            "optional": true,
            "field": "department",
            "description": "<p>Department</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n{\n    \"project_id\": 6,\n    \"department\": \"Sales\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n       \"status\": 200\n       \"message\": \"OK\",\n       \"data\": {\n           \"phones\": [\n               {\n                   \"phone\": \"+15211111111\",\n                   \"cid\": \"WOWMAC\",\n                   \"department_id\": 1,\n                   \"department\": \"Sales\",\n                   \"updated_dt\": \"2019-01-08 11:44:57\"\n               },\n               {\n                   \"phone\": \"+15222222222\",\n                   \"cid\": \"WSUDCV\",\n                   \"department_id\": 3,\n                   \"department\": \"Support\",\n                   \"updated_dt\": \"2019-01-09 11:50:25\"\n              }\n           ]\n       },\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n       \"status\": 422,\n       \"message\": \"Validation error\",\n       \"errors\": {\n            \"project_id\": [\n                \"Project Id cannot be blank.\"\n            ],\n            \"department\": [\n                \"Department is invalid.\"\n            ]\n       },\n       \"code\": \"14301\",\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n{\n      \"status\": 400,\n      \"message\": \"Load data error\",\n      \"errors\": [\n           \"Not found Department Phone Project data on POST request\"\n      ],\n      \"code\": \"14300\",\n      \"request\": {\n          ...\n      },\n      \"technical\": {\n          ...\n     }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/DepartmentPhoneProjectController.php",
    "groupTitle": "DepartmentPhoneProject"
  },
  {
    "type": "post",
    "url": "/v2/lead/create",
    "title": "Create Lead",
    "version": "0.2.0",
    "name": "CreateLead",
    "group": "Leads",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "lead",
            "description": "<p>Lead data array</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "20",
            "optional": false,
            "field": "lead.source_code",
            "description": "<p>Source Code</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "0..9",
            "optional": false,
            "field": "lead.adults",
            "description": "<p>Adult count</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "0..9",
            "optional": false,
            "field": "lead.children",
            "description": "<p>Children count</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "0..9",
            "optional": false,
            "field": "lead.infants",
            "description": "<p>Infants count</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": true,
            "field": "lead.request_ip",
            "description": "<p>Request IP</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "32",
            "optional": true,
            "field": "lead.discount_id",
            "description": "<p>Discount ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "40",
            "optional": false,
            "field": "lead.uid",
            "description": "<p>UID value</p>"
          },
          {
            "group": "Parameter",
            "type": "text",
            "optional": true,
            "field": "lead.user_agent",
            "description": "<p>User agent info</p>"
          },
          {
            "group": "Parameter",
            "type": "object[]",
            "optional": false,
            "field": "lead.segments",
            "description": "<p>Segments</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3",
            "optional": false,
            "field": "lead.segments.origin",
            "description": "<p>Segment Origin location Airport IATA-code</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3",
            "optional": false,
            "field": "lead.segments.destination",
            "description": "<p>Segment Destination location Airport IATA-code</p>"
          },
          {
            "group": "Parameter",
            "type": "datetime",
            "size": "YYYY-MM-DD",
            "optional": false,
            "field": "lead.segments.departure",
            "description": "<p>Segment Departure DateTime (format YYYY-MM-DD)</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "lead.client",
            "description": "<p>Client</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "20",
            "optional": false,
            "field": "lead.client.phone",
            "description": "<p>Client phone</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "2",
            "allowedValues": [
              "14-BOOK_FAILED",
              "15-ALTERNATIVE"
            ],
            "optional": false,
            "field": "lead.status",
            "description": "<p>Status</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "1",
            "allowedValues": [
              "E-ECONOMY",
              "B-BUSINESS",
              "F-FIRST",
              "P-PREMIUM"
            ],
            "optional": false,
            "field": "lead.cabin",
            "description": "<p>Cabin</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "lead.flight_id",
            "description": "<p>BO Flight ID</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n{\n     \"lead\": {\n          \"client\": {\n              \"phone\": \"+37369333333\"\n          },\n          \"uid\": \"WD6q53PO3b\",\n          \"status\": 14,\n          \"source_code\": \"JIVOCH\",\n          \"cabin\": \"E\",\n          \"adults\": 2,\n          \"children\": 2,\n          \"infants\": 2,\n          \"request_ip\": \"12.12.12.12\",\n          \"discount_id\": \"123123\",\n          \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36\",\n          \"flight_id\": 12457,\n          \"segments\": [\n              {\n                  \"origin\": \"NYC\",\n                  \"destination\": \"LON\",\n                  \"departure\": \"2019-12-16\"\n              },\n              {\n                  \"origin\": \"LON\",\n                  \"destination\": \"NYC\",\n                  \"departure\": \"2019-12-17\"\n              },\n              {\n                  \"origin\": \"LON\",\n                  \"destination\": \"NYC\",\n                  \"departure\": \"2019-12-18\"\n              }\n          ]\n      }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n      \"status\": 200,\n      \"message\": \"OK\",\n      \"data\": {\n         \"lead\": {\n              \"id\": 370949,\n              \"uid\": \"WD6q53PO3b\",\n              \"gid\": \"63e1505f4a8a87e6651048e3e3eae4e1\"\n          }\n      }\n      \"request\": {\n          \"lead\": {\n             \"client\": {\n                  \"phone\": \"+37369636963\"\n              },\n              \"uid\": \"WD6q53PO3b\",\n              \"status\": 14,\n              \"source_code\": \"JIVOCH\",\n              \"cabin\": \"E\",\n              \"adults\": 2,\n              \"children\": 2,\n              \"infants\": 2,\n              \"request_ip\": \"12.12.12.12\",\n              \"discount_id\": \"123123\",\n              \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36\",\n              \"flight_id\": 12457,\n              \"segments\": [\n                  {\n                      \"origin\": \"NYC\",\n                      \"destination\": \"LON\",\n                      \"departure\": \"2019-12-16\"\n                  },\n                  {\n                      \"origin\": \"LON\",\n                      \"destination\": \"NYC\",\n                      \"departure\": \"2019-12-17\"\n                  },\n                  {\n                      \"origin\": \"LON\",\n                      \"destination\": \"NYC\",\n                      \"departure\": \"2019-12-18\"\n                  }\n              ]\n          }\n      },\n      \"technical\": {\n          \"action\": \"v2/lead/create\",\n          \"response_id\": 11930215,\n          \"request_dt\": \"2019-12-30 12:22:20\",\n          \"response_dt\": \"2019-12-30 12:22:21\",\n          \"execution_time\": 0.055,\n          \"memory_usage\": 1394416\n      }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n      \"status\": 422,\n      \"message\": \"Validation error\",\n      \"errors\": {\n          \"children\": [\n              \"Children must be no greater than 9.\"\n          ],\n          \"segments[0][origin]\": [\n              \"IATA (NY) not found.\"\n          ],\n          \"segments[2][departure]\": [\n              \"The format of Departure is invalid.\"\n          ],\n          \"client[phone]\": [\n             \"The format of Phone is invalid.\"\n          ]\n      },\n      \"code\": 10301,\n      \"request\": {\n          ...\n      },\n      \"technical\": {\n          ...\n     }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n{\n      \"status\": 400,\n      \"message\": \"Load data error\",\n      \"errors\": [\n          \"Not found Lead data on POST request\"\n      ],\n      \"code\": 10300,\n      \"request\": {\n          ...\n      },\n      \"technical\": {\n          ...\n     }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n      \"status\": 422,\n      \"message\": \"Saving error\",\n      \"errors\": [\n          \"Saving error\"\n      ],\n      \"code\": 10101,\n      \"request\": {\n          ...\n      },\n      \"technical\": {\n          ...\n     }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/LeadController.php",
    "groupTitle": "Leads"
  },
  {
    "type": "post",
    "url": "/v1/lead/create",
    "title": "Create Lead",
    "version": "0.1.0",
    "name": "CreateLead",
    "group": "Leads",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "apiKey",
            "description": "<p>API Key for Project (if not use Basic-Authorization)</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "lead",
            "description": "<p>Lead data array</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "lead.source_id",
            "description": "<p>Source ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "20",
            "optional": false,
            "field": "lead.sub_sources_code",
            "description": "<p>Source Code</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "1..9",
            "optional": false,
            "field": "lead.adults",
            "description": "<p>Adult count</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "1",
            "allowedValues": [
              "E-ECONOMY",
              "B-BUSINESS",
              "F-FIRST",
              "P-PREMIUM"
            ],
            "optional": false,
            "field": "lead.cabin",
            "description": "<p>Cabin</p>"
          },
          {
            "group": "Parameter",
            "type": "array[]",
            "optional": false,
            "field": "lead.emails",
            "description": "<p>Array of Emails (string)</p>"
          },
          {
            "group": "Parameter",
            "type": "array[]",
            "optional": false,
            "field": "lead.phones",
            "description": "<p>Array of Phones (string)</p>"
          },
          {
            "group": "Parameter",
            "type": "object[]",
            "optional": false,
            "field": "lead.flights",
            "description": "<p>Array of Flights</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3",
            "optional": false,
            "field": "lead.flights.origin",
            "description": "<p>Flight Origin location Airport IATA-code</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3",
            "optional": false,
            "field": "lead.flights.destination",
            "description": "<p>Flight Destination location Airport IATA-code</p>"
          },
          {
            "group": "Parameter",
            "type": "datetime",
            "size": "YYYY-MM-DD HH:II:SS",
            "optional": false,
            "field": "lead.flights.departure",
            "description": "<p>Flight Departure DateTime (format YYYY-MM-DD HH:ii:ss)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "2",
            "allowedValues": [
              "OW-ONE_WAY",
              "RT-ROUND_TRIP",
              "MC-MULTI_DESTINATION"
            ],
            "optional": true,
            "field": "lead.trip_type",
            "description": "<p>Trip type (if empty - autocomplete)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "allowedValues": [
              "1-PENDING",
              "2-PROCESSING",
              "4-REJECT",
              "5-FOLLOW_UP",
              "8-ON_HOLD",
              "10-SOLD",
              "11-TRASH",
              "12-BOOKED",
              "13-SNOOZE"
            ],
            "optional": true,
            "field": "lead.status",
            "description": "<p>Status</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "0..9",
            "optional": true,
            "field": "lead.children",
            "description": "<p>Children count</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "0..9",
            "optional": true,
            "field": "lead.infants",
            "description": "<p>Infant count</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "40",
            "optional": true,
            "field": "lead.uid",
            "description": "<p>UID value</p>"
          },
          {
            "group": "Parameter",
            "type": "text",
            "optional": true,
            "field": "lead.notes_for_experts",
            "description": "<p>Notes for expert</p>"
          },
          {
            "group": "Parameter",
            "type": "text",
            "optional": true,
            "field": "lead.request_ip_detail",
            "description": "<p>Request IP detail (autocomplete)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": true,
            "field": "lead.request_ip",
            "description": "<p>Request IP</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "lead.snooze_for",
            "description": "<p>Snooze for</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "lead.rating",
            "description": "<p>Rating</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "lead.discount_id",
            "description": "<p>Discount Id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3..100",
            "optional": true,
            "field": "lead.client_first_name",
            "description": "<p>Client first name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3..100",
            "optional": true,
            "field": "lead.client_last_name",
            "description": "<p>Client last name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3..100",
            "optional": true,
            "field": "lead.client_middle_name",
            "description": "<p>Client middle name</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n   \"apiKey\": \"d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd\",\n   \"lead\": {\n       \"flights\": [\n           {\n               \"origin\": \"KIV\",\n               \"destination\": \"DME\",\n               \"departure\": \"2018-10-13 13:50:00\",\n           },\n           {\n               \"origin\": \"DME\",\n               \"destination\": \"KIV\",\n               \"departure\": \"2018-10-18 10:54:00\",\n           }\n       ],\n       \"emails\": [\n         \"email1@gmail.com\",\n         \"email2@gmail.com\",\n       ],\n       \"phones\": [\n         \"+373-69-487523\",\n         \"022-45-7895-89\",\n       ],\n       \"source_id\": 38,\n       \"sub_sources_code\": \"BBM101\",\n       \"adults\": 1,\n       \"client_first_name\": \"Alexandr\",\n       \"client_last_name\": \"Freeman\"\n   }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Integer",
            "optional": false,
            "field": "response_id",
            "description": "<p>Response Id</p>"
          },
          {
            "group": "Success 200",
            "type": "DateTime",
            "optional": false,
            "field": "request_dt",
            "description": "<p>Request Date &amp; Time</p>"
          },
          {
            "group": "Success 200",
            "type": "DateTime",
            "optional": false,
            "field": "response_dt",
            "description": "<p>Response Date &amp; Time</p>"
          },
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Data Array</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n{\n\"status\": 200,\n\"name\": \"Success\",\n\"code\": 0,\n\"message\": \"\",\n\"data\": {\n\"response\": {\n\"lead\": {\n\"client_id\": 11,\n\"employee_id\": null,\n\"status\": 1,\n\"uid\": \"5b73b80eaf69b\",\n\"gid\": \"65df1546edccce15518e929e5af1a4\",\n\"project_id\": 6,\n\"source_id\": \"38\",\n\"trip_type\": \"RT\",\n\"cabin\": \"E\",\n\"adults\": \"1\",\n\"children\": 0,\n\"infants\": 0,\n\"notes_for_experts\": null,\n\"created\": \"2018-08-15 05:20:14\",\n\"updated\": \"2018-08-15 05:20:14\",\n\"request_ip\": \"127.0.0.1\",\n\"request_ip_detail\": \"{\\\"ip\\\":\\\"127.0.0.1\\\",\\\"city\\\":\\\"North Pole\\\",\\\"postal\\\":\\\"99705\\\",\\\"state\\\":\\\"Alaska\\\",\\\"state_code\\\":\\\"AK\\\",\\\"country\\\":\\\"United States\\\",\\\"country_code\\\":\\\"US\\\",\\\"location\\\":\\\"64.7548317,-147.3431046\\\",\\\"timezone\\\":{\\\"id\\\":\\\"America\\\\/Anchorage\\\",\\\"location\\\":\\\"61.21805,-149.90028\\\",\\\"country_code\\\":\\\"US\\\",\\\"country_name\\\":\\\"United States of America\\\",\\\"iso3166_1_alpha_2\\\":\\\"US\\\",\\\"iso3166_1_alpha_3\\\":\\\"USA\\\",\\\"un_m49_code\\\":\\\"840\\\",\\\"itu\\\":\\\"USA\\\",\\\"marc\\\":\\\"xxu\\\",\\\"wmo\\\":\\\"US\\\",\\\"ds\\\":\\\"USA\\\",\\\"phone_prefix\\\":\\\"1\\\",\\\"fifa\\\":\\\"USA\\\",\\\"fips\\\":\\\"US\\\",\\\"gual\\\":\\\"259\\\",\\\"ioc\\\":\\\"USA\\\",\\\"currency_alpha_code\\\":\\\"USD\\\",\\\"currency_country_name\\\":\\\"UNITED STATES\\\",\\\"currency_minor_unit\\\":\\\"2\\\",\\\"currency_name\\\":\\\"US Dollar\\\",\\\"currency_code\\\":\\\"840\\\",\\\"independent\\\":\\\"Yes\\\",\\\"capital\\\":\\\"Washington\\\",\\\"continent\\\":\\\"NA\\\",\\\"tld\\\":\\\".us\\\",\\\"languages\\\":\\\"en-US,es-US,haw,fr\\\",\\\"geoname_id\\\":\\\"6252001\\\",\\\"edgar\\\":\\\"\\\"},\\\"datetime\\\":{\\\"date\\\":\\\"08\\\\/14\\\\/2018\\\",\\\"date_time\\\":\\\"08\\\\/14\\\\/2018 21:20:15\\\",\\\"date_time_txt\\\":\\\"Tuesday, August 14, 2018 21:20:15\\\",\\\"date_time_wti\\\":\\\"Tue, 14 Aug 2018 21:20:15 -0800\\\",\\\"date_time_ymd\\\":\\\"2018-08-14T21:20:15-08:00\\\",\\\"time\\\":\\\"21:20:15\\\",\\\"month\\\":\\\"8\\\",\\\"month_wilz\\\":\\\"08\\\",\\\"month_abbr\\\":\\\"Aug\\\",\\\"month_full\\\":\\\"August\\\",\\\"month_days\\\":\\\"31\\\",\\\"day\\\":\\\"14\\\",\\\"day_wilz\\\":\\\"14\\\",\\\"day_abbr\\\":\\\"Tue\\\",\\\"day_full\\\":\\\"Tuesday\\\",\\\"year\\\":\\\"2018\\\",\\\"year_abbr\\\":\\\"18\\\",\\\"hour_12_wolz\\\":\\\"9\\\",\\\"hour_12_wilz\\\":\\\"09\\\",\\\"hour_24_wolz\\\":\\\"21\\\",\\\"hour_24_wilz\\\":\\\"21\\\",\\\"hour_am_pm\\\":\\\"pm\\\",\\\"minutes\\\":\\\"20\\\",\\\"seconds\\\":\\\"15\\\",\\\"week\\\":\\\"33\\\",\\\"offset_seconds\\\":\\\"-28800\\\",\\\"offset_minutes\\\":\\\"-480\\\",\\\"offset_hours\\\":\\\"-8\\\",\\\"offset_gmt\\\":\\\"-08:00\\\",\\\"offset_tzid\\\":\\\"America\\\\/Anchorage\\\",\\\"offset_tzab\\\":\\\"AKDT\\\",\\\"offset_tzfull\\\":\\\"Alaska Daylight Time\\\",\\\"tz_string\\\":\\\"AKST+9AKDT,M3.2.0\\\\/2,M11.1.0\\\\/2\\\",\\\"dst\\\":\\\"true\\\",\\\"dst_observes\\\":\\\"true\\\",\\\"timeday_spe\\\":\\\"evening\\\",\\\"timeday_gen\\\":\\\"evening\\\"}}\",\n\"offset_gmt\": \"-08.00\",\n\"snooze_for\": null,\n\"rating\": null,\n\"id\": 7\n},\n\"flights\": [\n{\n\"origin\": \"BOS\",\n\"destination\": \"LGW\",\n\"departure\": \"2018-09-19\"\n},\n{\n\"origin\": \"LGW\",\n\"destination\": \"BOS\",\n\"departure\": \"2018-09-22\"\n}\n],\n\"emails\": [\n\"chalpet@gmail.com\",\n\"chalpet2@gmail.com\"\n],\n\"phones\": [\n\"+373-69-98-698\",\n\"+373-69-98-698\"\n]\n},\n\"request\": {\n\"client_id\": null,\n\"employee_id\": null,\n\"status\": null,\n\"uid\": null,\n\"project_id\": 6,\n\"source_id\": \"38\",\n\"trip_type\": null,\n\"cabin\": null,\n\"adults\": \"1\",\n\"children\": null,\n\"infants\": null,\n\"notes_for_experts\": null,\n\"created\": null,\n\"updated\": null,\n\"request_ip\": null,\n\"request_ip_detail\": null,\n\"offset_gmt\": null,\n\"snooze_for\": null,\n\"rating\": null,\n\"flights\": [\n{\n\"origin\": \"BOS\",\n\"destination\": \"LGW\",\n\"departure\": \"2018-09-19\"\n},\n{\n\"origin\": \"LGW\",\n\"destination\": \"BOS\",\n\"departure\": \"2018-09-22\"\n}\n],\n\"emails\": [\n\"chalpet@gmail.com\",\n\"chalpet2@gmail.com\"\n],\n\"phones\": [\n\"+373-69-98-698\",\n\"+373-69-98-698\"\n],\n\"client_first_name\": \"Alexandr\",\n\"client_last_name\": \"Freeman\"\n}\n},\n\"action\": \"v1/lead/create\",\n\"response_id\": 42,\n\"request_dt\": \"2018-08-15 05:20:14\",\n\"response_dt\": \"2018-08-15 05:20:15\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "UserNotFound",
            "description": "<p>The id of the User was not found.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 422 Unprocessable entity\n{\n    \"name\": \"Unprocessable entity\",\n    \"message\": \"Flight [0]: Destination should contain at most 3 characters.\",\n    \"code\": 5,\n    \"status\": 422,\n    \"type\": \"yii\\\\web\\\\UnprocessableEntityHttpException\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/LeadController.php",
    "groupTitle": "Leads"
  },
  {
    "type": "post",
    "url": "/v1/lead/get",
    "title": "Get Lead",
    "version": "0.1.0",
    "name": "GetLead",
    "group": "Leads",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "apiKey",
            "description": "<p>API Key for Project (if not use Basic-Authorization)</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "lead",
            "description": "<p>Lead data array</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "lead.lead_id",
            "description": "<p>Lead ID</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "lead.source_id",
            "description": "<p>Source ID</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n   \"apiKey\": \"d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd\",\n   \"lead\": {\n       \"lead_id\": 302,\n       \"source_id\": 38,\n   }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Integer",
            "optional": false,
            "field": "response_id",
            "description": "<p>Response Id</p>"
          },
          {
            "group": "Success 200",
            "type": "DateTime",
            "optional": false,
            "field": "request_dt",
            "description": "<p>Request Date &amp; Time</p>"
          },
          {
            "group": "Success 200",
            "type": "DateTime",
            "optional": false,
            "field": "response_dt",
            "description": "<p>Response Date &amp; Time</p>"
          },
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Data Array</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "UserNotFound",
            "description": "<p>The id of the User was not found.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 404 Not Found\n{\n    \"name\": \"Not Found\",\n    \"message\": \"Not found lead ID: 302\",\n    \"code\": 9,\n    \"status\": 404,\n    \"type\": \"yii\\\\web\\\\NotFoundHttpException\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/LeadController.php",
    "groupTitle": "Leads"
  },
  {
    "type": "post",
    "url": "/v1/lead/update",
    "title": "Update Lead",
    "version": "0.1.0",
    "name": "UpdateLead",
    "group": "Leads",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "apiKey",
            "description": "<p>API Key for Project (if not use Basic-Authorization)</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "lead",
            "description": "<p>Lead data array</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "lead.lead_id",
            "description": "<p>Lead ID</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "lead.source_id",
            "description": "<p>Source ID</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "1..9",
            "optional": false,
            "field": "lead.adults",
            "description": "<p>Adult count</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "1",
            "allowedValues": [
              "E-ECONOMY",
              "B-BUSINESS",
              "F-FIRST",
              "P-PREMIUM"
            ],
            "optional": false,
            "field": "lead.cabin",
            "description": "<p>Cabin</p>"
          },
          {
            "group": "Parameter",
            "type": "array[]",
            "optional": false,
            "field": "lead.emails",
            "description": "<p>Array of Emails (string)</p>"
          },
          {
            "group": "Parameter",
            "type": "array[]",
            "optional": false,
            "field": "lead.phones",
            "description": "<p>Array of Phones (string)</p>"
          },
          {
            "group": "Parameter",
            "type": "object[]",
            "optional": false,
            "field": "lead.flights",
            "description": "<p>Array of Flights</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3",
            "optional": false,
            "field": "lead.flights.origin",
            "description": "<p>Flight Origin location Airport IATA-code</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3",
            "optional": false,
            "field": "lead.flights.destination",
            "description": "<p>Flight Destination location Airport IATA-code</p>"
          },
          {
            "group": "Parameter",
            "type": "datetime",
            "size": "YYYY-MM-DD HH:II:SS",
            "optional": false,
            "field": "lead.flights.departure",
            "description": "<p>Flight Departure DateTime (format YYYY-MM-DD HH:ii:ss)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "2",
            "allowedValues": [
              "OW-ONE_WAY",
              "RT-ROUND_TRIP",
              "MC-MULTI_DESTINATION"
            ],
            "optional": true,
            "field": "lead.trip_type",
            "description": "<p>Trip type (if empty - autocomplete)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "allowedValues": [
              "1-PENDING",
              "2-PROCESSING",
              "4-REJECT",
              "5-FOLLOW_UP",
              "8-ON_HOLD",
              "10-SOLD",
              "11-TRASH",
              "12-BOOKED",
              "13-SNOOZE"
            ],
            "optional": true,
            "field": "lead.status",
            "description": "<p>Status</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "0..9",
            "optional": true,
            "field": "lead.children",
            "description": "<p>Children count</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "0..9",
            "optional": true,
            "field": "lead.infants",
            "description": "<p>Infant count</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "40",
            "optional": true,
            "field": "lead.uid",
            "description": "<p>UID value</p>"
          },
          {
            "group": "Parameter",
            "type": "text",
            "optional": true,
            "field": "lead.notes_for_experts",
            "description": "<p>Notes for expert</p>"
          },
          {
            "group": "Parameter",
            "type": "text",
            "optional": true,
            "field": "lead.request_ip_detail",
            "description": "<p>Request IP detail (autocomplete)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": true,
            "field": "lead.request_ip",
            "description": "<p>Request IP</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "lead.snooze_for",
            "description": "<p>Snooze for</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "lead.rating",
            "description": "<p>Rating</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3..100",
            "optional": true,
            "field": "lead.client_first_name",
            "description": "<p>Client first name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3..100",
            "optional": true,
            "field": "lead.client_last_name",
            "description": "<p>Client last name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3..100",
            "optional": true,
            "field": "lead.client_middle_name",
            "description": "<p>Client middle name</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n   \"apiKey\": \"d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd\",\n   \"lead\": {\n       \"lead_id\": 38,\n       \"flights\": [\n           {\n               \"origin\": \"KIV\",\n               \"destination\": \"DME\",\n               \"departure\": \"2018-10-13 13:50:00\",\n           },\n           {\n               \"origin\": \"DME\",\n               \"destination\": \"KIV\",\n               \"departure\": \"2018-10-18 10:54:00\",\n           }\n       ],\n       \"emails\": [\n         \"email1@gmail.com\",\n         \"email2@gmail.com\",\n       ],\n       \"phones\": [\n         \"+373-69-487523\",\n         \"022-45-7895-89\",\n       ],\n       \"source_id\": 38,\n       \"adults\": 1,\n       \"client_first_name\": \"Alexandr\",\n       \"client_last_name\": \"Freeman\"\n   }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Integer",
            "optional": false,
            "field": "response_id",
            "description": "<p>Response Id</p>"
          },
          {
            "group": "Success 200",
            "type": "DateTime",
            "optional": false,
            "field": "request_dt",
            "description": "<p>Request Date &amp; Time</p>"
          },
          {
            "group": "Success 200",
            "type": "DateTime",
            "optional": false,
            "field": "response_dt",
            "description": "<p>Response Date &amp; Time</p>"
          },
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Data Array</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "UserNotFound",
            "description": "<p>The id of the User was not found.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 422 Unprocessable entity\n{\n    \"name\": \"Unprocessable entity\",\n    \"message\": \"Flight [0]: Destination should contain at most 3 characters.\",\n    \"code\": 5,\n    \"status\": 422,\n    \"type\": \"yii\\\\web\\\\UnprocessableEntityHttpException\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/LeadController.php",
    "groupTitle": "Leads"
  },
  {
    "type": "post",
    "url": "/v1/lead/call-expert",
    "title": "Update Lead Call Expert",
    "version": "0.1.0",
    "name": "UpdateLeadCallExpert",
    "group": "Leads",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "call",
            "description": "<p>CallExpert data array</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "call.lce_id",
            "description": "<p>Call Expert ID</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "allowedValues": [
              "1-PENDING",
              "2-PROCESSING",
              "3-DONE",
              "4-CANCEL"
            ],
            "optional": false,
            "field": "call.lce_status_id",
            "description": "<p>Status Id</p>"
          },
          {
            "group": "Parameter",
            "type": "text",
            "optional": false,
            "field": "call.lce_response_text",
            "description": "<p>Response text from Expert (Required on lce_status_id = 3)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "30",
            "optional": false,
            "field": "call.lce_expert_username",
            "description": "<p>Expert Username (Required on lce_status_id = 3)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "call.lce_expert_user_id",
            "description": "<p>Expert Id</p>"
          },
          {
            "group": "Parameter",
            "type": "array[]",
            "optional": true,
            "field": "call.lce_response_lead_quotes",
            "description": "<p>Array of UID quotes (string)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n   \"call\": {\n       \"lce_id\": 38,\n       \"lce_response_text\": \"Message from expert\",\n       \"lce_expert_username\": \"Alex\",\n       \"lce_expert_user_id\": 12,\n       \"lce_response_lead_quotes\": [\n             \"5ccbe7a458765\",\n             \"5ccbe797a6a22\"\n         ],\n       \"lce_status_id\": 2\n   }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "status",
            "description": "<p>Response Status</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Response Name</p>"
          },
          {
            "group": "Success 200",
            "type": "Integer",
            "optional": false,
            "field": "code",
            "description": "<p>Response Code</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response Message</p>"
          },
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Response Data Array</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "action",
            "description": "<p>Response API action</p>"
          },
          {
            "group": "Success 200",
            "type": "Integer",
            "optional": false,
            "field": "response_id",
            "description": "<p>Response Id</p>"
          },
          {
            "group": "Success 200",
            "type": "DateTime",
            "optional": false,
            "field": "request_dt",
            "description": "<p>Request Date &amp; Time</p>"
          },
          {
            "group": "Success 200",
            "type": "DateTime",
            "optional": false,
            "field": "response_dt",
            "description": "<p>Response Date &amp; Time</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n \"status\": 200,\n \"name\": \"Success\",\n \"code\": 0,\n \"message\": \"\",\n \"data\": {\n     \"response\": {\n         \"lce_id\": 8,\n         \"lce_lead_id\": 113947,\n         \"lce_request_text\": \"12\\r\\n2\\r\\nqwe qwe qwe qwe qwe fasd asd fasdf\\r\\n\",\n         \"lce_request_dt\": \"2019-05-03 14:08:20\",\n         \"lce_response_text\": \"Test expert text\",\n         \"lce_response_lead_quotes\": \"[\\\"5ccbe7a458765\\\", \\\"5ccbe797a6a22\\\"]\",\n         \"lce_response_dt\": \"2019-05-07 09:14:01\",\n         \"lce_status_id\": 3,\n         \"lce_agent_user_id\": 167,\n         \"lce_expert_user_id\": \"2\",\n         \"lce_expert_username\": \"Alex\",\n         \"lce_updated_dt\": \"2019-05-07 09:14:01\"\n     }\n },\n \"action\": \"v1/lead/call-expert\",\n \"response_id\": 457671,\n \"request_dt\": \"2019-05-07 09:14:01\",\n \"response_dt\": \"2019-05-07 09:14:01\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "UserNotFound",
            "description": "<p>The id of the User was not found.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n\nHTTP/1.1 401 Unauthorized\n {\n     \"name\": \"Unauthorized\",\n     \"message\": \"Your request was made with invalid credentials.\",\n     \"code\": 0,\n     \"status\": 401,\n     \"type\": \"yii\\\\web\\\\UnauthorizedHttpException\"\n }\n\n\nHTTP/1.1 400 Bad Request\n {\n     \"name\": \"Bad Request\",\n     \"message\": \"Not found LeadCallExpert data on POST request\",\n     \"code\": 6,\n     \"status\": 400,\n     \"type\": \"yii\\\\web\\\\BadRequestHttpException\"\n }\n\n\nHTTP/1.1 404 Not Found\n {\n     \"name\": \"Not Found\",\n     \"message\": \"Not found LeadCallExpert ID: 100\",\n     \"code\": 9,\n     \"status\": 404,\n     \"type\": \"yii\\\\web\\\\NotFoundHttpException\"\n }\n\n\nHTTP/1.1 422 Unprocessable entity\n {\n     \"name\": \"Unprocessable entity\",\n     \"message\": \"Response Text cannot be blank.; Expert Username cannot be blank.\",\n     \"code\": 5,\n     \"status\": 422,\n     \"type\": \"yii\\\\web\\\\UnprocessableEntityHttpException\"\n }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/LeadController.php",
    "groupTitle": "Leads"
  },
  {
    "type": "post",
    "url": "/v2/order/create",
    "title": "Create Order",
    "version": "0.2.0",
    "name": "CreateOrder",
    "group": "Orders",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n       \"status\": \"Success\",\n       \"success\": {\n          \"recordLocator\": \"ORZ7I4\",\n          \"caseNumber\": \"OVAGO-282667-TSMITH-AMADEUS-010220-I1B1L1\",\n          \"totalPrice\": \"573.75\"\n       },\n       \"failure\": [],\n       \"priceInfo\": [],\n       \"errors\": [],\n       \"source\": {\n          \"type\": 1,\n          \"status\": 200\n       },\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n       \"status\": \"Success\",\n       \"success\": [],\n       \"failure\": {\n             \"message\": \"Price Increase\"\n       },\n       \"priceInfo\": {\n          \"totalPrice\": 1389.87,\n          \"totalTax\": 684.58,\n          \"fareType\": \"PUB\",\n          \"bookingClass\": \"WWWW\",\n          \"currency\": \"USD\",\n          \"detail\": {\n              \"ADT\": {\n                  \"quantity\": 2,\n              \"totalFare\": 448.29,\n              \"baseTax\": 342.29,\n              \"baseFare\": 106,\n            }\n          }\n       },\n       \"errors\": [],\n       \"source\": {\n          \"type\": 1,\n          \"status\": 200\n       },\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (500):",
          "content": "\nHTTP/1.1 500 Internal Server Error\n{\n       \"status\": \"Failed\",\n       \"source\": {\n           \"type\": 1,\n           \"status\": 500\n       },\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (404):",
          "content": "\nHTTP/1.1 404 Not Found\n{\n       \"status\": \"Failed\",\n       \"source\": {\n           \"type\": 1,\n           \"status\": 404\n       },\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n       \"status\": \"Failed\",\n       \"message\": \"Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received\",\n       \"errors\": [\n             \"Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received\"\n       ],\n       \"code\": 0,\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/OrderController.php",
    "groupTitle": "Orders"
  },
  {
    "type": "post",
    "url": "/v2/quote/get-info",
    "title": "Get Quote",
    "version": "0.2.0",
    "name": "GetQuote",
    "group": "Quotes",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "size": "13",
            "optional": false,
            "field": "uid",
            "description": "<p>Quote UID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "apiKey",
            "description": "<p>API Key for Project (if not use Basic-Authorization)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "clientIP",
            "description": "<p>Client IP address</p>"
          },
          {
            "group": "Parameter",
            "type": "bool",
            "optional": true,
            "field": "clientUseProxy",
            "description": "<p>Client Use Proxy</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "clientUserAgent",
            "description": "<p>Client User Agent</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n     \"uid\": \"5b6d03d61f078\",\n     \"apiKey\": \"d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "status",
            "description": "<p>Status</p>"
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "result",
            "description": "<p>Result of itinerary and pricing</p>"
          },
          {
            "group": "Success 200",
            "type": "array",
            "optional": false,
            "field": "errors",
            "description": "<p>Errors</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "uid",
            "description": "<p>Quote UID</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "agentName",
            "description": "<p>Agent Name</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "agentEmail",
            "description": "<p>Agent Email</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "agentDirectLine",
            "description": "<p>Agent DirectLine</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "action",
            "description": "<p>Action</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "response_id",
            "description": "<p>Response Id</p>"
          },
          {
            "group": "Success 200",
            "type": "DateTime",
            "optional": false,
            "field": "request_dt",
            "description": "<p>Request Date &amp; Time</p>"
          },
          {
            "group": "Success 200",
            "type": "DateTime",
            "optional": false,
            "field": "response_dt",
            "description": "<p>Response Date &amp; Time</p> <p>&quot;errors&quot;: [], &quot;uid&quot;: &quot;5b7424e858e91&quot;, &quot;agentName&quot;: &quot;admin&quot;, &quot;agentEmail&quot;: &quot;assistant@wowfare.com&quot;, &quot;agentDirectLine&quot;: &quot;+1 888 946 3882&quot;, &quot;action&quot;: &quot;v2/quote/get-info&quot;,</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n\n\n{\"status\": \"Success\",\n    \"result\": {\n        \"prices\": {\n            \"totalPrice\": 2056.98,\n            \"totalTax\": 1058.98,\n            \"isCk\": true\n        },\n        \"passengers\": {\n            \"ADT\": {\n                \"cnt\": 2,\n                \"price\": 1028.49,\n                \"tax\": 529.49,\n                \"baseFare\": 499\n            },\n            \"INF\": {\n                \"cnt\": 1,\n                \"price\": 0,\n                \"tax\": 0,\n                \"baseFare\": 0\n            }\n        },\n        \"trips\": [\n            {\n                \"tripId\": 1,\n                \"segments\": [\n                    {\n                        \"segmentId\": 1,\n                        \"departureTime\": \"2019-12-06 16:20\",\n                        \"arrivalTime\": \"2019-12-06 17:57\",\n                        \"stop\": 0,\n                        \"stops\": null,\n                        \"flightNumber\": \"7312\",\n                        \"bookingClass\": \"T\",\n                        \"duration\": 97,\n                        \"departureAirportCode\": \"IND\",\n                        \"departureAirportTerminal\": \"\",\n                        \"arrivalAirportCode\": \"YYZ\",\n                        \"arrivalAirportTerminal\": \"\",\n                        \"operatingAirline\": \"AC\",\n                        \"airEquipType\": null,\n                        \"marketingAirline\": \"AC\",\n                        \"cabin\": \"Y\",\n                        \"ticket_id\": 1,\n                        \"baggage\": {\n                            \"\": {\n                                \"allowPieces\": 2,\n                                \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\",\n                                \"charge\": {\n                                    \"price\": 100,\n                                    \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                    \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                    \"firstPiece\": 1,\n                                    \"lastPiece\": 1\n                                }\n                            }\n                        }\n                    },\n                    {\n                        \"segmentId\": 2,\n                        \"departureTime\": \"2019-12-06 20:45\",\n                        \"arrivalTime\": \"2019-12-07 09:55\",\n                        \"stop\": 0,\n                        \"stops\": null,\n                        \"flightNumber\": \"880\",\n                        \"bookingClass\": \"T\",\n                        \"duration\": 430,\n                        \"departureAirportCode\": \"YYZ\",\n                        \"departureAirportTerminal\": \"\",\n                        \"arrivalAirportCode\": \"CDG\",\n                        \"arrivalAirportTerminal\": \"\",\n                        \"operatingAirline\": \"AC\",\n                        \"airEquipType\": null,\n                        \"marketingAirline\": \"AC\",\n                        \"cabin\": \"Y\",\n                        \"ticket_id\": 2,\n                        \"baggage\": {\n                            \"\": {\n                                \"allowPieces\": 2,\n                                \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\",\n                                \"charge\": {\n                                    \"price\": 100,\n                                    \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                    \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                    \"firstPiece\": 1,\n                                    \"lastPiece\": 1\n                                }\n                            }\n                        }\n                    },\n                    {\n                        \"segmentId\": 3,\n                        \"departureTime\": \"2019-12-07 13:40\",\n                        \"arrivalTime\": \"2019-12-07 19:05\",\n                        \"stop\": 0,\n                        \"stops\": null,\n                        \"flightNumber\": \"6692\",\n                        \"bookingClass\": \"T\",\n                        \"duration\": 265,\n                        \"departureAirportCode\": \"CDG\",\n                        \"departureAirportTerminal\": \"\",\n                        \"arrivalAirportCode\": \"IST\",\n                        \"arrivalAirportTerminal\": \"\",\n                        \"operatingAirline\": \"AC\",\n                        \"airEquipType\": null,\n                        \"marketingAirline\": \"AC\",\n                        \"cabin\": \"Y\",\n                        \"ticket_id\": 2,\n                        \"baggage\": {\n                            \"\": {\n                                \"allowPieces\": 2,\n                                \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\",\n                                \"charge\": {\n                                    \"price\": 100,\n                                    \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                    \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                    \"firstPiece\": 1,\n                                    \"lastPiece\": 1\n                                }\n                            }\n                        }\n                    }\n                ],\n                \"duration\": 1185\n            },\n            {\n                \"tripId\": 2,\n                \"segments\": [\n                    {\n                        \"segmentId\": 1,\n                        \"departureTime\": \"2019-12-25 09:15\",\n                        \"arrivalTime\": \"2019-12-25 10:35\",\n                        \"stop\": 0,\n                        \"stops\": null,\n                        \"flightNumber\": \"6681\",\n                        \"bookingClass\": \"T\",\n                        \"duration\": 140,\n                        \"departureAirportCode\": \"IST\",\n                        \"departureAirportTerminal\": \"\",\n                        \"arrivalAirportCode\": \"GVA\",\n                        \"arrivalAirportTerminal\": \"\",\n                        \"operatingAirline\": \"AC\",\n                        \"airEquipType\": null,\n                        \"marketingAirline\": \"AC\",\n                        \"cabin\": \"Y\",\n                        \"ticket_id\": 1,\n                        \"baggage\": {\n                            \"\": {\n                                \"allowPieces\": 1,\n                                \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡\",\n                                \"allowMaxWeight\": \"UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS\",\n                                \"charge\": {\n                                    \"price\": 100,\n                                    \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                    \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND\",\n                                    \"firstPiece\": 1,\n                                    \"lastPiece\": 1\n                                }\n                            }\n                        }\n                    },\n                    {\n                        \"segmentId\": 2,\n                        \"departureTime\": \"2019-12-25 12:00\",\n                        \"arrivalTime\": \"2019-12-25 17:34\",\n                        \"stop\": 0,\n                        \"stops\": null,\n                        \"flightNumber\": \"835\",\n                        \"bookingClass\": \"T\",\n                        \"duration\": 694,\n                        \"departureAirportCode\": \"GVA\",\n                        \"departureAirportTerminal\": \"\",\n                        \"arrivalAirportCode\": \"YYZ\",\n                        \"arrivalAirportTerminal\": \"\",\n                        \"operatingAirline\": \"AC\",\n                        \"airEquipType\": null,\n                        \"marketingAirline\": \"AC\",\n                        \"cabin\": \"Y\",\n                        \"ticket_id\": 2,\n                        \"baggage\": {\n                            \"\": {\n                                \"allowPieces\": 1,\n                                \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡\",\n                                \"allowMaxWeight\": \"UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS\",\n                                \"charge\": {\n                                    \"price\": 100,\n                                    \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                    \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND\",\n                                    \"firstPiece\": 1,\n                                    \"lastPiece\": 1\n                                }\n                            }\n                        }\n                    },\n                    {\n                        \"segmentId\": 3,\n                        \"departureTime\": \"2019-12-25 20:55\",\n                        \"arrivalTime\": \"2019-12-25 22:37\",\n                        \"stop\": 0,\n                        \"stops\": null,\n                        \"flightNumber\": \"7313\",\n                        \"bookingClass\": \"T\",\n                        \"duration\": 102,\n                        \"departureAirportCode\": \"YYZ\",\n                        \"departureAirportTerminal\": \"\",\n                        \"arrivalAirportCode\": \"IND\",\n                        \"arrivalAirportTerminal\": \"\",\n                        \"operatingAirline\": \"AC\",\n                        \"airEquipType\": null,\n                        \"marketingAirline\": \"AC\",\n                        \"cabin\": \"Y\",\n                        \"ticket_id\": 2,\n                        \"baggage\": {\n                            \"\": {\n                                \"allowPieces\": 1,\n                                \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡\",\n                                \"allowMaxWeight\": \"UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS\",\n                                \"charge\": {\n                                    \"price\": 100,\n                                    \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                    \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND\",\n                                    \"firstPiece\": 1,\n                                    \"lastPiece\": 1\n                                }\n                            }\n                        }\n                    }\n                ],\n                \"duration\": 1222\n            }\n        ],\n        \"validatingCarrier\": \"AC\",\n        \"fareType\": \"PUB\",\n        \"tripType\": \"RT\",\n        \"currency\": \"USD\",\n        \"currencyRate\": 1\n    },\n    \"errors\": [],\n    \"uid\": \"5cb97d1c78486\",\n    \"lead_id\": 92322,\n    \"lead_uid\": \"5cb8735a502f5\",\n    \"lead_delayed_charge\": 0,\n    \"lead_status\": null,\n    \"booked_quote_uid\": null,\n    \"agentName\": \"admin\",\n    \"agentEmail\": \"admin@wowfare.com\",\n    \"agentDirectLine\": \"\",\n    \"generalEmail\": \"info@wowfare.com\",\n    \"generalDirectLine\": \"+37379731662\",\n    \"quote\": {\n        \"id\": 382366,\n        \"uid\": \"5d43e1ec36372\",\n        \"lead_id\": 178363,\n        \"employee_id\": 167,\n        \"record_locator\": \"\",\n        \"pcc\": \"DFWG32100\",\n        \"cabin\": \"E\",\n        \"gds\": \"A\",\n        \"trip_type\": \"OW\",\n        \"main_airline_code\": \"SU\",\n        \"reservation_dump\": \"1  SU1845T  22AUG  KIVSVO    255A    555A  TH\",\n        \"status\": 5,\n        \"check_payment\": 1,\n        \"fare_type\": \"PUB\",\n        \"created\": \"2019-08-02 07:10:36\",\n        \"updated\": \"2019-08-05 08:58:18\",\n        \"created_by_seller\": 1,\n        \"employee_name\": \"alex.connor2\",\n        \"last_ticket_date\": \"2019-08-09 00:00:00\",\n        \"service_fee_percent\": null,\n        \"pricing_info\": null,\n        \"alternative\": 1,\n        \"tickets\": \"[{\\\"key\\\":\\\"02_QVdBUlFBKlkxMDAwL05ZQ01BRDIwMTktMDgtMjYvTUFETllDMjAxOS0wOS0wNipVQX4jVUE1MSNVQTUw\\\",\\\"routingId\\\":0,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-08-11\\\",\\\"totalPrice\\\":392.73,\\\"totalTax\\\":272.73,\\\"markup\\\":50,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"JWZ\\\",\\\"cnt\\\":1,\\\"price\\\":392.73,\\\"tax\\\":272.73,\\\"baseFare\\\":120,\\\"pubBaseFare\\\":120,\\\"baseTax\\\":222.73,\\\"markup\\\":50,\\\"refundPenalty\\\":\\\"\\\",\\\"changePenalty\\\":\\\"Percentage: 100.00%\\\",\\\"endorsementPenalty\\\":\\\"\\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":120,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":222.73,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}}},\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"UA\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"fareType\\\":\\\"SR\\\",\\\"tripType\\\":\\\"RT\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segmentIds\\\":[1]},{\\\"tripId\\\":2,\\\"segmentIds\\\":[3]}]},{\\\"key\\\":\\\"02_QVdBUlFBKlkxMDAwL01BRFZJRTIwMTktMDgtMjcvVklFTUFEMjAxOS0wOS0wNSpMWH4jTFgyMDI3I0xYMzU2OCNMWDM1NjMjTFgyMDQ4\\\",\\\"routingId\\\":0,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-08-09\\\",\\\"totalPrice\\\":305.3,\\\"totalTax\\\":184.3,\\\"markup\\\":50,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"ADT\\\",\\\"cnt\\\":1,\\\"price\\\":305.3,\\\"tax\\\":184.3,\\\"baseFare\\\":121,\\\"pubBaseFare\\\":121,\\\"baseTax\\\":134.3,\\\"markup\\\":50,\\\"refundPenalty\\\":\\\"Percentage: 100.00%\\\",\\\"changePenalty\\\":\\\"Percentage: 100.00%\\\",\\\"endorsementPenalty\\\":\\\"\\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":121,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":134.3,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}}},\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"LX\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"fareType\\\":\\\"PUB\\\",\\\"tripType\\\":\\\"RT\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segmentIds\\\":[2,3]},{\\\"tripId\\\":2,\\\"segmentIds\\\":[1,2]}]}]\",\n        \"origin_search_data\": \"{\\\"key\\\":\\\"01_U0FMMTAxKlkxMDAwL0pGS0ZSQTIwMTktMTEtMjEqUk9+I1FSNzA0I1FSMjI3I1JPMjk4I1JPMzAxOjNkMjBiYzI5LWIzMmItNGJhOC05OTljLTQ4ZTFlYWI1NGU1Ng==\\\",\\\"routingId\\\":306,\\\"gdsOfferId\\\":\\\"3d20bc29-b32b-4ba8-999c-48e1eab54e56\\\",\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-11-23\\\",\\\"totalPrice\\\":670.35,\\\"totalTax\\\":367.35,\\\"markup\\\":100,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":100,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"JWZ\\\",\\\"cnt\\\":1,\\\"price\\\":670.35,\\\"tax\\\":367.35,\\\"baseFare\\\":303,\\\"pubBaseFare\\\":303,\\\"baseTax\\\":267.35,\\\"markup\\\":100,\\\"refundPenalty\\\":\\\"Amount: USD375.00 Percentage: 100.00%\\\",\\\"changePenalty\\\":\\\"Amount: USD260.00 Percentage: 100.00%\\\",\\\"endorsementPenalty\\\":\\\" \\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":303,\\\"currency\\\":\\\"USD\\\"},\\\"oPubBaseFare\\\":{\\\"amount\\\":303,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":267.35,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":100,\\\"currency\\\":\\\"USD\\\"}}},\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segments\\\":[{\\\"segmentId\\\":1,\\\"departureTime\\\":\\\"2019-11-21 09:45\\\",\\\"arrivalTime\\\":\\\"2019-11-22 06:00\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"704\\\",\\\"bookingClass\\\":\\\"N\\\",\\\"duration\\\":735,\\\"departureAirportCode\\\":\\\"JFK\\\",\\\"departureAirportTerminal\\\":\\\"7\\\",\\\"arrivalAirportCode\\\":\\\"DOH\\\",\\\"arrivalAirportTerminal\\\":\\\"\\\",\\\"operatingAirline\\\":\\\"QR\\\",\\\"airEquipType\\\":\\\"351\\\",\\\"marketingAirline\\\":\\\"QR\\\",\\\"marriageGroup\\\":\\\"I\\\",\\\"mileage\\\":6689,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"NLUSN1RO\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"allowPieces\\\":2}},\\\"recheckBaggage\\\":false},{\\\"segmentId\\\":2,\\\"departureTime\\\":\\\"2019-11-22 07:10\\\",\\\"arrivalTime\\\":\\\"2019-11-22 11:25\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"227\\\",\\\"bookingClass\\\":\\\"N\\\",\\\"duration\\\":315,\\\"departureAirportCode\\\":\\\"DOH\\\",\\\"departureAirportTerminal\\\":\\\"\\\",\\\"arrivalAirportCode\\\":\\\"SOF\\\",\\\"arrivalAirportTerminal\\\":\\\"2\\\",\\\"operatingAirline\\\":\\\"QR\\\",\\\"airEquipType\\\":\\\"320\\\",\\\"marketingAirline\\\":\\\"QR\\\",\\\"marriageGroup\\\":\\\"O\\\",\\\"mileage\\\":1999,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"NLUSN1RO\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"allowPieces\\\":2}},\\\"recheckBaggage\\\":false},{\\\"segmentId\\\":3,\\\"departureTime\\\":\\\"2019-11-22 19:45\\\",\\\"arrivalTime\\\":\\\"2019-11-22 20:50\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"298\\\",\\\"bookingClass\\\":\\\"T\\\",\\\"duration\\\":65,\\\"departureAirportCode\\\":\\\"SOF\\\",\\\"departureAirportTerminal\\\":\\\"2\\\",\\\"arrivalAirportCode\\\":\\\"OTP\\\",\\\"arrivalAirportTerminal\\\":\\\"\\\",\\\"operatingAirline\\\":\\\"RO\\\",\\\"airEquipType\\\":\\\"AT7\\\",\\\"marketingAirline\\\":\\\"RO\\\",\\\"marriageGroup\\\":\\\"I\\\",\\\"mileage\\\":185,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"TOWSVR\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"allowPieces\\\":1}},\\\"recheckBaggage\\\":true},{\\\"segmentId\\\":4,\\\"departureTime\\\":\\\"2019-11-23 08:35\\\",\\\"arrivalTime\\\":\\\"2019-11-23 10:15\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"301\\\",\\\"bookingClass\\\":\\\"T\\\",\\\"duration\\\":160,\\\"departureAirportCode\\\":\\\"OTP\\\",\\\"departureAirportTerminal\\\":\\\"\\\",\\\"arrivalAirportCode\\\":\\\"FRA\\\",\\\"arrivalAirportTerminal\\\":\\\"2\\\",\\\"operatingAirline\\\":\\\"RO\\\",\\\"airEquipType\\\":\\\"73W\\\",\\\"marketingAirline\\\":\\\"RO\\\",\\\"marriageGroup\\\":\\\"O\\\",\\\"mileage\\\":903,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"TOWSVR\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"allowPieces\\\":1}},\\\"recheckBaggage\\\":false}],\\\"duration\\\":2550}],\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"RO\\\",\\\"gds\\\":\\\"G\\\",\\\"pcc\\\":\\\"NA\\\",\\\"cons\\\":\\\"GIS\\\",\\\"fareType\\\":\\\"NA\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"currencies\\\":[\\\"USD\\\"],\\\"currencyRates\\\":{\\\"USDUSD\\\":{\\\"from\\\":\\\"USD\\\",\\\"to\\\":\\\"USD\\\",\\\"rate\\\":1}},\\\"tickets\\\":[{\\\"key\\\":\\\"02_QVdBUlFBKlkxMDAwL0pGS1NPRjIwMTktMTEtMjEqUVJ+I1FSNzA0I1FSMjI3\\\",\\\"routingId\\\":0,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-11-21\\\",\\\"totalPrice\\\":388.8,\\\"totalTax\\\":267.8,\\\"markup\\\":50,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"JWZ\\\",\\\"cnt\\\":1,\\\"price\\\":388.8,\\\"tax\\\":267.8,\\\"baseFare\\\":121,\\\"pubBaseFare\\\":121,\\\"baseTax\\\":217.8,\\\"markup\\\":50,\\\"refundPenalty\\\":\\\"Amount: USD375.00 \\\",\\\"changePenalty\\\":\\\"Amount: USD260.00\\\",\\\"endorsementPenalty\\\":\\\"\\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":121,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":217.8,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}}},\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"QR\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"fareType\\\":\\\"SR\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segmentIds\\\":[1,2]}]},{\\\"key\\\":\\\"01_QVdBUlFBKlkxMDAwL1NPRkZSQTIwMTktMTEtMjIqUk9+I1JPMjk4I1JPMzAx\\\",\\\"routingId\\\":0,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-10-19\\\",\\\"totalPrice\\\":265.6,\\\"totalTax\\\":83.6,\\\"markup\\\":50,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"ADT\\\",\\\"cnt\\\":1,\\\"price\\\":265.6,\\\"tax\\\":83.6,\\\"baseFare\\\":182,\\\"pubBaseFare\\\":182,\\\"baseTax\\\":33.6,\\\"markup\\\":50,\\\"refundPenalty\\\":\\\"Percentage: 100.00%\\\",\\\"changePenalty\\\":\\\"Percentage: 100.00%\\\",\\\"endorsementPenalty\\\":\\\"\\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":182,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":33.6,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}}},\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"RO\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"fareType\\\":\\\"PUB\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segmentIds\\\":[3,4]}]}]}\"\n    },\n    },\n    \"action\": \"v2/quote/get-info\",\n    \"response_id\": 298939,\n    \"request_dt\": \"2019-04-25 13:12:44\",\n    \"response_dt\": \"2019-04-25 13:12:44\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "UserNotFound",
            "description": "<p>The id of the User was not found.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 404 Not Found\n{\n    \"name\": \"Not Found\",\n    \"message\": \"Not found Quote UID: 30\",\n    \"code\": 2,\n    \"status\": 404,\n    \"type\": \"yii\\\\web\\\\NotFoundHttpException\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/QuoteController.php",
    "groupTitle": "Quotes"
  },
  {
    "type": "post",
    "url": "/v1/quote/get-info",
    "title": "Get Quote",
    "version": "0.1.0",
    "name": "GetQuote",
    "group": "Quotes",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        }
      ]
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "size": "13",
            "optional": false,
            "field": "uid",
            "description": "<p>Quote UID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "apiKey",
            "description": "<p>API Key for Project (if not use Basic-Authorization)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "clientIP",
            "description": "<p>Client IP address</p>"
          },
          {
            "group": "Parameter",
            "type": "bool",
            "optional": true,
            "field": "clientUseProxy",
            "description": "<p>Client Use Proxy</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "clientUserAgent",
            "description": "<p>Client User Agent</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n     \"uid\": \"5b6d03d61f078\",\n     \"apiKey\": \"d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "status",
            "description": "<p>Status</p>"
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "itinerary",
            "description": "<p>Itinerary List</p>"
          },
          {
            "group": "Success 200",
            "type": "array",
            "optional": false,
            "field": "errors",
            "description": "<p>Errors</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "uid",
            "description": "<p>Quote UID</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "agentName",
            "description": "<p>Agent Name</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "agentEmail",
            "description": "<p>Agent Email</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "agentDirectLine",
            "description": "<p>Agent DirectLine</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "action",
            "description": "<p>Action</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "response_id",
            "description": "<p>Response Id</p>"
          },
          {
            "group": "Success 200",
            "type": "DateTime",
            "optional": false,
            "field": "request_dt",
            "description": "<p>Request Date &amp; Time</p>"
          },
          {
            "group": "Success 200",
            "type": "DateTime",
            "optional": false,
            "field": "response_dt",
            "description": "<p>Response Date &amp; Time</p> <p>&quot;errors&quot;: [], &quot;uid&quot;: &quot;5b7424e858e91&quot;, &quot;agentName&quot;: &quot;admin&quot;, &quot;agentEmail&quot;: &quot;assistant@wowfare.com&quot;, &quot;agentDirectLine&quot;: &quot;+1 888 946 3882&quot;, &quot;action&quot;: &quot;v1/quote/get-info&quot;,</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n\n\n{\n\"status\": \"Success\",\n\"itinerary\": {\n\"tripType\": \"OW\",\n\"mainCarrier\": \"WOW air\",\n\"trips\": [\n{\n\"segments\": [\n{\n\"carrier\": \"WW\",\n\"airlineName\": \"WOW air\",\n\"departureAirport\": \"BOS\",\n\"arrivalAirport\": \"KEF\",\n\"departureDateTime\": {\n\"date\": \"2018-09-19 19:00:00.000000\",\n\"timezone_type\": 3,\n\"timezone\": \"UTC\"\n},\n\"arrivalDateTime\": {\n\"date\": \"2018-09-20 04:30:00.000000\",\n\"timezone_type\": 3,\n\"timezone\": \"UTC\"\n},\n\"flightNumber\": \"126\",\n\"bookingClass\": \"O\",\n\"departureCity\": \"Boston\",\n\"arrivalCity\": \"Reykjavik\",\n\"flightDuration\": 330,\n\"layoverDuration\": 0,\n\"cabin\": \"E\",\n\"departureCountry\": \"United States\",\n\"arrivalCountry\": \"Iceland\"\n},\n{\n\"carrier\": \"WW\",\n\"airlineName\": \"WOW air\",\n\"departureAirport\": \"KEF\",\n\"arrivalAirport\": \"LGW\",\n\"departureDateTime\": {\n\"date\": \"2018-09-20 15:30:00.000000\",\n\"timezone_type\": 3,\n\"timezone\": \"UTC\"\n},\n\"arrivalDateTime\": {\n\"date\": \"2018-09-20 19:50:00.000000\",\n\"timezone_type\": 3,\n\"timezone\": \"UTC\"\n},\n\"flightNumber\": \"814\",\n\"bookingClass\": \"N\",\n\"departureCity\": \"Reykjavik\",\n\"arrivalCity\": \"London\",\n\"flightDuration\": 200,\n\"layoverDuration\": 660,\n\"cabin\": \"E\",\n\"departureCountry\": \"Iceland\",\n\"arrivalCountry\": \"United Kingdom\"\n}\n],\n\"totalDuration\": 1190,\n\"routing\": \"BOS-KEF-LGW\",\n\"title\": \"Boston - London\"\n}\n],\n\"price\": {\n\"detail\": {\n\"ADT\": {\n\"selling\": 350.2,\n\"fare\": 237,\n\"taxes\": 113.2,\n\"tickets\": 1\n}\n},\n\"tickets\": 1,\n\"selling\": 350.2,\n\"amountPerPax\": 350.2,\n\"fare\": 237,\n\"mark_up\": 0,\n\"taxes\": 113.2,\n\"currency\": \"USD\",\n\"isCC\": false\n}\n},\n\"errors\": [],\n\"uid\": \"5b7424e858e91\",\n\"lead_id\": 123456,\n\"lead_uid\": \"00jhk0017\",\n\"lead_delayed_charge\": 0,\n\"lead_status\": \"sold\",\n\"booked_quote_uid\": \"5b8ddfc56a15c\",\n\"agentName\": \"admin\",\n\"agentEmail\": \"assistant@wowfare.com\",\n\"agentDirectLine\": \"+1 888 946 3882\",\n\"action\": \"v1/quote/get-info\",\n\"response_id\": 173,\n\"request_dt\": \"2018-08-16 06:42:03\",\n\"response_dt\": \"2018-08-16 06:42:03\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "UserNotFound",
            "description": "<p>The id of the User was not found.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 404 Not Found\n{\n    \"name\": \"Not Found\",\n    \"message\": \"Not found Quote UID: 30\",\n    \"code\": 2,\n    \"status\": 404,\n    \"type\": \"yii\\\\web\\\\NotFoundHttpException\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/QuoteController.php",
    "groupTitle": "Quotes"
  }
] });
