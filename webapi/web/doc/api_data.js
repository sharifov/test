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
    "type": "get",
    "url": "/v2/case-category/list",
    "title": "Get CaseCategory",
    "version": "0.2.0",
    "name": "CaseCategoryList",
    "group": "CaseCategory",
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
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Accept-Encoding",
            "description": ""
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "If-Modified-Since",
            "description": "<p>Format <code> day-name, day month year hour:minute:second GMT</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        },
        {
          "title": "Header-Example (If-Modified-Since):",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\",\n    \"If-Modified-Since\": \"Mon, 23 Dec 2019 08:17:54 GMT\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n     \"status\": 200,\n     \"message\": \"OK\",\n     \"data\": {\n         \"case-category\": [\n             {\n                 \"cc_id\": 1,\n                 \"cc_key\": \"add_infant\",\n                 \"cc_name\": \"Add infant\",\n                 \"cc_dep_id\": 3,\n                 \"cc_updated_dt\": null\n             },\n             {\n                 \"cc_id\": 2,\n                 \"cc_key\": null,\n                 \"cc_name\": \"Insurance Add/Remove\",\n                 \"cc_dep_id\": 3,\n                 \"cc_updated_dt\": \"2019-09-26 15:14:01\"\n             }\n         ]\n     },\n     \"technical\": {\n         \"action\": \"v2/case-category/list\",\n         \"response_id\": 11926631,\n         \"request_dt\": \"2020-03-16 11:26:34\",\n         \"response_dt\": \"2020-03-16 11:26:34\",\n         \"execution_time\": 0.076,\n         \"memory_usage\": 506728\n     },\n     \"request\": []\n }",
          "type": "json"
        },
        {
          "title": "Not Modified-Response (304):",
          "content": "\nHTTP/1.1 304 Not Modified\nCache-Control: public, max-age=3600\nLast-Modified: Mon, 23 Dec 2019 08:17:53 GMT",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (405):",
          "content": "\nHTTP/1.1 405 Method Not Allowed\n  {\n      \"name\": \"Method Not Allowed\",\n      \"message\": \"Method Not Allowed. This URL can only handle the following request methods: GET.\",\n      \"code\": 0,\n      \"status\": 405,\n      \"type\": \"yii\\\\web\\\\MethodNotAllowedHttpException\"\n  }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/CaseCategoryController.php",
    "groupTitle": "CaseCategory"
  },
  {
    "type": "post",
    "url": "/v2/cases/create",
    "title": "Create Case",
    "version": "0.2.0",
    "name": "CreateCase",
    "group": "Cases",
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
            "size": "160",
            "optional": false,
            "field": "contact_email",
            "description": "<p>Client Email</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "20",
            "optional": false,
            "field": "contact_phone",
            "description": "<p>Client Phone</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "category_id",
            "description": "<p>Case category id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "5..7",
            "optional": false,
            "field": "order_uid",
            "description": "<p>Order uid (symbols and numbers only)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": true,
            "field": "project_key",
            "description": "<p>Project Key (if not exist project assign API User)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "255",
            "optional": true,
            "field": "subject",
            "description": "<p>Subject</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "65000",
            "optional": true,
            "field": "description",
            "description": "<p>Description</p>"
          },
          {
            "group": "Parameter",
            "type": "array[]",
            "optional": true,
            "field": "order_info",
            "description": "<p>Order Info (key =&gt; value, key: string, value: string)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n      \"contact_email\": \"test@test.com\",\n      \"contact_phone\": \"+37369636690\",\n      \"category_id\": 12,\n      \"order_uid\": \"12WS09W\",\n      \"subject\": \"Subject text\",\n      \"description\": \"Description text\",\n      \"project_key\": \"project_key\",\n      \"order_info\": {\n          \"Departure Date\":\"2020-03-07\",\n          \"Departure Airport\":\"LON\"\n      }\n  }",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n  {\n      \"status\": 200,\n      \"message\": \"OK\",\n      \"data\": {\n          \"case_gid\": \"708ddf3e44ec477f8807d8b5f748bb6c\",\n          \"client_uuid\": \"5d0cd25a-7f22-4b18-9547-e19a3e7d0c9a\"\n      },\n      \"technical\": {\n          \"action\": \"v2/cases/create\",\n          \"response_id\": 11934216,\n          \"request_dt\": \"2020-03-17 08:31:30\",\n          \"response_dt\": \"2020-03-17 08:31:30\",\n          \"execution_time\": 0.156,\n          \"memory_usage\": 979248\n      },\n      \"request\": {\n          \"contact_email\": \"test@test.com\",\n          \"contact_phone\": \"+37369636690\",\n          \"category_id\": 12,\n          \"order_uid\": \"12WS09W\",\n          \"subject\": \"Subject text\",\n          \"description\": \"Description text\",\n          \"project_key\": \"project_key\",\n          \"order_info\": {\n              \"Departure Date\": \"2020-03-07\",\n              \"Departure Airport\": \"LON\"\n          }\n      }\n  }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response(Validation error) (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n  {\n      \"status\": 422,\n      \"message\": \"Validation error\",\n      \"errors\": {\n          \"contact_email\": [\n              \"Contact Email cannot be blank.\"\n          ],\n          \"contact_phone\": [\n              \"The format of Contact Phone is invalid.\"\n          ],\n          \"order_uid\": [\n              \"Order Uid should contain at most 7 characters.\"\n          ]\n      },\n      \"code\": \"21301\",\n      \"technical\": {\n         ...\n      },\n      \"request\": {\n         ...\n      }\n  }",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n      \"status\": 422,\n      \"message\": \"Saving error\",\n      \"errors\": [\n          \"Saving error\"\n      ],\n      \"code\": 21101,\n      \"technical\": {\n          ...\n      },\n      \"request\": {\n          ...\n      }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response(Load data error) (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n{\n      \"status\": 400,\n      \"message\": \"Load data error\",\n      \"errors\": [\n          \"Not found Case data on POST request\"\n      ],\n      \"code\": 21300,\n      \"technical\": {\n          ...\n      },\n      \"request\": {\n          ...\n      }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/CasesController.php",
    "groupTitle": "Cases"
  },
  {
    "type": "post",
    "url": "/v2/client-account/create",
    "title": "Create Client Account",
    "version": "0.2.0",
    "name": "CreateClientAccount",
    "group": "ClientAccount",
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
            "size": "36",
            "optional": true,
            "field": "uuid",
            "description": "<p>Client Uuid</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "hid",
            "description": "<p>Origin Id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": false,
            "field": "username",
            "description": "<p>Username</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": true,
            "field": "first_name",
            "description": "<p>First name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": true,
            "field": "middle_name",
            "description": "<p>Middle name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "2",
            "optional": true,
            "field": "nationality_country_code",
            "description": "<p>Nationality country code</p>"
          },
          {
            "group": "Parameter",
            "type": "datetime",
            "size": "YYYY-MM-DD",
            "optional": true,
            "field": "dob",
            "description": "<p>Dob</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "1..2",
            "optional": true,
            "field": "gender",
            "description": "<p>Gender</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": true,
            "field": "phone",
            "description": "<p>Phone</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "0..1",
            "optional": true,
            "field": "subscription",
            "description": "<p>Subscription</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "5",
            "optional": true,
            "field": "language_id",
            "description": "<p>Language</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3",
            "optional": true,
            "field": "currency_code",
            "description": "<p>Currency code</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": true,
            "field": "timezone",
            "description": "<p>Timezone</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "40",
            "optional": true,
            "field": "created_ip",
            "description": "<p>Created ip</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "0..1",
            "optional": true,
            "field": "enabled",
            "description": "<p>Enabled</p>"
          },
          {
            "group": "Parameter",
            "type": "datetime",
            "size": "YYYY-MM-DD HH:II:SS",
            "optional": true,
            "field": "origin_created_dt",
            "description": "<p>Origin Created dt</p>"
          },
          {
            "group": "Parameter",
            "type": "datetime",
            "size": "YYYY-MM-DD HH:II:SS",
            "optional": true,
            "field": "origin_updated_dt",
            "description": "<p>Origin Updated dt</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n         \"uuid\": \"f04f9609-31e1-4dba-bffd-a689d4391fef\",\n         \"hid\": 2,\n         \"username\": \"example\",\n         \"first_name\": \"\",\n         \"middle_name\": \"\",\n         \"last_name\": \"\",\n         \"nationality_country_code\": \"\",\n         \"dob\": \"2001-09-09\",\n         \"gender\": 1,\n         \"phone\": \"\",\n         \"subscription\": 1,\n         \"language_id\": \"en-PI\",\n         \"currency_code\": \"EUR\",\n         \"timezone\": \"\",\n         \"created_ip\": \"127.0.0.1\",\n         \"enabled\": 1,\n         \"origin_created_dt\": \"2020-11-19 10:45:17\",\n         \"origin_updated_dt\": \"2020-11-04 05:25:18\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n  {\n      \"status\": 200,\n      \"message\": \"OK\",\n      \"data\": {\n         \"uuid\": \"f04f9609-31e1-4dba-bffd-a689d4391fef\"\n      },\n      \"technical\": {\n          \"action\": \"/v2/client-account/create\",\n          \"response_id\": 11934216,\n          \"request_dt\": \"2020-03-17 08:31:30\",\n          \"response_dt\": \"2020-03-17 08:31:30\",\n          \"execution_time\": 0.156,\n          \"memory_usage\": 979248\n      },\n      \"request\": {\n          \"uuid\": \"f04f9609-31e1-4dba-bffd-a689d4391fef\"\n      }\n  }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response(Validation error) (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n  {\n      \"status\": 422,\n      \"message\": \"Validation error\",\n      \"errors\": {\n          ...\n      },\n      \"code\": \"21301\",\n      \"technical\": {\n         ...\n      },\n      \"request\": {\n         ...\n      }\n  }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/ClientAccountController.php",
    "groupTitle": "ClientAccount"
  },
  {
    "type": "post",
    "url": "/v2/client-account/get",
    "title": "Get Client Account",
    "version": "0.2.0",
    "name": "GetClientAccount",
    "group": "ClientAccount",
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
            "size": "36",
            "optional": false,
            "field": "uuid",
            "description": "<p>Client Uuid</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n     \"uuid\": \"f04f9609-31e1-4dba-bffd-a689d4391fef\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n  {\n      \"status\": 200,\n      \"message\": \"OK\",\n      \"data\": {\n         \"ca_uuid\": \"f04f9609-31e1-4dba-bffd-a689d4391fef\",\n         \"ca_hid\": 2,\n         \"ca_username\": \"example\",\n         \"ca_first_name\": \"\",\n         \"ca_middle_name\": \"\",\n         \"ca_last_name\": \"\",\n         \"ca_nationality_country_code\": \"\",\n         \"ca_dob\": \"2001-09-09\",\n         \"ca_gender\": 1,\n         \"ca_phone\": \"\",\n         \"ca_subscription\": 1,\n         \"ca_language_id\": \"en-PI\",\n         \"ca_currency_code\": \"EUR\",\n         \"ca_timezone\": \"\",\n         \"ca_created_ip\": \"\",\n         \"ca_enabled\": 1,\n         \"ca_origin_created_dt\": \"2020-11-19 10:45:17\",\n         \"ca_origin_updated_dt\": \"2020-11-04 05:25:18\"\n      },\n      \"technical\": {\n          \"action\": \"/v2/client-account/get\",\n          \"response_id\": 11934216,\n          \"request_dt\": \"2020-03-17 08:31:30\",\n          \"response_dt\": \"2020-03-17 08:31:30\",\n          \"execution_time\": 0.156,\n          \"memory_usage\": 979248\n      },\n      \"request\": {\n          \"uuid\": \"f04f9609-31e1-4dba-bffd-a689d4391fef\"\n      }\n  }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response(Validation error) (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n  {\n      \"status\": 422,\n      \"message\": \"Validation error\",\n      \"errors\": {\n          ...\n      },\n      \"code\": \"21301\",\n      \"technical\": {\n         ...\n      },\n      \"request\": {\n         ...\n      }\n  }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/ClientAccountController.php",
    "groupTitle": "ClientAccount"
  },
  {
    "type": "post",
    "url": "/v2/client-account/update",
    "title": "Update Client Account",
    "version": "0.2.0",
    "name": "UpdateClientAccount",
    "group": "ClientAccount",
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
            "size": "36",
            "optional": false,
            "field": "uuid",
            "description": "<p>Client Uuid</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "hid",
            "description": "<p>Origin Id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": true,
            "field": "username",
            "description": "<p>Username</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": true,
            "field": "first_name",
            "description": "<p>First name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": true,
            "field": "middle_name",
            "description": "<p>Middle name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "2",
            "optional": true,
            "field": "nationality_country_code",
            "description": "<p>Nationality country code</p>"
          },
          {
            "group": "Parameter",
            "type": "datetime",
            "size": "YYYY-MM-DD",
            "optional": true,
            "field": "dob",
            "description": "<p>Dob</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "1..2",
            "optional": true,
            "field": "gender",
            "description": "<p>Gender</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": true,
            "field": "phone",
            "description": "<p>Phone</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "0..1",
            "optional": true,
            "field": "subscription",
            "description": "<p>Subscription</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "5",
            "optional": true,
            "field": "language_id",
            "description": "<p>Language</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3",
            "optional": true,
            "field": "currency_code",
            "description": "<p>Currency code</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": true,
            "field": "timezone",
            "description": "<p>Timezone</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "40",
            "optional": true,
            "field": "created_ip",
            "description": "<p>Created ip</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "0..1",
            "optional": true,
            "field": "enabled",
            "description": "<p>Enabled</p>"
          },
          {
            "group": "Parameter",
            "type": "datetime",
            "size": "YYYY-MM-DD HH:II:SS",
            "optional": true,
            "field": "origin_created_dt",
            "description": "<p>Origin Created dt</p>"
          },
          {
            "group": "Parameter",
            "type": "datetime",
            "size": "YYYY-MM-DD HH:II:SS",
            "optional": true,
            "field": "origin_updated_dt",
            "description": "<p>Origin Updated dt</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n         \"uuid\": \"f04f9609-31e1-4dba-bffd-a689d4391fef\",\n         \"hid\": 2,\n         \"username\": \"example\",\n         \"first_name\": \"\",\n         \"middle_name\": \"\",\n         \"last_name\": \"\",\n         \"nationality_country_code\": \"\",\n         \"dob\": \"2001-09-09\",\n         \"gender\": 1,\n         \"phone\": \"\",\n         \"subscription\": 1,\n         \"language_id\": \"en-PI\",\n         \"currency_code\": \"EUR\",\n         \"timezone\": \"\",\n         \"created_ip\": \"127.0.0.1\",\n         \"enabled\": 1,\n         \"origin_created_dt\": \"2020-11-19 10:45:17\",\n         \"origin_updated_dt\": \"2020-11-04 05:25:18\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n  {\n      \"status\": 200,\n      \"message\": \"OK\",\n      \"data\": {\n         \"ClientAccount updated successfully\": 123\n      },\n      \"technical\": {\n          \"action\": \"/v2/client-account/update\",\n          \"response_id\": 11934216,\n          \"request_dt\": \"2020-03-17 08:31:30\",\n          \"response_dt\": \"2020-03-17 08:31:30\",\n          \"execution_time\": 0.156,\n          \"memory_usage\": 979248\n      },\n      \"request\": {\n          \"uuid\": \"f04f9609-31e1-4dba-bffd-a689d4391fef\"\n      }\n  }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response(Validation error) (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n  {\n      \"status\": 422,\n      \"message\": \"Validation error\",\n      \"errors\": {\n          ...\n      },\n      \"code\": \"21301\",\n      \"technical\": {\n         ...\n      },\n      \"request\": {\n         ...\n      }\n  }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/ClientAccountController.php",
    "groupTitle": "ClientAccount"
  },
  {
    "type": "post",
    "url": "/v1/client-chat-request/feedback",
    "title": "Client Chat Feedback",
    "version": "0.1.0",
    "name": "ClientChatFeedback",
    "group": "ClientChat",
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
      "examples": [
        {
          "title": "Request-Example LEAVE_FEEDBACK:",
          "content": "{\n     \"event\": \"LEAVE_FEEDBACK\",\n     \"data\": {\n         \"rid\": \"20a20989-4d26-42f4-9a1c-2948ce4c4d56\",\n         \"comment\": \"Hello, this is my feedback\",\n         \"rating\": 4,\n         \"visitor\": {\n             \"id\": \"1c1d90ff-5489-45f5-b19b-2181a65ce898\",\n             \"project\": \"ovago\"\n         }\n     }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n   \"status\": 200\n   \"message\": \"Ok\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n{\n \"status\":400,\n \"message\":\"Some errors occurred while creating client chat request\",\n \"code\":\"13104\",\n \"errors\":[\"Event is invalid.\"]\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/ClientChatRequestController.php",
    "groupTitle": "ClientChat"
  },
  {
    "type": "get",
    "url": "/v1/client-chat-request/chat-form",
    "title": "Client Chat Form",
    "version": "0.1.0",
    "name": "ClientChatForm",
    "group": "ClientChat",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": false,
            "field": "form_key",
            "description": "<p>Form Key</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "5",
            "optional": false,
            "field": "language_id",
            "description": "<p>Language ID (en-US)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "allowedValues": [
              "0",
              "1"
            ],
            "optional": true,
            "field": "cache",
            "description": "<p>Cache (not required, default eq 1)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n    \"form_key\": \"example_form\",\n    \"language_id\": \"ru-RU\",\n    \"cache\": 1\n}",
          "type": "get"
        }
      ]
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Accept-Encoding",
            "description": ""
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "If-Modified-Since",
            "description": "<p>Format <code> day-name, day month year hour:minute:second GMT</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        },
        {
          "title": "Header-Example (If-Modified-Since):",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\",\n    \"If-Modified-Since\": \"Mon, 23 Dec 2019 08:17:54 GMT\",\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n\"status\": 200,\n\"message\": \"OK\",\n\"data\": {\n        \"data_form\": [\n            {\n                \"type\": \"textarea\",\n                \"name\": \"example_name\",\n                \"className\": \"form-control\",\n                \"label\": \"Please, describe problem\",\n                \"required\": true,\n                \"rows\": 5\n            },\n            {\n                \"type\": \"select\",\n                \"name\": \"destination\",\n                \"className\": \"form-control\",\n                \"label\": \"Куда летим?\",\n                \"values\": [\n                    \"label\": \"Амстердам\",\n                    \"value\": \"AMS\",\n                    \"selected\": true\n                ],\n                [\n                    \"label\": \"Магадан\",\n                    \"value\": \"GDX\",\n                    \"selected\": false\n                ]\n            },\n            {\n                \"type\": \"button\",\n                \"name\": \"button-123\",\n                \"className\": \"btn-success btn\",\n                \"label\": \"Submit\"\n            }\n        ],\n        \"from_cache\" : true\n     }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n  {\n    \"status\": 400,\n    \"message\": \"Validate failed\",\n    \"code\": \"13110\",\n    \"errors\": []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/ClientChatRequestController.php",
    "groupTitle": "ClientChat"
  },
  {
    "type": "get",
    "url": "/v1/client-chat-request/project-config",
    "title": "Project Config",
    "version": "0.1.0",
    "name": "ClientChatProjectConfig",
    "group": "ClientChat",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "project_id",
            "description": "<p>Project ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": true,
            "field": "project_key",
            "description": "<p>Project Key (Priority)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "5",
            "optional": true,
            "field": "language_id",
            "description": "<p>Language ID (ru-RU)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "allowedValues": [
              "0",
              "1"
            ],
            "optional": true,
            "field": "nocache",
            "description": "<p>W/o cache</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n    \"project_id\": 1,\n    \"project_key\": \"ovago\",\n    \"language_id\": \"ru-RU\",\n    \"nocache\": 1\n}",
          "type": "get"
        }
      ]
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Credentials <code>base64_encode(Username:Password)</code></p>"
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Accept-Encoding",
            "description": ""
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "If-Modified-Since",
            "description": "<p>Format <code> day-name, day month year hour:minute:second GMT</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        },
        {
          "title": "Header-Example (If-Modified-Since):",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\",\n    \"If-Modified-Since\": \"Mon, 23 Dec 2019 08:17:54 GMT\",\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n\n{\n\"status\": 200,\n\"message\": \"OK\",\n\"data\": {\n\"endpoint\": \"chatbot.travel-dev.com\",\n\"enabled\": true,\n\"project\": \"WOWFARE\",\n\"projectKey\": \"wowfare\",\n\"notificationSound\": \"https://cdn.travelinsides.com/npmstatic/assets/chime.mp3\",\n\"theme\": {\n\"theme\": \"linear-gradient(270deg, #0AAB99 0%, #1E71D1 100%)\",\n\"primary\": \"#0C89DF\",\n\"primaryDark\": \"#0066BA\",\n\"accent\": \"#0C89DF\",\n\"accentDark\": \"#0066BA\"\n},\n\"settings\": {\n},\n\n\"channels\": [\n        {\n            \"id\": 2,\n            \"name\": \"Channel 2\",\n            \"priority\": 1,\n            \"default\": false,\n            \"enabled\": true,\n            \"settings\": {\n                \"max_dialog_count\": 4,\n                \"feedback_rating_enabled\": false,\n                \"feedback_message_enabled\": true,\n                \"history_email_enabled\": false,\n                \"history_download_enabled\": true\n            }\n        },\n        {\n            \"id\": 3,\n            \"name\": \"Channel 11\",\n            \"priority\": 2,\n            \"default\": true,\n            \"enabled\": true,\n            \"settings\": {\n                \"max_dialog_count\": 1,\n                \"feedback_rating_enabled\": true,\n                \"feedback_message_enabled\": true,\n                \"history_email_enabled\": true,\n                \"history_download_enabled\": true\n            }\n        }\n    ],\n    \"language_id\": \"ru-RU\",\n        \"translations\": {\n            \"connection_lost\": {\n                \"title\": \"Connection Lost\",\n                \"subtitle\": \"Trying to reconnect\"\n            },\n            \"waiting_for_response\": \"Waiting for response\",\n            \"waiting_for_agent\": \"Waiting for an agent\",\n            \"video_reply\": \"Video message\",\n            \"audio_reply\": \"Audio message\",\n            \"image_reply\": \"Image message\",\n            \"new_message\": \"New message\",\n            \"agent\": \"Agent\",\n            \"textarea_placeholder\": \"Type a message...\",\n            \"registration\": {\n                \"title\": \"Welcome\",\n                \"subtitle\": \"Be sure to leave a message\",\n                \"name\": \"Name\",\n                \"name_placeholder\": \"Your name\",\n                \"email\": \"Email\",\n                \"email_placeholder\": \"Your email\",\n                \"department\": \"Department\",\n                \"department_placeholder\": \"Choose a department\",\n                \"start_chat\": \"Start chat\"\n            },\n            \"conversations\": {\n                \"no_conversations\": \"No conversations yet\",\n                \"no_archived_conversations\": \"No archived conversations yet\",\n                \"history\": \"Conversation history\",\n                \"active\": \"Active\",\n                \"archived\": \"Archived Chats\",\n                \"start_new\": \"New Chat\"\n            },\n            \"file_upload\": {\n                \"file_too_big\": \"This file is too big. Max file size is {{size}}\",\n                \"file_too_big_alt\": \"No archived conversations yetThis file is too large\",\n                \"generic_error\": \"Failed to upload, please try again\",\n                \"not_allowed\": \"This file type is not supported\",\n                \"drop_file\": \"Drop file here to upload it\",\n                \"upload_progress\": \"Uploading file...\"\n            },\n            \"department\": {\n                \"sales\": \"Sales\",\n                \"support\": \"Support\",\n                \"exchange\": \"Exchange\"\n            }\n        },\n        \"cache\": true\n    }\n    }",
          "type": "json"
        },
        {
          "title": "Not Modified-Response (304):",
          "content": "\nHTTP/1.1 304 Not Modified\nCache-Control: public, max-age=3600\nLast-Modified: Mon, 23 Dec 2019 08:17:53 GMT",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n  {\n\"status\": 400,\n\"message\": \"Project Config not found\",\n\"code\": \"13108\",\n\"errors\": []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/ClientChatRequestController.php",
    "groupTitle": "ClientChat"
  },
  {
    "type": "post",
    "url": "/v1/client-chat-request/create",
    "title": "Client Chat Request",
    "version": "0.1.0",
    "name": "ClientChatRequest",
    "group": "ClientChat",
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
      "examples": [
        {
          "title": "Request-Example ROOM_CONNECTED:",
          "content": "{\n            \"event\": \"ROOM_CONNECTED\",\n            \"data\": {\n                \"rid\": \"d83ef2d3-30bf-4636-a2c6-7f5b4b0e81a4\",\n                \"geo\": {\n                    \"ip\": \"92.115.180.30\",\n                    \"version\": \"IPv4\",\n                    \"city\": \"Chisinau\",\n                    \"region\": \"Chi\\u0219in\\u0103u Municipality\",\n                    \"region_code\": \"CU\",\n                    \"country\": \"MD\",\n                    \"country_name\": \"Republic of Moldova\",\n                    \"country_code\": \"MD\",\n                    \"country_code_iso3\": \"MDA\",\n                    \"country_capital\": \"Chisinau\",\n                    \"country_tld\": \".md\",\n                    \"continent_code\": \"EU\",\n                    \"in_eu\": false,\n                    \"postal\": \"MD-2000\",\n                    \"latitude\": 47.0056,\n                    \"longitude\": 28.8575,\n                    \"timezone\": \"Europe\\/Chisinau\",\n                    \"utc_offset\": \"+0300\",\n                    \"country_calling_code\": \"+373\",\n                    \"currency\": \"MDL\",\n                    \"currency_name\": \"Leu\",\n                    \"languages\": \"ro,ru,gag,tr\",\n                    \"country_area\": 33843,\n                    \"country_population\": 3545883,\n                    \"asn\": \"AS8926\",\n                    \"org\": \"Moldtelecom SA\"\n                },\n                \"visitor\": {\n                    \"conversations\": 0,\n                    \"lastAgentMessage\": null,\n                    \"lastVisitorMessage\": null,\n                    \"id\": \"fef46d63-8a30-4eec-89eb-62f1bfc0ffcd\",\n                    \"uuid\": \"54d87707-bb54-46e3-9eca-8f776c7bcacf\",\n                    \"project\": \"ovago\"\n                },\n                \"sources\": [],\n                \"page\": {\n                    \"url\": \"https:\\/\\/dev-ovago.travel-dev.com\\/search\\/WAS-FRA%2F2021-03-22%2F2021-03-28\",\n                    \"title\": \"Air Ticket Booking - Find Cheap Flights and Airfare Deals - Ovago.com\",\n                    \"referrer\": \"https:\\/\\/dev-ovago.travel-dev.com\\/search\\/WAS-FRA%2F2021-03-22%2F2021-03-28\"\n                },\n                \"system\": {\n                    \"user_agent\": \"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/85.0.4183.102 Safari\\/537.36\",\n                    \"language\": \"en-US\",\n                    \"resolution\": \"1920x1080\"\n                },\n                \"custom\": {\n                    \"event\": {\n                        \"eventName\": \"UPDATE\",\n                        \"eventProps\": []\n                    }\n                }\n            }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n   \"status\": 200\n   \"message\": \"Ok\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n{\n \"status\":400,\n \"message\":\"Some errors occurred while creating client chat request\",\n \"code\":\"13104\",\n \"errors\":[\"Event is invalid.\"]\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/ClientChatRequestController.php",
    "groupTitle": "ClientChat"
  },
  {
    "type": "post",
    "url": "/v1/client-chat-request/create-message",
    "title": "Create Message",
    "version": "0.1.0",
    "name": "ClientChatRequestCreateMessage",
    "group": "ClientChat",
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
      "examples": [
        {
          "title": "Request-Example AGENT_UTTERED:",
          "content": "{\n            \"event\": \"AGENT_UTTERED\",\n            \"data\": {\n                \"id\": \"G6CBYkRYBotjaPPSu\",\n                \"rid\": \"e19bf809-12c9-4981-89d0-da2f5d071890\",\n                \"token\": \"56976e05-1916-44fb-a074-5a8d0358019b\",\n                \"visitor\": {\n                    \"conversations\": 0,\n                    \"lastAgentMessage\": null,\n                    \"lastVisitorMessage\": null,\n                    \"id\": \"56976e05-1916-44fb-a074-5a8d0358019b\",\n                    \"username\": \"guest-1219\",\n                    \"phone\": null,\n                    \"token\": \"56976e05-1916-44fb-a074-5a8d0358019b\"\n                },\n                \"agent\": {\n                    \"name\": \"vadim_larsen_admin\",\n                    \"username\": \"vadim_larsen_admin\",\n                    \"email\": \"vadim.larsen@techork.com\"\n                },\n                \"msg\": \"test\",\n                \"timestamp\": 1602587182948,\n                \"u\": {\n                    \"_id\": \"MszwfgYRGB9Tpw5Et\",\n                    \"username\": \"vadim.larsen\"\n                },\n                    \"agentId\": \"MszwfgYRGB9Tpw5Et\"\n                }\n}",
          "type": "json"
        },
        {
          "title": "Request-Example GUEST_UTTERED with Attachment:",
          "content": "{\n            \"event\": \"GUEST_UTTERED\",\n            \"data\": {\n                \"id\": \"93ea7e9d-04cc-4f96-8bbf-d8b646113fd7\",\n                \"rid\": \"88c395e3-fe19-4fe2-99dc-b0a1874efbdd\",\n                \"token\": \"9728d3b4-5754-4339-9b0f-1c75edc727e9\",\n                \"visitor\": {\n                    \"conversations\": 0,\n                    \"lastAgentMessage\": null,\n                    \"lastVisitorMessage\": null,\n                    \"id\": \"9728d3b4-5754-4339-9b0f-1c75edc727e9\",\n                    \"name\": \"Henry Fonda\",\n                    \"username\": \"guest-1220\",\n                    \"phone\": null,\n                    \"token\": \"9728d3b4-5754-4339-9b0f-1c75edc727e9\"\n                },\n                \"agent\": {\n                    \"name\": \"bot\",\n                    \"username\": \"bot\",\n                    \"email\": \"bot@techork.com\"\n                },\n                \"msg\": \"Hi\",\n                \"timestamp\": 1602588445024,\n                \"u\": {\n                    \"_id\": \"cYNGwXX6L8cN3eb2Q\",\n                    \"username\": \"guest-1220\",\n                    \"name\": \"Henry Fonda\"\n                }\n            }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n   \"status\": 200\n   \"message\": \"Ok\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n{\n \"status\":400,\n \"message\":\"Some errors occurred while creating client chat request\",\n \"code\":\"13104\",\n \"errors\":[\"Event is invalid.\"]\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/ClientChatRequestController.php",
    "groupTitle": "ClientChat"
  },
  {
    "type": "get",
    "url": "/v1/client/info",
    "title": "Client Info",
    "version": "0.1.0",
    "name": "Client",
    "group": "Client",
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
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Accept-Encoding",
            "description": ""
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
            "optional": false,
            "field": "client_uuid",
            "description": "<p>Client UUID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "project_key",
            "description": "<p>Project key</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n{\n     \"client_uuid\": \"af5241f1-094f-4fde-ada3-bd72986216f0\",\n     \"project_key\": \"ovago\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n     \"status\": 200,\n     \"message\": \"OK\",\n     \"data\": {\n         \"first_name\": \"Client first name\",\n         \"last_name\": \"Client last name\",\n         \"created\": \"2020-09-24 11:29:15\"\n     }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 200 OK\n\n{\n    \"status\": 422,\n    \"message\": \"Validation error\",\n    \"errors\": {\n        \"client_uuid\": [\n            \"Client Uuid cannot be blank.\"\n       ],\n       \"project_key\": [\n            \"Project Key is invalid.\"\n        ]\n    },\n    \"code\": \"11602\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 200 OK\n\n{\n    \"status\": 400,\n    \"message\": \"Load data error\",\n    \"errors\": {\n         \"Not found Client data on request\"\n    },\n    \"code\": \"11601\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (404):",
          "content": "\nHTTP/1.1 200 OK\n\n{\n    \"status\": 404,\n    \"message\": \"Client not found\",\n    \"code\": \"11100\",\n    \"errors\": []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/ClientController.php",
    "groupTitle": "Client"
  },
  {
    "type": "post",
    "url": "/v2/client-email/subscribe",
    "title": "Client Email Subscribe",
    "version": "0.2.0",
    "name": "Client_Email_Subscribe",
    "group": "ClientEmail",
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
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Accept-Encoding",
            "description": ""
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "If-Modified-Since",
            "description": "<p>Format <code> day-name, day month year hour:minute:second GMT</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        },
        {
          "title": "Header-Example (If-Modified-Since):",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\",\n    \"If-Modified-Since\": \"Mon, 23 Dec 2019 08:17:54 GMT\"\n}",
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
            "size": "160",
            "optional": false,
            "field": "email",
            "description": "<p>Email</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n     \"status\": 200,\n     \"message\": \"OK\",\n     \"data\": {\n         \"email\" : \"example@email.com\"\n     },\n     \"technical\": {\n         \"action\": \"v2/client-email/subscribe\",\n         \"response_id\": 11926631,\n         \"request_dt\": \"2020-03-16 11:26:34\",\n         \"response_dt\": \"2020-03-16 11:26:34\",\n         \"execution_time\": 0.076,\n         \"memory_usage\": 506728\n     },\n     \"request\": []\n }",
          "type": "json"
        },
        {
          "title": "Not Modified-Response (304):",
          "content": "\nHTTP/1.1 304 Not Modified\nCache-Control: public, max-age=3600\nLast-Modified: Mon, 23 Dec 2019 08:17:53 GMT",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (405):",
          "content": "\nHTTP/1.1 405 Method Not Allowed\n  {\n      \"name\": \"Method Not Allowed\",\n      \"message\": \"Method Not Allowed. This URL can only handle the following request methods: GET.\",\n      \"code\": 0,\n      \"status\": 405,\n      \"type\": \"yii\\\\web\\\\MethodNotAllowedHttpException\"\n  }",
          "type": "json"
        },
        {
          "title": "Error-Response(Validation error) (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n  {\n      \"status\": 422,\n      \"message\": \"Validation error\",\n      \"errors\": {\n          \"email\": [\n              \"Contact Email cannot be blank.\"\n          ]\n      },\n      \"code\": \"21301\",\n      \"technical\": {\n         ...\n      },\n      \"request\": {\n         ...\n      }\n  }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/ClientEmailController.php",
    "groupTitle": "ClientEmail"
  },
  {
    "type": "post",
    "url": "/v2/client-email/unsubscribe",
    "title": "Client Email Unsubscribe",
    "version": "0.2.0",
    "name": "Client_Email_Unsubscribe",
    "group": "ClientEmail",
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
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Accept-Encoding",
            "description": ""
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "If-Modified-Since",
            "description": "<p>Format <code> day-name, day month year hour:minute:second GMT</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        },
        {
          "title": "Header-Example (If-Modified-Since):",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\",\n    \"If-Modified-Since\": \"Mon, 23 Dec 2019 08:17:54 GMT\"\n}",
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
            "size": "160",
            "optional": false,
            "field": "email",
            "description": "<p>Email</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n     \"status\": 200,\n     \"message\": \"OK\",\n     \"data\": {\n         \"email\" : \"example@email.com\"\n     },\n     \"technical\": {\n         \"action\": \"v2/client-email/unsubscribe\",\n         \"response_id\": 11926631,\n         \"request_dt\": \"2020-03-16 11:26:34\",\n         \"response_dt\": \"2020-03-16 11:26:34\",\n         \"execution_time\": 0.076,\n         \"memory_usage\": 506728\n     },\n     \"request\": []\n }",
          "type": "json"
        },
        {
          "title": "Not Modified-Response (304):",
          "content": "\nHTTP/1.1 304 Not Modified\nCache-Control: public, max-age=3600\nLast-Modified: Mon, 23 Dec 2019 08:17:53 GMT",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (405):",
          "content": "\nHTTP/1.1 405 Method Not Allowed\n  {\n      \"name\": \"Method Not Allowed\",\n      \"message\": \"Method Not Allowed. This URL can only handle the following request methods: GET.\",\n      \"code\": 0,\n      \"status\": 405,\n      \"type\": \"yii\\\\web\\\\MethodNotAllowedHttpException\"\n  }",
          "type": "json"
        },
        {
          "title": "Error-Response(Validation error) (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n  {\n      \"status\": 422,\n      \"message\": \"Validation error\",\n      \"errors\": {\n          \"email\": [\n              \"Contact Email cannot be blank.\"\n          ]\n      },\n      \"code\": \"21301\",\n      \"technical\": {\n         ...\n      },\n      \"request\": {\n         ...\n      }\n  }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/ClientEmailController.php",
    "groupTitle": "ClientEmail"
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
          "content": "\nHTTP/1.1 200 OK\n{\n       \"status\": 200\n       \"message\": \"OK\",\n       \"data\": {\n           \"phones\": [\n               {\n                   \"phone\": \"+15211111111\",\n                   \"cid\": \"WOWMAC\",\n                   \"department_id\": 1,\n                   \"department\": \"Sales\",\n                   \"language_id\": \"en-US\",\n                   \"updated_dt\": \"2019-01-08 11:44:57\"\n               },\n               {\n                   \"phone\": \"+15222222222\",\n                   \"cid\": \"WSUDCV\",\n                   \"department_id\": 3,\n                   \"department\": \"Support\",\n                   \"language_id\": \"fr-FR\",\n                   \"updated_dt\": \"2019-01-09 11:50:25\"\n              }\n           ]\n       },\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
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
    "url": "/v1/flight/fail",
    "title": "Flight Oder Fail",
    "version": "0.1.0",
    "name": "Flight_Oder_Fail",
    "group": "Flight",
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
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Accept-Encoding",
            "description": ""
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
            "size": "15",
            "optional": false,
            "field": "orderUid",
            "description": "<p>Order Uid</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": true,
            "field": "description",
            "description": "<p>Description</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n          \"orderUid\": \"or6061be5ec5c0e\",\n          \"description\": \"Example reason failing\"\n       }",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n     \"status\": 200,\n     \"message\": \"OK\",\n     \"data\": {\n         \"resultMessage\": \"Order Uid(or6061be5ec5c0e) successful failed\"\n     }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "HTTP/1.1 200 OK\n{\n    \"status\": 422,\n    \"message\": \"Validation error\",\n    \"errors\": {\n        \"orderUid\": [\n            \"orderUid cannot be blank\"\n       ]\n    },\n    \"code\": \"15801\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (404):",
          "content": "HTTP/1.1 200 OK\n{\n    \"status\": 404,\n    \"message\": \"Order not found\",\n    \"code\": \"15300\",\n    \"errors\": []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/FlightController.php",
    "groupTitle": "Flight"
  },
  {
    "type": "post",
    "url": "/v1/flight/replace",
    "title": "Flight Replace",
    "version": "0.1.0",
    "name": "Flight_Replace",
    "group": "Flight",
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
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Accept-Encoding",
            "description": ""
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
            "size": "15",
            "optional": false,
            "field": "fareId",
            "description": "<p>Fare Id (Order identity)</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "flights",
            "description": "<p>Flights data array</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": true,
            "field": "payments",
            "description": "<p>Payments data array</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": true,
            "field": "options",
            "description": "<p>Options data array</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n{\n          \"fareId\": \"or6061be5ec5c0e\",\n          \"parentBookingId\": \"OE96040\",\n          \"parentId\": 205975,\n          \"sameItinerary\": true,\n          \"flights\": [\n              {\n                  \"appKey\": \"038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826\",\n                  \"uniqueId\": \"OE96040\",\n                  \"status\": 6,\n                  \"pnr\": \"\",\n                  \"gds\": \"\",\n                  \"flightType\": \"RT\",\n                  \"validatingCarrier\": \"PR\",\n                  \"bookingInfo\": [\n                      {\n                          \"bookingId\": \"OE96040\",\n                          \"pnr\": \"Q3PM1G\",\n                          \"gds\": \"S\",\n                          \"validatingCarrier\": \"PR\",\n                          \"status\": 6,\n                          \"state\": \"Rejected\",\n                          \"passengers\": {\n                              \"1\": {\n                                  \"fullName\": \"Arthur Davis\",\n                                  \"first_name\": \"Arthur\",\n                                  \"middle_name\": \"\",\n                                  \"last_name\": \"Davis\",\n                                  \"birth_date\": \"1963-04-07\",\n                                  \"nationality\": \"US\",\n                                  \"gender\": \"M\",\n                                  \"aGender\": \"Mr.\",\n                                  \"tktNumber\": null,\n                                  \"paxType\": \"ADT\"\n                              }\n                          },\n                          \"airlinesCode\": [\n                              {\n                                  \"code\": \"PR\",\n                                  \"airline\": \"Philippine Airlines\",\n                                  \"recordLocator\": \"Q3PM1G\"\n                              }\n                          ],\n                          \"insurance\": []\n                      }\n                  ],\n                  \"trips\": [\n                      {\n                          \"segments\": [\n                              {\n                                  \"segmentId\": 1001959,\n                                  \"passengers\":{\n                                      \"p1\":{\n                                          \"fullname\":\"Tester Testerov\",\n                                          \"products\":{\n                                              \"bag\":\"test_30kg\",\n                                              \"seat\":\"24E\"\n                                          }\n                                      }\n                                  },\n                                  \"airline\": \"PR\",\n                                  \"airlineName\": \"Philippine Airlines\",\n                                  \"mainAirline\": \"PR\",\n                                  \"arrivalAirport\": \"MNL\",\n                                  \"arrivalTime\": \"2021-05-15 04:00:00\",\n                                  \"departureAirport\": \"LAX\",\n                                  \"departureTime\": \"2021-05-13 22:30:00\",\n                                  \"bookingClass\": \"U\",\n                                  \"flightNumber\": 103,\n                                  \"statusCode\": \"HK\",\n                                  \"operatingAirline\": \"Philippine Airlines\",\n                                  \"operatingAirlineCode\": \"PR\",\n                                  \"cabin\": \"Economy\",\n                                  \"departureCity\": \"Los Angeles\",\n                                  \"arrivalCity\": \"Manila\",\n                                  \"departureCountry\": \"US\",\n                                  \"arrivalCountry\": \"PH\",\n                                  \"departureAirportName\": \"Los Angeles International Airport\",\n                                  \"arrivalAirportName\": \"Ninoy Aquino International Airport\",\n                                  \"flightDuration\": 870,\n                                  \"layoverDuration\": 0,\n                                  \"airlineRecordLocator\": \"Q3PM1G\",\n                                  \"aircraft\": \"773\",\n                                  \"baggage\": 2,\n                                  \"carryOn\": true,\n                                  \"marriageGroup\": \"773\",\n                                  \"fareCode\": \"U9XBUS\",\n                                  \"mileage\": 7305\n                              },\n                              {\n                                  \"segmentId\": 1001960,\n                                  \"passengers\":{\n                                      \"p1\":{\n                                          \"fullname\":\"Tester Testerov\",\n                                          \"products\":{\n                                              \"bag\":\"test_30kg\",\n                                              \"seat\":\"25E\"\n                                          }\n                                      }\n                                  },\n                                  \"airline\": \"PR\",\n                                  \"airlineName\": \"Philippine Airlines\",\n                                  \"mainAirline\": \"PR\",\n                                  \"arrivalAirport\": \"TPE\",\n                                  \"arrivalTime\": \"2021-05-15 08:40:00\",\n                                  \"departureAirport\": \"MNL\",\n                                  \"departureTime\": \"2021-05-15 06:30:00\",\n                                  \"bookingClass\": \"U\",\n                                  \"flightNumber\": 890,\n                                  \"statusCode\": \"HK\",\n                                  \"operatingAirline\": \"Philippine Airlines\",\n                                  \"operatingAirlineCode\": \"PR\",\n                                  \"cabin\": \"Economy\",\n                                  \"departureCity\": \"Manila\",\n                                  \"arrivalCity\": \"Taipei\",\n                                  \"departureCountry\": \"PH\",\n                                  \"arrivalCountry\": \"TW\",\n                                  \"departureAirportName\": \"Ninoy Aquino International Airport\",\n                                  \"arrivalAirportName\": \"Taiwan Taoyuan International Airport\",\n                                  \"flightDuration\": 130,\n                                  \"layoverDuration\": 150,\n                                  \"airlineRecordLocator\": \"Q3PM1G\",\n                                  \"aircraft\": \"321\",\n                                  \"baggage\": 2,\n                                  \"carryOn\": true,\n                                  \"marriageGroup\": \"321\",\n                                  \"fareCode\": \"U9XBUS\",\n                                  \"mileage\": 728\n                              }\n                          ]\n                      }\n                  ],\n                  \"price\": {\n                      \"tickets\": 1,\n                      \"selling\": 767.75,\n                      \"currentProfit\": 0,\n                      \"fare\": 446,\n                      \"net\": 717.75,\n                      \"taxes\": 321.75,\n                      \"tips\": 0,\n                      \"currency\": \"USD\",\n                      \"detail\": {\n                          \"ADT\": {\n                              \"selling\": 767.75,\n                              \"fare\": 446,\n                              \"baseTaxes\": 271.75,\n                              \"taxes\": 321.75,\n                              \"tickets\": 1,\n                              \"insurance\": 0\n                          }\n                      }\n                  },\n                  \"departureTime\": \"2021-05-13 22:30:00\",\n                  \"invoiceUri\": \"\\/checkout\\/download\\/OE96040\\/invoice\",\n                  \"eTicketUri\": \"\\/checkout\\/download\\/OE96040\\/e-ticket\",\n                  \"scheduleChange\": \"No\"\n              }\n          ],\n          \"trips\": [],\n          \"payments\": [\n              {\n                  \"pay_amount\": 200.21,\n                  \"pay_currency\": \"USD\",\n                  \"pay_auth_id\": 728282,\n                  \"pay_type\": \"Capture\",\n                  \"pay_code\": \"ch_YYYYYYYYYYYYYYYYYYYYY\",\n                  \"pay_date\": \"2021-03-25\",\n                  \"pay_method_key\": \"card\",\n                  \"pay_description\": \"example description\",\n                  \"creditCard\": {\n                      \"holder_name\": \"Tester holder\",\n                      \"number\": \"111**********111\",\n                      \"type\": \"Visa\",\n                      \"expiration\": \"07 / 23\",\n                      \"cvv\": \"123\"\n                  },\n                  \"billingInfo\": {\n                      \"first_name\": \"Hobbit\",\n                      \"middle_name\": \"Hard\",\n                      \"last_name\": \"Lover\",\n                      \"address\": \"1013 Weda Cir\",\n                      \"country_id\": \"US\",\n                      \"city\": \"Gotham City\",\n                      \"state\": \"KY\",\n                      \"zip\": \"99999\",\n                      \"phone\": \"+19074861000\",\n                      \"email\": \"barabara@test.com\"\n                  }\n              }\n          ],\n          \"options\": [\n              {\n                \"pqo_key\": \"cfar\",\n                \"pqo_name\": \"CFAR option\",\n                \"pqo_price\": 750.21,\n                \"pqo_markup\": 100.21,\n                \"pqo_description\": \"CFAR option: Cancel before limit\",\n                \"pqo_request_data\": \"{\\\"type\\\":\\\"standard\\\",\\\"amount\\\":750.21,\\\"options\\\":[{\\\"name\\\":\\\"Cancel before limit\\\",\\\"type\\\":\\\"before\\\",\\\"limit\\\":0,\\\"value\\\":\\\"60\\\"}],\\\"paxCount\\\":3,\\\"isActivated\\\":true,\\\"amountPerPax\\\":250.07}\"\n              },\n              {\n                \"pqo_key\": \"package\",\n                \"pqo_name\": \"Package option\",\n                \"pqo_price\": 89.85,\n                \"pqo_markup\": 0,\n                \"pqo_description\": \"Package option: Exchange and Refund Processing Fee\",\n                \"pqo_request_data\": \"{\\\"type\\\":\\\"standard\\\",\\\"amount\\\":89.85,\\\"options\\\":[{\\\"name\\\":\\\"24 Hour Free Cancellation\\\",\\\"type\\\":\\\"VOID\\\",\\\"value\\\":\\\"included\\\",\\\"special\\\":true}],\\\"paxCount\\\":3,\\\"isActivated\\\":true,\\\"amountPerPax\\\":29.95}\"\n              }\n          ]\n      }",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n     \"status\": 200,\n     \"message\": \"OK\",\n     \"data\": {\n         \"resultMessage\": \"Order Uid(or6061be5ec5c0e) successful processed\"\n     }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "HTTP/1.1 200 OK\n{\n    \"status\": 422,\n    \"message\": \"Validation error\",\n    \"errors\": {\n        \"orderUid\": [\n            \"orderUid cannot be blank\"\n       ]\n    },\n    \"code\": \"15801\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (404):",
          "content": "HTTP/1.1 200 OK\n{\n    \"status\": 404,\n    \"message\": \"Order not found\",\n    \"code\": \"15300\",\n    \"errors\": []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/FlightController.php",
    "groupTitle": "Flight"
  },
  {
    "type": "post",
    "url": "/v1/flight/ticket-issue",
    "title": "Flight Ticket Issue",
    "version": "0.1.0",
    "name": "Flight_Ticket_Issue",
    "group": "Flight",
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
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Accept-Encoding",
            "description": ""
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
            "size": "255",
            "optional": false,
            "field": "fareId",
            "description": "<p>Fare Id (Order identity)</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "flights",
            "description": "<p>Flights data array</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": true,
            "field": "payments",
            "description": "<p>Payments data array</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": true,
            "field": "options",
            "description": "<p>Options data array</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n{\n          \"fareId\": \"or6061be5ec5c0e\",\n          \"parentBookingId\": \"OE96041\",\n          \"parentId\": 205975,\n          \"sameItinerary\": true,\n          \"flights\": [\n              {\n                  \"appKey\": \"038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826\",\n                  \"uniqueId\": \"OE96040\",\n                  \"status\": 3,\n                  \"pnr\": \"Q3PM1G\",\n                  \"gds\": \"S\",\n                  \"flightType\": \"RT\",\n                  \"validatingCarrier\": \"PR\",\n                  \"bookingInfo\": [\n                      {\n                          \"bookingId\": \"OE96040\",\n                          \"pnr\": \"Q3PM1G\",\n                          \"gds\": \"S\",\n                          \"validatingCarrier\": \"PR\",\n                          \"status\": 3,\n                          \"state\": \"Success\",\n                          \"passengers\": {\n                              \"1\": {\n                                  \"fullName\": \"Arthur Davis\",\n                                  \"first_name\": \"Arthur\",\n                                  \"middle_name\": \"\",\n                                  \"last_name\": \"Davis\",\n                                  \"birth_date\": \"1963-04-07\",\n                                  \"nationality\": \"US\",\n                                  \"gender\": \"M\",\n                                  \"aGender\": \"Mr.\",\n                                  \"tktNumber\": \"tktNumber\",\n                                  \"paxType\": \"ADT\"\n                              }\n                          },\n                          \"airlinesCode\": [\n                              {\n                                  \"code\": \"PR\",\n                                  \"airline\": \"Philippine Airlines\",\n                                  \"recordLocator\": \"Q3PM1G\"\n                              }\n                          ],\n                          \"insurance\": []\n                      }\n                  ],\n                  \"trips\": [\n                      {\n                          \"segments\": [\n                              {\n                                  \"segmentId\": 1001959,\n                                  \"passengers\":{\n                                      \"p1\":{\n                                          \"fullname\":\"Tester Testerov\",\n                                          \"products\":{\n                                              \"bag\":\"test_30kg\",\n                                              \"seat\":\"24E\"\n                                          }\n                                      }\n                                  },\n                                  \"airline\": \"PR\",\n                                  \"airlineName\": \"Philippine Airlines\",\n                                  \"mainAirline\": \"PR\",\n                                  \"arrivalAirport\": \"MNL\",\n                                  \"arrivalTime\": \"2021-05-15 04:00:00\",\n                                  \"departureAirport\": \"LAX\",\n                                  \"departureTime\": \"2021-05-13 22:30:00\",\n                                  \"bookingClass\": \"U\",\n                                  \"flightNumber\": 103,\n                                  \"statusCode\": \"HK\",\n                                  \"operatingAirline\": \"Philippine Airlines\",\n                                  \"operatingAirlineCode\": \"PR\",\n                                  \"cabin\": \"Economy\",\n                                  \"departureCity\": \"Los Angeles\",\n                                  \"arrivalCity\": \"Manila\",\n                                  \"departureCountry\": \"US\",\n                                  \"arrivalCountry\": \"PH\",\n                                  \"departureAirportName\": \"Los Angeles International Airport\",\n                                  \"arrivalAirportName\": \"Ninoy Aquino International Airport\",\n                                  \"flightDuration\": 870,\n                                  \"layoverDuration\": 0,\n                                  \"airlineRecordLocator\": \"Q3PM1G\",\n                                  \"aircraft\": \"773\",\n                                  \"baggage\": 2,\n                                  \"carryOn\": true,\n                                  \"marriageGroup\": \"773\",\n                                  \"fareCode\": \"U9XBUS\",\n                                  \"mileage\": 7305\n                              },\n                              {\n                                  \"segmentId\": 1001960,\n                                  \"passengers\":{\n                                      \"p1\":{\n                                          \"fullname\":\"Tester Testerov\",\n                                          \"products\":{\n                                              \"bag\":\"test_30kg\",\n                                              \"seat\":\"25E\"\n                                          }\n                                      }\n                                  },\n                                  \"airline\": \"PR\",\n                                  \"airlineName\": \"Philippine Airlines\",\n                                  \"mainAirline\": \"PR\",\n                                  \"arrivalAirport\": \"TPE\",\n                                  \"arrivalTime\": \"2021-05-15 08:40:00\",\n                                  \"departureAirport\": \"MNL\",\n                                  \"departureTime\": \"2021-05-15 06:30:00\",\n                                  \"bookingClass\": \"U\",\n                                  \"flightNumber\": 890,\n                                  \"statusCode\": \"HK\",\n                                  \"operatingAirline\": \"Philippine Airlines\",\n                                  \"operatingAirlineCode\": \"PR\",\n                                  \"cabin\": \"Economy\",\n                                  \"departureCity\": \"Manila\",\n                                  \"arrivalCity\": \"Taipei\",\n                                  \"departureCountry\": \"PH\",\n                                  \"arrivalCountry\": \"TW\",\n                                  \"departureAirportName\": \"Ninoy Aquino International Airport\",\n                                  \"arrivalAirportName\": \"Taiwan Taoyuan International Airport\",\n                                  \"flightDuration\": 130,\n                                  \"layoverDuration\": 150,\n                                  \"airlineRecordLocator\": \"Q3PM1G\",\n                                  \"aircraft\": \"321\",\n                                  \"baggage\": 2,\n                                  \"carryOn\": true,\n                                  \"marriageGroup\": \"321\",\n                                  \"fareCode\": \"U9XBUS\",\n                                  \"mileage\": 728\n                              }\n                          ]\n                      }\n                  ],\n                  \"price\": {\n                      \"tickets\": 1,\n                      \"selling\": 767.75,\n                      \"currentProfit\": 0,\n                      \"fare\": 446,\n                      \"net\": 717.75,\n                      \"taxes\": 321.75,\n                      \"tips\": 0,\n                      \"currency\": \"USD\",\n                      \"detail\": {\n                          \"ADT\": {\n                              \"selling\": 767.75,\n                              \"fare\": 446,\n                              \"baseTaxes\": 271.75,\n                              \"taxes\": 321.75,\n                              \"tickets\": 1,\n                              \"insurance\": 0\n                          }\n                      }\n                  },\n                  \"departureTime\": \"2021-05-13 22:30:00\",\n                  \"invoiceUri\": \"\\/checkout\\/download\\/OE96040\\/invoice\",\n                  \"eTicketUri\": \"\\/checkout\\/download\\/OE96040\\/e-ticket\",\n                  \"scheduleChange\": \"No\"\n              }\n          ],\n          \"trips\": [],\n          \"payments\": [\n              {\n                  \"pay_amount\": 200.21,\n                  \"pay_currency\": \"USD\",\n                  \"pay_auth_id\": 728282,\n                  \"pay_type\": \"Capture\",\n                  \"pay_code\": \"ch_YYYYYYYYYYYYYYYYYYYYY\",\n                  \"pay_date\": \"2021-03-25\",\n                  \"pay_method_key\": \"card\",\n                  \"pay_description\": \"example description\",\n                  \"creditCard\": {\n                      \"holder_name\": \"Tester holder\",\n                      \"number\": \"111**********111\",\n                      \"type\": \"Visa\",\n                      \"expiration\": \"07 / 23\",\n                      \"cvv\": \"123\"\n                  },\n                  \"billingInfo\": {\n                      \"first_name\": \"Hobbit\",\n                      \"middle_name\": \"Hard\",\n                      \"last_name\": \"Lover\",\n                      \"address\": \"1013 Weda Cir\",\n                      \"country_id\": \"US\",\n                      \"city\": \"Gotham City\",\n                      \"state\": \"KY\",\n                      \"zip\": \"99999\",\n                      \"phone\": \"+19074861000\",\n                      \"email\": \"barabara@test.com\"\n                  }\n              }\n          ],\n          \"options\": [\n              {\n                \"pqo_key\": \"cfar\",\n                \"pqo_name\": \"CFAR option\",\n                \"pqo_price\": 750.21,\n                \"pqo_markup\": 100.21,\n                \"pqo_description\": \"CFAR option: Cancel before limit\",\n                \"pqo_request_data\": \"{\\\"type\\\":\\\"standard\\\",\\\"amount\\\":750.21,\\\"options\\\":[{\\\"name\\\":\\\"Cancel before limit\\\",\\\"type\\\":\\\"before\\\",\\\"limit\\\":0,\\\"value\\\":\\\"60\\\"}],\\\"paxCount\\\":3,\\\"isActivated\\\":true,\\\"amountPerPax\\\":250.07}\"\n              },\n              {\n                \"pqo_key\": \"package\",\n                \"pqo_name\": \"Package option\",\n                \"pqo_price\": 89.85,\n                \"pqo_markup\": 0,\n                \"pqo_description\": \"Package option: Exchange and Refund Processing Fee\",\n                \"pqo_request_data\": \"{\\\"type\\\":\\\"standard\\\",\\\"amount\\\":89.85,\\\"options\\\":[{\\\"name\\\":\\\"24 Hour Free Cancellation\\\",\\\"type\\\":\\\"VOID\\\",\\\"value\\\":\\\"included\\\",\\\"special\\\":true}],\\\"paxCount\\\":3,\\\"isActivated\\\":true,\\\"amountPerPax\\\":29.95}\"\n              }\n          ]\n      }",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n     \"status\": 200,\n     \"message\": \"OK\",\n     \"data\": {\n         \"resultMessage\": \"Order Uid(or6061be5ec5c0e) successful processed\"\n     }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "HTTP/1.1 200 OK\n{\n    \"status\": 422,\n    \"message\": \"Validation error\",\n    \"errors\": {\n        \"orderUid\": [\n            \"orderUid cannot be blank\"\n       ]\n    },\n    \"code\": \"15801\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (404):",
          "content": "HTTP/1.1 200 OK\n{\n    \"status\": 404,\n    \"message\": \"Order not found\",\n    \"code\": \"15300\",\n    \"errors\": []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/FlightController.php",
    "groupTitle": "Flight"
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
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "20",
            "optional": false,
            "field": "lead.user_language",
            "description": "<p>User language</p>"
          },
          {
            "group": "Parameter",
            "type": "datetime",
            "size": "YYYY-MM-DD HH:mm:ss",
            "optional": true,
            "field": "lead.expire_at",
            "description": "<p>Expire at</p>"
          },
          {
            "group": "Parameter",
            "type": "object[]",
            "optional": false,
            "field": "lead.visitor_log",
            "description": "<p>Array of Visitor log</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "10",
            "optional": false,
            "field": "lead.visitor_log.vl_source_cid",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "36",
            "optional": false,
            "field": "lead.visitor_log.vl_ga_client_id",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "36",
            "optional": false,
            "field": "lead.visitor_log.vl_ga_user_id",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "lead.visitor_log.vl_customer_id",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": false,
            "field": "lead.visitor_log.vl_gclid",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "255",
            "optional": false,
            "field": "lead.visitor_log.vl_dclid",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "lead.visitor_log.vl_utm_source",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "lead.visitor_log.vl_utm_medium",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "lead.visitor_log.vl_utm_campaign",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "lead.visitor_log.vl_utm_term",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "lead.visitor_log.vl_utm_content",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "500",
            "optional": false,
            "field": "lead.visitor_log.vl_referral_url",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "500",
            "optional": false,
            "field": "lead.visitor_log.vl_location_url",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "500",
            "optional": false,
            "field": "lead.visitor_log.vl_user_agent",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "39",
            "optional": false,
            "field": "lead.visitor_log.vl_ip_address",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "object[]",
            "optional": true,
            "field": "lead.lead_data",
            "description": "<p>Array of Lead Data</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": true,
            "field": "lead.lead_data.field_key",
            "description": "<p>Lead Data Key</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "500",
            "optional": true,
            "field": "lead.lead_data.field_value",
            "description": "<p>Lead Data Value</p>"
          },
          {
            "group": "Parameter",
            "type": "datetime",
            "size": "YYYY-MM-DD HH:mm:ss",
            "optional": false,
            "field": "lead.visitor_log.vl_visit_dt",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "Client",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Client.name",
            "description": "<p>Client name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Client.phone",
            "description": "<p>Client phone</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Client.email",
            "description": "<p>Client email</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Client.client_ip",
            "description": "<p>Client client_ip</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Client.uuid",
            "description": "<p>Client uuid</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n   \"apiKey\": \"d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd\",\n   \"lead\": {\n       \"flights\": [\n           {\n               \"origin\": \"KIV\",\n               \"destination\": \"DME\",\n               \"departure\": \"2018-10-13 13:50:00\",\n           },\n           {\n               \"origin\": \"DME\",\n               \"destination\": \"KIV\",\n               \"departure\": \"2018-10-18 10:54:00\",\n           }\n       ],\n       \"emails\": [\n         \"email1@gmail.com\",\n         \"email2@gmail.com\",\n       ],\n       \"phones\": [\n         \"+373-69-487523\",\n         \"022-45-7895-89\",\n       ],\n       \"source_id\": 38,\n       \"sub_sources_code\": \"BBM101\",\n       \"adults\": 1,\n       \"client_first_name\": \"Alexandr\",\n       \"client_last_name\": \"Freeman\",\n       \"user_language\": \"en-GB\",\n       \"expire_at\": \"2020-01-20 12:12:12\",\n       \"lead_data\": [\n              {\n                 \"field_key\": \"example_key\",\n                 \"field_value\": \"example_value\"\n             }\n       ],\n       \"visitor_log\": [\n              {\n                  \"vl_source_cid\": \"string_abc\",\n                  \"vl_ga_client_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n                  \"vl_ga_user_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n                  \"vl_customer_id\": \"3\",\n                  \"vl_gclid\": \"gclid=TeSter-123#bookmark\",\n                  \"vl_dclid\": \"CJKu8LrQxd4CFQ1qwQodmJIElw\",\n                  \"vl_utm_source\": \"newsletter4\",\n                  \"vl_utm_medium\": \"string_abc\",\n                  \"vl_utm_campaign\": \"string_abc\",\n                  \"vl_utm_term\": \"string_abc\",\n                  \"vl_utm_content\": \"string_abc\",\n                  \"vl_referral_url\": \"string_abc\",\n                  \"vl_location_url\": \"string_abc\",\n                  \"vl_user_agent\": \"string_abc\",\n                  \"vl_ip_address\": \"127.0.0.1\",\n                  \"vl_visit_dt\": \"2020-02-14 12:00:00\"\n              },\n              {\n                  \"vl_source_cid\": \"string_abc\",\n                  \"vl_ga_client_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n                  \"vl_ga_user_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n                  \"vl_customer_id\": \"3\",\n                  \"vl_gclid\": \"gclid=TeSter-123#bookmark\",\n                  \"vl_dclid\": \"CJKu8LrQxd4CFQ1qwQodmJIElw\",\n                  \"vl_utm_source\": \"newsletter4\",\n                  \"vl_utm_medium\": \"string_abc\",\n                  \"vl_utm_campaign\": \"string_abc\",\n                  \"vl_utm_term\": \"string_abc\",\n                  \"vl_utm_content\": \"string_abc\",\n                  \"vl_referral_url\": \"string_abc\",\n                  \"vl_location_url\": \"string_abc\",\n                  \"vl_user_agent\": \"string_abc\",\n                  \"vl_ip_address\": \"127.0.0.1\",\n                  \"vl_visit_dt\": \"2020-02-14 12:00:00\"\n              }\n       ]\n   },\n   \"Client\": {\n       \"name\": \"Alexandr\",\n       \"phone\": \"+373-69-487523\",\n       \"email\": \"email1@gmail.com\",\n       \"client_ip\": \"127.0.0.1\",\n       \"uuid\": \"35009a79-1a05-49d7-b876-2b884d0f825b\"\n   }\n}",
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
          "content": "    HTTP/1.1 200 OK\n{\n  \"status\": 200,\n  \"name\": \"Success\",\n  \"code\": 0,\n  \"message\": \"\",\n  \"data\": {\n      \"response\": {\n          \"lead\": {\n              \"client_id\": 11,\n              \"employee_id\": null,\n              \"status\": 1,\n              \"uid\": \"5b73b80eaf69b\",\n              \"gid\": \"65df1546edccce15518e929e5af1a4\",\n              \"project_id\": 6,\n              \"source_id\": \"38\",\n              \"trip_type\": \"RT\",\n              \"cabin\": \"E\",\n              \"adults\": \"1\",\n              \"children\": 0,\n              \"infants\": 0,\n              \"notes_for_experts\": null,\n              \"created\": \"2018-08-15 05:20:14\",\n              \"updated\": \"2018-08-15 05:20:14\",\n              \"request_ip\": \"127.0.0.1\",\n              \"request_ip_detail\": \"{\\\"ip\\\":\\\"127.0.0.1\\\",\\\"city\\\":\\\"North Pole\\\",\\\"postal\\\":\\\"99705\\\",\\\"state\\\":\\\"Alaska\\\",\\\"state_code\\\":\\\"AK\\\",\\\"country\\\":\\\"United States\\\",\\\"country_code\\\":\\\"US\\\",\\\"location\\\":\\\"64.7548317,-147.3431046\\\",\\\"timezone\\\":{\\\"id\\\":\\\"America\\\\/Anchorage\\\",\\\"location\\\":\\\"61.21805,-149.90028\\\",\\\"country_code\\\":\\\"US\\\",\\\"country_name\\\":\\\"United States of America\\\",\\\"iso3166_1_alpha_2\\\":\\\"US\\\",\\\"iso3166_1_alpha_3\\\":\\\"USA\\\",\\\"un_m49_code\\\":\\\"840\\\",\\\"itu\\\":\\\"USA\\\",\\\"marc\\\":\\\"xxu\\\",\\\"wmo\\\":\\\"US\\\",\\\"ds\\\":\\\"USA\\\",\\\"phone_prefix\\\":\\\"1\\\",\\\"fifa\\\":\\\"USA\\\",\\\"fips\\\":\\\"US\\\",\\\"gual\\\":\\\"259\\\",\\\"ioc\\\":\\\"USA\\\",\\\"currency_alpha_code\\\":\\\"USD\\\",\\\"currency_country_name\\\":\\\"UNITED STATES\\\",\\\"currency_minor_unit\\\":\\\"2\\\",\\\"currency_name\\\":\\\"US Dollar\\\",\\\"currency_code\\\":\\\"840\\\",\\\"independent\\\":\\\"Yes\\\",\\\"capital\\\":\\\"Washington\\\",\\\"continent\\\":\\\"NA\\\",\\\"tld\\\":\\\".us\\\",\\\"languages\\\":\\\"en-US,es-US,haw,fr\\\",\\\"geoname_id\\\":\\\"6252001\\\",\\\"edgar\\\":\\\"\\\"},\\\"datetime\\\":{\\\"date\\\":\\\"08\\\\/14\\\\/2018\\\",\\\"date_time\\\":\\\"08\\\\/14\\\\/2018 21:20:15\\\",\\\"date_time_txt\\\":\\\"Tuesday, August 14, 2018 21:20:15\\\",\\\"date_time_wti\\\":\\\"Tue, 14 Aug 2018 21:20:15 -0800\\\",\\\"date_time_ymd\\\":\\\"2018-08-14T21:20:15-08:00\\\",\\\"time\\\":\\\"21:20:15\\\",\\\"month\\\":\\\"8\\\",\\\"month_wilz\\\":\\\"08\\\",\\\"month_abbr\\\":\\\"Aug\\\",\\\"month_full\\\":\\\"August\\\",\\\"month_days\\\":\\\"31\\\",\\\"day\\\":\\\"14\\\",\\\"day_wilz\\\":\\\"14\\\",\\\"day_abbr\\\":\\\"Tue\\\",\\\"day_full\\\":\\\"Tuesday\\\",\\\"year\\\":\\\"2018\\\",\\\"year_abbr\\\":\\\"18\\\",\\\"hour_12_wolz\\\":\\\"9\\\",\\\"hour_12_wilz\\\":\\\"09\\\",\\\"hour_24_wolz\\\":\\\"21\\\",\\\"hour_24_wilz\\\":\\\"21\\\",\\\"hour_am_pm\\\":\\\"pm\\\",\\\"minutes\\\":\\\"20\\\",\\\"seconds\\\":\\\"15\\\",\\\"week\\\":\\\"33\\\",\\\"offset_seconds\\\":\\\"-28800\\\",\\\"offset_minutes\\\":\\\"-480\\\",\\\"offset_hours\\\":\\\"-8\\\",\\\"offset_gmt\\\":\\\"-08:00\\\",\\\"offset_tzid\\\":\\\"America\\\\/Anchorage\\\",\\\"offset_tzab\\\":\\\"AKDT\\\",\\\"offset_tzfull\\\":\\\"Alaska Daylight Time\\\",\\\"tz_string\\\":\\\"AKST+9AKDT,M3.2.0\\\\/2,M11.1.0\\\\/2\\\",\\\"dst\\\":\\\"true\\\",\\\"dst_observes\\\":\\\"true\\\",\\\"timeday_spe\\\":\\\"evening\\\",\\\"timeday_gen\\\":\\\"evening\\\"}}\",\n              \"offset_gmt\": \"-08.00\",\n              \"snooze_for\": null,\n              \"rating\": null,\n              \"id\": 7\n          },\n          \"flights\": [\n              {\n                  \"origin\": \"BOS\",\n                  \"destination\": \"LGW\",\n                  \"departure\": \"2018-09-19\"\n              },\n              {\n                  \"origin\": \"LGW\",\n                  \"destination\": \"BOS\",\n                  \"departure\": \"2018-09-22\"\n              }\n          ],\n          \"emails\": [\n              \"chalpet@gmail.com\",\n              \"chalpet2@gmail.com\"\n          ],\n          \"phones\": [\n              \"+373-69-98-698\",\n              \"+373-69-98-698\"\n          ],\n         \"client\": {\n             \"uuid\": \"35009a79-1a05-49d7-b876-2b884d0f825b\"\n             \"client_id\": 331968,\n             \"first_name\": \"Johann\",\n             \"middle_name\": \"Sebastian\",\n             \"last_name\": \"Bach\",\n             \"phones\": [\n                \"+13152572166\"\n             ],\n             \"emails\": [\n                \"example@test.com\",\n                \"bah@gmail.com\"\n             ]\n          },\n         \"leadDataInserted\": [\n             {\n                 \"ld_field_key\": \"kayakclickid\",\n                 \"ld_field_value\": \"example_value\",\n                 \"ld_id\": 3\n             }\n         ],\n         \"warnings\": []\n      },\n      \"request\": {\n          \"client_id\": null,\n          \"employee_id\": null,\n          \"status\": null,\n          \"uid\": null,\n          \"project_id\": 6,\n          \"source_id\": \"38\",\n          \"trip_type\": null,\n          \"cabin\": null,\n          \"adults\": \"1\",\n          \"children\": null,\n          \"infants\": null,\n          \"notes_for_experts\": null,\n          \"created\": null,\n          \"updated\": null,\n          \"request_ip\": null,\n          \"request_ip_detail\": null,\n          \"offset_gmt\": null,\n          \"snooze_for\": null,\n          \"rating\": null,\n          \"flights\": [\n              {\n                  \"origin\": \"BOS\",\n                  \"destination\": \"LGW\",\n                  \"departure\": \"2018-09-19\"\n              },\n              {\n                  \"origin\": \"LGW\",\n                  \"destination\": \"BOS\",\n                  \"departure\": \"2018-09-22\"\n              }\n          ],\n          \"emails\": [\n              \"chalpet@gmail.com\",\n              \"chalpet2@gmail.com\"\n          ],\n          \"phones\": [\n              \"+373-69-98-698\",\n              \"+373-69-98-698\"\n          ],\n          \"client_first_name\": \"Alexandr\",\n          \"client_last_name\": \"Freeman\"\n      }\n  },\n  \"action\": \"v1/lead/create\",\n  \"response_id\": 42,\n  \"request_dt\": \"2018-08-15 05:20:14\",\n  \"response_dt\": \"2018-08-15 05:20:15\"\n}",
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
    "url": "/v2/lead/create",
    "title": "Create Lead Alternative",
    "version": "0.2.0",
    "name": "CreateLeadAlternative",
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
            "type": "string",
            "size": "50",
            "optional": true,
            "field": "lead.project_key",
            "description": "<p>Project key</p>"
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
            "optional": true,
            "field": "lead.children",
            "description": "<p>Children count (by default 0)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "0..9",
            "optional": true,
            "field": "lead.infants",
            "description": "<p>Infants count (by default 0)</p>"
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
            "size": "15",
            "optional": true,
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
            "field": "lead.flights",
            "description": "<p>Flights</p>"
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
            "size": "YYYY-MM-DD",
            "optional": false,
            "field": "lead.flights.departure",
            "description": "<p>Flight Departure DateTime (format YYYY-MM-DD)</p>"
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
            "description": "<p>Client phone or Client email or Client chat_visitor_id is required</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "160",
            "optional": false,
            "field": "lead.client.email",
            "description": "<p>Client email or Client phone or Client chat_visitor_id is required</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "lead.client.chat_visitor_id",
            "description": "<p>Client chat_visitor_id or Client email or Client phone is required</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": true,
            "field": "lead.client.uuid",
            "description": "<p>Client uuid</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "size": "2",
            "allowedValues": [
              "14-BOOK_FAILED",
              "15-ALTERNATIVE",
              "1-PENDING"
            ],
            "optional": true,
            "field": "lead.status",
            "description": "<p>Status (by default 1-PENDING)</p>"
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
            "optional": true,
            "field": "lead.cabin",
            "description": "<p>Cabin (by default E)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "lead.flight_id",
            "description": "<p>BO Flight ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "5",
            "optional": false,
            "field": "lead.user_language",
            "description": "<p>User Language</p>"
          },
          {
            "group": "Parameter",
            "type": "datetime",
            "size": "YYYY-MM-DD HH:mm:ss",
            "optional": true,
            "field": "lead.expire_at",
            "description": "<p>Expire at</p>"
          },
          {
            "group": "Parameter",
            "type": "object[]",
            "optional": true,
            "field": "lead.lead_data",
            "description": "<p>Array of Lead Data</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": true,
            "field": "lead.lead_data.field_key",
            "description": "<p>Lead Data Key</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "500",
            "optional": true,
            "field": "lead.lead_data.field_value",
            "description": "<p>Lead Data Value</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n{\n     \"lead\": {\n          \"client\": {\n              \"phone\": \"+37369333333\",\n              \"email\": \"email@email.com\",\n              \"uuid\" : \"af5246f1-094f-4fde-ada3-bd7298621613\",\n              \"chat_visitor_id\" : \"6b811a3e-41c4-4d49-a99a-afw3e4rtf3tfregf\"\n          },\n          \"uid\": \"WD6q53PO3b\",\n          \"status\": 14,\n          \"source_code\": \"JIVOCH\",\n          \"project_key\": \"ovago\",\n          \"cabin\": \"E\",\n          \"adults\": 2,\n          \"children\": 2,\n          \"infants\": 2,\n          \"request_ip\": \"12.12.12.12\",\n          \"discount_id\": \"123123\",\n          \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36\",\n          \"flight_id\": 12457,\n          \"user_language\": \"en-GB\",\n          \"expire_at\": \"2020-01-20 12:12:12\",\n          \"flights\": [\n              {\n                  \"origin\": \"NYC\",\n                  \"destination\": \"LON\",\n                  \"departure\": \"2019-12-16\"\n              },\n              {\n                  \"origin\": \"LON\",\n                  \"destination\": \"NYC\",\n                  \"departure\": \"2019-12-17\"\n              },\n              {\n                  \"origin\": \"LON\",\n                  \"destination\": \"NYC\",\n                  \"departure\": \"2019-12-18\"\n              }\n          ],\n         \"lead_data\": [\n              {\n                 \"field_key\": \"example_key\",\n                 \"field_value\": \"example_value\"\n             }\n         ]\n      }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n      \"status\": 200,\n      \"message\": \"OK\",\n      \"data\": {\n         \"lead\": {\n              \"id\": 370949,\n              \"uid\": \"WD6q53PO3b\",\n              \"gid\": \"63e1505f4a8a87e6651048e3e3eae4e1\",\n              \"client_id\": 1034,\n              \"client\": {\n                 \"uuid\": \"35009a79-1a05-49d7-b876-2b884d0f825b\"\n                 \"client_id\": 331968,\n                 \"first_name\": \"Johann\",\n                 \"middle_name\": \"Sebastian\",\n                 \"last_name\": \"Bach\",\n                 \"phones\": [\n                     \"+13152572166\"\n                 ],\n                 \"emails\": [\n                     \"example@test.com\",\n                     \"bah@gmail.com\"\n                 ]\n             },\n             \"leadDataInserted\": [\n                 {\n                     \"ld_field_key\": \"kayakclickid\",\n                     \"ld_field_value\": \"example_value\",\n                     \"ld_id\": 3\n                 }\n             ],\n             \"warnings\": []\n         }\n      }\n      \"request\": {\n          \"lead\": {\n             \"client\": {\n                  \"phone\": \"+37369636963\",\n                  \"email\": \"example@test.com\",\n                  \"uuid\" : \"af5246f1-094f-4fde-ada3-bd7298621613\"\n              },\n              \"uid\": \"WD6q53PO3b\",\n              \"status\": 14,\n              \"source_code\": \"JIVOCH\",\n              \"cabin\": \"E\",\n              \"adults\": 2,\n              \"children\": 2,\n              \"infants\": 2,\n              \"request_ip\": \"12.12.12.12\",\n              \"discount_id\": \"123123\",\n              \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36\",\n              \"flight_id\": 12457,\n              \"user_language\": \"en-GB\",\n              \"expire_at\": \"2020-01-20 12:12:12\",\n              \"flights\": [\n                  {\n                      \"origin\": \"NYC\",\n                      \"destination\": \"LON\",\n                      \"departure\": \"2019-12-16\"\n                  },\n                  {\n                      \"origin\": \"LON\",\n                      \"destination\": \"NYC\",\n                      \"departure\": \"2019-12-17\"\n                  },\n                  {\n                      \"origin\": \"LON\",\n                      \"destination\": \"NYC\",\n                      \"departure\": \"2019-12-18\"\n                  }\n              ]\n          }\n      },\n      \"technical\": {\n          \"action\": \"v2/lead/create\",\n          \"response_id\": 11930215,\n          \"request_dt\": \"2019-12-30 12:22:20\",\n          \"response_dt\": \"2019-12-30 12:22:21\",\n          \"execution_time\": 0.055,\n          \"memory_usage\": 1394416\n      }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n      \"status\": 422,\n      \"message\": \"Validation error\",\n      \"errors\": {\n          \"children\": [\n              \"Children must be no greater than 9.\"\n          ],\n          \"flights[0][origin]\": [\n              \"IATA (NY) not found.\"\n          ],\n          \"flights[2][departure]\": [\n              \"The format of Departure is invalid.\"\n          ],\n          \"client[phone]\": [\n             \"The format of Phone is invalid.\"\n          ]\n      },\n      \"code\": 10301,\n      \"request\": {\n          ...\n      },\n      \"technical\": {\n          ...\n     }\n}",
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
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "lead.uid",
            "description": "<p>Uid</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n   \"apiKey\": \"d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd\",\n   \"lead\": {\n       \"lead_id\": 302,\n       \"source_id\": 38,\n       \"uid\": \"5fe2081025a25\"\n   }\n}",
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
    "url": "/v1/lead-request/adwords",
    "title": "Lead create from request",
    "version": "0.1.0",
    "name": "Lead_create_adwords",
    "group": "Leads",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "google_key",
            "description": "<p>Google key</p>"
          },
          {
            "group": "Parameter",
            "type": "bool",
            "optional": true,
            "field": "is_test",
            "description": "<p>Is test</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "user_column_data",
            "description": "<p>A repeated key-value tuple transmitting user submitted data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n {\n   \"google_key\":\"examplekey\",\n   \"is_test\":true,\n   \"user_column_data\": [\n        {\n          \"string_value\":\"john@doe.com\",\n          \"column_id\": \"EMAIL\"\n        },\n        {\n          \"string_value\":\"+11234567890\",\n          \"column_id\":\"PHONE_NUMBER\"\n        }\n  ]\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n     \"status\": 200,\n     \"message\": \"OK\",\n     \"data\": {\n         \"resultMessage\": \"LeadRequest created. ID(123)\"\n     }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "HTTP/1.1 200 OK\n{\n    \"status\": 422,\n    \"message\": \"Validation error\",\n    \"errors\": {\n        \"email\": [\n            \"Email cannot be blank\"\n       ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/LeadRequestController.php",
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
    "url": "/v2/offer/confirm-alternative",
    "title": "Confirm Alternative Offer",
    "version": "0.2.0",
    "name": "ConfirmAlternativeOffer",
    "group": "Offer",
    "permission": [
      {
        "name": "Authorized User"
      }
    ],
    "description": "<p>Offer can only be confirmed if it is in the Pending status</p>",
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
            "size": "max 32",
            "optional": false,
            "field": "gid",
            "description": "<p>Offer gid</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n{\n    \"gid\": \"04d3fe3fc74d0514ee93e208a52bcf90\",\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n {\n            \"status\": 200,\n            \"message\": \"OK\",\n        }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n            \"status\": 422,\n            \"message\": \"Error\",\n            \"errors\": [\n                \"Not found Offer\"\n            ],\n            \"code\": \"18402\"\n        }",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Validation Error\n{\n            \"status\": 422,\n            \"message\": \"Validation error\",\n            \"errors\": {\n                \"gid\": [\n                    \"Gid should contain at most 32 characters.\"\n                ]\n            },\n            \"code\": \"18401\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Validation Error\n{\n            \"status\": 422,\n            \"message\": \"Error\",\n            \"errors\": [\n                \"Offer does not contain quotes that can be confirmed\"\n            ],\n            \"code\": \"18404\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n{\n            \"status\": 400,\n            \"message\": \"Load data error\",\n            \"errors\": [\n                \"Not found Offer data on POST request\"\n            ],\n            \"code\": \"18400\"\n        }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/OfferController.php",
    "groupTitle": "Offer"
  },
  {
    "type": "post",
    "url": "/v2/offer/view",
    "title": "View Offer",
    "version": "0.2.0",
    "name": "ViewOffer",
    "group": "Offer",
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
            "optional": false,
            "field": "offerGid",
            "description": "<p>Offer gid</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "visitor",
            "description": "<p>Visitor</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "32",
            "optional": false,
            "field": "visitor.id",
            "description": "<p>Visitor Id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "visitor.ipAddress",
            "description": "<p>Visitor Ip Address</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "255",
            "optional": false,
            "field": "visitor.userAgent",
            "description": "<p>Visitor User Agent</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n{\n    \"offerGid\": \"04d3fe3fc74d0514ee93e208a52bcf90\",\n    \"visitor\": {\n        \"id\": \"hdsjfghsd5489tertwhf289hfgkewr\",\n        \"ipAddress\": \"12.12.13.22\",\n        \"userAgent\": \"mozilea/asdfsdf/ as/dfgsdf gsdf gsdgf/ds\"\n    }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n    \"status\": 200,\n    \"message\": \"OK\",\n    \"offer\": {\n        \"of_gid\": \"ea6dc06421db46b5a77e8505d0934f38\",\n        \"of_uid\": \"of604642e300c54\",\n        \"of_name\": \"Offer 2\",\n        \"of_lead_id\": 513111,\n        \"of_status_id\": 1,\n        \"of_client_currency\": \"USD\",\n        \"of_client_currency_rate\": 1,\n        \"of_app_total\": 343.5,\n        \"of_client_total\": 343.5,\n        \"of_status_name\": \"New\",\n        \"quotes\": [\n            {\n                \"pq_gid\": \"f81636da78e007fcc6653d26a3650285\",\n                \"pq_name\": \"\",\n                \"pq_order_id\": null,\n                \"pq_description\": null,\n                \"pq_status_id\": 1,\n                \"pq_price\": 343.5,\n                \"pq_origin_price\": 343.5,\n                \"pq_client_price\": 343.5,\n                \"pq_service_fee_sum\": 0,\n                \"pq_origin_currency\": \"USD\",\n                \"pq_client_currency\": \"USD\",\n                \"pq_status_name\": \"New\",\n                \"pq_files\": [],\n                \"data\": {\n                    \"fq_flight_id\": 47,\n                    \"fq_source_id\": null,\n                    \"fq_product_quote_id\": 159,\n                    \"fq_gds\": \"T\",\n                    \"fq_gds_pcc\": \"E9V\",\n                    \"fq_gds_offer_id\": null,\n                    \"fq_type_id\": 0,\n                    \"fq_cabin_class\": \"E\",\n                    \"fq_trip_type_id\": 1,\n                    \"fq_main_airline\": \"LO\",\n                    \"fq_fare_type_id\": 1,\n                    \"fq_origin_search_data\": \"{\\\"key\\\":\\\"2_U0FMMTAxKlkyMTAwL0tJVkxPTjIwMjEtMDktMTYqTE9+I0xPNTE2I0xPMjgxfmxjOmVuX3Vz\\\",\\\"routingId\\\":1,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2021-03-11\\\",\\\"totalPrice\\\":343.5,\\\"totalTax\\\":184.5,\\\"comm\\\":0,\\\"isCk\\\":false,\\\"markupId\\\":0,\\\"markupUid\\\":\\\"\\\",\\\"markup\\\":0},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"ADT\\\",\\\"cnt\\\":2,\\\"baseFare\\\":58,\\\"pubBaseFare\\\":58,\\\"baseTax\\\":61.5,\\\"markup\\\":0,\\\"comm\\\":0,\\\"price\\\":119.5,\\\"tax\\\":61.5,\\\"oBaseFare\\\":{\\\"amount\\\":58,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":61.5,\\\"currency\\\":\\\"USD\\\"}},\\\"CHD\\\":{\\\"codeAs\\\":\\\"CHD\\\",\\\"cnt\\\":1,\\\"baseFare\\\":43,\\\"pubBaseFare\\\":43,\\\"baseTax\\\":61.5,\\\"markup\\\":0,\\\"comm\\\":0,\\\"price\\\":104.5,\\\"tax\\\":61.5,\\\"oBaseFare\\\":{\\\"amount\\\":43,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":61.5,\\\"currency\\\":\\\"USD\\\"}}},\\\"penalties\\\":{\\\"exchange\\\":true,\\\"refund\\\":false,\\\"list\\\":[{\\\"type\\\":\\\"ex\\\",\\\"applicability\\\":\\\"before\\\",\\\"permitted\\\":true,\\\"amount\\\":0},{\\\"type\\\":\\\"ex\\\",\\\"applicability\\\":\\\"after\\\",\\\"permitted\\\":true,\\\"amount\\\":0},{\\\"type\\\":\\\"re\\\",\\\"applicability\\\":\\\"before\\\",\\\"permitted\\\":false},{\\\"type\\\":\\\"re\\\",\\\"applicability\\\":\\\"after\\\",\\\"permitted\\\":false}]},\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segments\\\":[{\\\"segmentId\\\":1,\\\"departureTime\\\":\\\"2021-09-16 18:25\\\",\\\"arrivalTime\\\":\\\"2021-09-16 19:15\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"516\\\",\\\"bookingClass\\\":\\\"S\\\",\\\"duration\\\":110,\\\"departureAirportCode\\\":\\\"KIV\\\",\\\"departureAirportTerminal\\\":\\\"\\\",\\\"arrivalAirportCode\\\":\\\"WAW\\\",\\\"arrivalAirportTerminal\\\":\\\"\\\",\\\"operatingAirline\\\":\\\"LO\\\",\\\"airEquipType\\\":\\\"DH4\\\",\\\"marketingAirline\\\":\\\"LO\\\",\\\"marriageGroup\\\":\\\"I\\\",\\\"mileage\\\":508,\\\"cabin\\\":\\\"Y\\\",\\\"cabinIsBasic\\\":true,\\\"brandId\\\":\\\"685421\\\",\\\"brandName\\\":\\\"ECONOMY SAVER\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"S1SAV14\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"carryOn\\\":true,\\\"allowPieces\\\":0},\\\"CHD\\\":{\\\"carryOn\\\":true,\\\"allowPieces\\\":0}},\\\"recheckBaggage\\\":false},{\\\"segmentId\\\":2,\\\"departureTime\\\":\\\"2021-09-17 07:30\\\",\\\"arrivalTime\\\":\\\"2021-09-17 09:25\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"281\\\",\\\"bookingClass\\\":\\\"S\\\",\\\"duration\\\":175,\\\"departureAirportCode\\\":\\\"WAW\\\",\\\"departureAirportTerminal\\\":\\\"\\\",\\\"arrivalAirportCode\\\":\\\"LHR\\\",\\\"arrivalAirportTerminal\\\":\\\"2\\\",\\\"operatingAirline\\\":\\\"LO\\\",\\\"airEquipType\\\":\\\"738\\\",\\\"marketingAirline\\\":\\\"LO\\\",\\\"marriageGroup\\\":\\\"O\\\",\\\"mileage\\\":893,\\\"cabin\\\":\\\"Y\\\",\\\"cabinIsBasic\\\":true,\\\"brandId\\\":\\\"685421\\\",\\\"brandName\\\":\\\"ECONOMY SAVER\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"S1SAV14\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"carryOn\\\":true,\\\"allowPieces\\\":0},\\\"CHD\\\":{\\\"carryOn\\\":true,\\\"allowPieces\\\":0}},\\\"recheckBaggage\\\":false}],\\\"duration\\\":1020}],\\\"maxSeats\\\":7,\\\"paxCnt\\\":3,\\\"validatingCarrier\\\":\\\"LO\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"cons\\\":\\\"GTT\\\",\\\"fareType\\\":\\\"PUB\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"currencies\\\":[\\\"USD\\\"],\\\"currencyRates\\\":{\\\"USDUSD\\\":{\\\"from\\\":\\\"USD\\\",\\\"to\\\":\\\"USD\\\",\\\"rate\\\":1}},\\\"keys\\\":{\\\"travelport\\\":{\\\"traceId\\\":\\\"b58ab976-7391-40b0-a1d2-44a2821d44cf\\\",\\\"availabilitySources\\\":\\\"S,S\\\",\\\"type\\\":\\\"T\\\"},\\\"seatHoldSeg\\\":{\\\"trip\\\":0,\\\"segment\\\":0,\\\"seats\\\":7}},\\\"ngsFeatures\\\":{\\\"stars\\\":1,\\\"name\\\":\\\"ECONOMY SAVER\\\",\\\"list\\\":[]},\\\"meta\\\":{\\\"eip\\\":0,\\\"noavail\\\":false,\\\"searchId\\\":\\\"U0FMMTAxWTIxMDB8S0lWTE9OMjAyMS0wOS0xNg==\\\",\\\"lang\\\":\\\"en\\\",\\\"rank\\\":6,\\\"cheapest\\\":true,\\\"fastest\\\":false,\\\"best\\\":false,\\\"bags\\\":0,\\\"country\\\":\\\"us\\\"},\\\"price\\\":119.5,\\\"originRate\\\":1,\\\"stops\\\":[1],\\\"time\\\":[{\\\"departure\\\":\\\"2021-09-16 18:25\\\",\\\"arrival\\\":\\\"2021-09-17 09:25\\\"}],\\\"bagFilter\\\":\\\"\\\",\\\"airportChange\\\":false,\\\"technicalStopCnt\\\":0,\\\"duration\\\":[1020],\\\"totalDuration\\\":1020,\\\"topCriteria\\\":\\\"cheapest\\\",\\\"rank\\\":6}\",\n                    \"fq_last_ticket_date\": \"2021-03-11\",\n                    \"fq_json_booking\": null,\n                    \"fq_ticket_json\": null,\n                    \"fq_type_name\": \"Base\",\n                    \"fq_fare_type_name\": \"Public\",\n                    \"flight\": {\n                        \"fl_product_id\": 76,\n                        \"fl_trip_type_id\": 1,\n                        \"fl_cabin_class\": \"E\",\n                        \"fl_adults\": 2,\n                        \"fl_children\": 1,\n                        \"fl_infants\": 0,\n                        \"fl_trip_type_name\": \"One Way\",\n                        \"fl_cabin_class_name\": \"Economy\"\n                    },\n                    \"trips\": [\n                        {\n                            \"fqt_id\": 100,\n                            \"fqt_uid\": \"fqt6046483f5c6cf\",\n                            \"fqt_key\": null,\n                            \"fqt_duration\": 1020,\n                            \"segments\": [\n                                {\n                                    \"fqs_uid\": \"fqs6046483e349c6\",\n                                    \"fqs_departure_dt\": \"2021-09-16 18:25:00\",\n                                    \"fqs_arrival_dt\": \"2021-09-16 19:15:00\",\n                                    \"fqs_stop\": 0,\n                                    \"fqs_flight_number\": 516,\n                                    \"fqs_booking_class\": \"S\",\n                                    \"fqs_duration\": 110,\n                                    \"fqs_departure_airport_iata\": \"KIV\",\n                                    \"fqs_departure_airport_terminal\": \"\",\n                                    \"fqs_arrival_airport_iata\": \"WAW\",\n                                    \"fqs_arrival_airport_terminal\": \"\",\n                                    \"fqs_operating_airline\": \"LO\",\n                                    \"fqs_marketing_airline\": \"LO\",\n                                    \"fqs_air_equip_type\": \"DH4\",\n                                    \"fqs_marriage_group\": \"I\",\n                                    \"fqs_cabin_class\": \"Y\",\n                                    \"fqs_meal\": \"\",\n                                    \"fqs_fare_code\": \"S1SAV14\",\n                                    \"fqs_ticket_id\": null,\n                                    \"fqs_recheck_baggage\": 0,\n                                    \"fqs_mileage\": 508,\n                                    \"departureLocation\": \"Chisinau\",\n                                    \"arrivalLocation\": \"Warsaw\",\n                                    \"operating_airline\": \"LOT Polish Airlines\",\n                                    \"marketing_airline\": \"LOT Polish Airlines\",\n                                    \"baggages\": [\n                                        {\n                                            \"qsb_flight_pax_code_id\": 1,\n                                            \"qsb_flight_quote_segment_id\": 255,\n                                            \"qsb_airline_code\": null,\n                                            \"qsb_allow_pieces\": 0,\n                                            \"qsb_allow_weight\": null,\n                                            \"qsb_allow_unit\": null,\n                                            \"qsb_allow_max_weight\": null,\n                                            \"qsb_allow_max_size\": null\n                                        },\n                                        {\n                                            \"qsb_flight_pax_code_id\": 2,\n                                            \"qsb_flight_quote_segment_id\": 255,\n                                            \"qsb_airline_code\": null,\n                                            \"qsb_allow_pieces\": 0,\n                                            \"qsb_allow_weight\": null,\n                                            \"qsb_allow_unit\": null,\n                                            \"qsb_allow_max_weight\": null,\n                                            \"qsb_allow_max_size\": null\n                                        }\n                                    ]\n                                },\n                                {\n                                    \"fqs_uid\": \"fqs6046483e37fc7\",\n                                    \"fqs_departure_dt\": \"2021-09-17 07:30:00\",\n                                    \"fqs_arrival_dt\": \"2021-09-17 09:25:00\",\n                                    \"fqs_stop\": 0,\n                                    \"fqs_flight_number\": 281,\n                                    \"fqs_booking_class\": \"S\",\n                                    \"fqs_duration\": 175,\n                                    \"fqs_departure_airport_iata\": \"WAW\",\n                                    \"fqs_departure_airport_terminal\": \"\",\n                                    \"fqs_arrival_airport_iata\": \"LHR\",\n                                    \"fqs_arrival_airport_terminal\": \"2\",\n                                    \"fqs_operating_airline\": \"LO\",\n                                    \"fqs_marketing_airline\": \"LO\",\n                                    \"fqs_air_equip_type\": \"738\",\n                                    \"fqs_marriage_group\": \"O\",\n                                    \"fqs_cabin_class\": \"Y\",\n                                    \"fqs_meal\": \"\",\n                                    \"fqs_fare_code\": \"S1SAV14\",\n                                    \"fqs_ticket_id\": null,\n                                    \"fqs_recheck_baggage\": 0,\n                                    \"fqs_mileage\": 893,\n                                    \"departureLocation\": \"Warsaw\",\n                                    \"arrivalLocation\": \"London\",\n                                    \"operating_airline\": \"LOT Polish Airlines\",\n                                    \"marketing_airline\": \"LOT Polish Airlines\",\n                                    \"baggages\": [\n                                        {\n                                            \"qsb_flight_pax_code_id\": 1,\n                                            \"qsb_flight_quote_segment_id\": 256,\n                                            \"qsb_airline_code\": null,\n                                            \"qsb_allow_pieces\": 0,\n                                            \"qsb_allow_weight\": null,\n                                            \"qsb_allow_unit\": null,\n                                            \"qsb_allow_max_weight\": null,\n                                            \"qsb_allow_max_size\": null\n                                        },\n                                        {\n                                            \"qsb_flight_pax_code_id\": 2,\n                                            \"qsb_flight_quote_segment_id\": 256,\n                                            \"qsb_airline_code\": null,\n                                            \"qsb_allow_pieces\": 0,\n                                            \"qsb_allow_weight\": null,\n                                            \"qsb_allow_unit\": null,\n                                            \"qsb_allow_max_weight\": null,\n                                            \"qsb_allow_max_size\": null\n                                        }\n                                    ]\n                                }\n                            ]\n                        }\n                    ],\n                    \"pax_prices\": [\n                        {\n                            \"qpp_fare\": \"58.00\",\n                            \"qpp_tax\": \"61.50\",\n                            \"qpp_system_mark_up\": \"0.00\",\n                            \"qpp_agent_mark_up\": \"0.00\",\n                            \"qpp_origin_fare\": \"58.00\",\n                            \"qpp_origin_currency\": \"USD\",\n                            \"qpp_origin_tax\": \"61.50\",\n                            \"qpp_client_currency\": \"USD\",\n                            \"qpp_client_fare\": \"58.00\",\n                            \"qpp_client_tax\": \"61.50\",\n                            \"paxType\": \"ADT\"\n                        },\n                        {\n                            \"qpp_fare\": \"43.00\",\n                            \"qpp_tax\": \"61.50\",\n                            \"qpp_system_mark_up\": \"0.00\",\n                            \"qpp_agent_mark_up\": \"0.00\",\n                            \"qpp_origin_fare\": \"43.00\",\n                            \"qpp_origin_currency\": \"USD\",\n                            \"qpp_origin_tax\": \"61.50\",\n                            \"qpp_client_currency\": \"USD\",\n                            \"qpp_client_fare\": \"43.00\",\n                            \"qpp_client_tax\": \"61.50\",\n                            \"paxType\": \"CHD\"\n                        }\n                    ],\n                    \"paxes\": [\n                        {\n                            \"fp_uid\": \"fp6046483b5f034\",\n                            \"fp_pax_id\": null,\n                            \"fp_pax_type\": \"ADT\",\n                            \"fp_first_name\": null,\n                            \"fp_last_name\": null,\n                            \"fp_middle_name\": null,\n                            \"fp_dob\": null\n                        },\n                        {\n                            \"fp_uid\": \"fp6046483b61c29\",\n                            \"fp_pax_id\": null,\n                            \"fp_pax_type\": \"ADT\",\n                            \"fp_first_name\": null,\n                            \"fp_last_name\": null,\n                            \"fp_middle_name\": null,\n                            \"fp_dob\": null\n                        },\n                        {\n                            \"fp_uid\": \"fp6046483b64835\",\n                            \"fp_pax_id\": null,\n                            \"fp_pax_type\": \"CHD\",\n                            \"fp_first_name\": null,\n                            \"fp_last_name\": null,\n                            \"fp_middle_name\": null,\n                            \"fp_dob\": null\n                        }\n                    ]\n                },\n                \"product\": {\n                    \"pr_gid\": \"\",\n                    \"pr_type_id\": 1,\n                    \"pr_name\": \"\",\n                    \"pr_lead_id\": 513111,\n                    \"pr_description\": \"\",\n                    \"pr_status_id\": null,\n                    \"pr_service_fee_percent\": null,\n                    \"holder\": {\n                        \"ph_first_name\": \"test\",\n                        \"ph_last_name\": \"test\",\n                        \"ph_email\": \"test@test.test\",\n                        \"ph_phone_number\": \"+19074861000\"\n                    }\n                },\n                \"productQuoteOptions\": [],\n                \"origin\": {\n                    \"pq_gid\": \"eebad5110d96b60fee6d2084c866ce28\",\n                    \"pq_name\": \"ROO.ST & ROO.ST\",\n                    \"pq_order_id\": 526,\n                    \"pq_description\": null,\n                    \"pq_status_id\": 8,\n                    \"pq_price\": 7065.34,\n                    \"pq_origin_price\": 6292.6,\n                    \"pq_client_price\": 7065.34,\n                    \"pq_service_fee_sum\": 238.93,\n                    \"pq_origin_currency\": \"USD\",\n                    \"pq_client_currency\": \"USD\",\n                    \"pq_status_name\": \"Error\",\n                    \"pq_files\": [],\n                    \"data\": {\n                        \"hq_hash_key\": \"f293c1629f74b2938d41cdea92769ffe\",\n                        \"hq_destination_name\": \"Chisinau\",\n                        \"hq_hotel_name\": \"Cosmos Hotel\",\n                        \"hq_request_hash\": \"9de433bc355aed187eca25f7628a480e\",\n                        \"hq_booking_id\": null,\n                        \"hq_json_booking\": null,\n                        \"hq_check_in_date\": \"2021-09-10\",\n                        \"hq_check_out_date\": \"2021-09-30\",\n                        \"hq_nights\": 20,\n                        \"hotel_request\": {\n                            \"ph_check_in_date\": \"2021-09-10\",\n                            \"ph_check_out_date\": \"2021-09-30\",\n                            \"ph_destination_code\": \"KIV\",\n                            \"ph_destination_label\": \"Moldova, Chisinau\",\n                            \"ph_holder_name\": null,\n                            \"ph_holder_surname\": null,\n                            \"destination_city\": \"Chisinau\"\n                        },\n                        \"hotel\": {\n                            \"hl_name\": \"Cosmos Hotel\",\n                            \"hl_star\": \"\",\n                            \"hl_category_name\": \"3 STARS\",\n                            \"hl_destination_name\": \"Chisinau\",\n                            \"hl_zone_name\": \"Chisinau\",\n                            \"hl_country_code\": \"MD\",\n                            \"hl_state_code\": \"MD\",\n                            \"hl_description\": \"The hotel is situated in the heart of Chisinau, the capital of Moldova. It is perfectly located for access to the business centre, cultural institutions and much more. Chisinau Airport is only 15 minutes away and the railway station is less than 5 minutes away from the hotel.\\n\\nThe city hotel offers a choice of 150 rooms, 24-hour reception and check-out services in the lobby, luggage storage, a hotel safe, currency exchange facility and a cloakroom. There is lift access to the upper floors as well as an on-site restaurant and conference facilities. Internet access, a laundry service (fees apply) and free parking in the car park are also on offer to guests during their stay.\\n\\nAll the rooms are furnished with double or king-size beds and provide an en suite bathroom with a shower. Air conditioning, central heating, satellite TV, a telephone, mini fridge, radio and free wireless Internet access are also on offer.\\n\\nThere is a golf course about 12 km from the hotel.\\n\\nThe hotel's restaurant offers a wide selection of local and European cuisine. Breakfast is served as a buffet and lunch and dinner can be chosen à la carte.\",\n                            \"hl_address\": \"NEGRUZZI, 2\",\n                            \"hl_postal_code\": \"MD2001\",\n                            \"hl_city\": \"CHISINAU\",\n                            \"hl_email\": \"info@hotel-cosmos.com\",\n                            \"hl_web\": null,\n                            \"hl_phone_list\": [\n                                {\n                                    \"type\": \"PHONEBOOKING\",\n                                    \"number\": \"+37322890054\"\n                                },\n                                {\n                                    \"type\": \"PHONEHOTEL\",\n                                    \"number\": \"+37322837505\"\n                                },\n                                {\n                                    \"type\": \"FAXNUMBER\",\n                                    \"number\": \"+37322542744\"\n                                }\n                            ],\n                            \"hl_image_list\": [\n                                {\n                                    \"url\": \"14/148030/148030a_hb_a_001.jpg\",\n                                    \"type\": \"GEN\"\n                                }\n                            ],\n                            \"hl_image_base_url\": null,\n                            \"json_booking\": null\n                        },\n                        \"rooms\": [\n                            {\n                                \"hqr_room_name\": \"Room Standard\",\n                                \"hqr_class\": \"NOR\",\n                                \"hqr_amount\": 188.78,\n                                \"hqr_currency\": \"USD\",\n                                \"hqr_board_name\": \"BED AND BREAKFAST\",\n                                \"hqr_rooms\": 1,\n                                \"hqr_adults\": 1,\n                                \"hqr_children\": null,\n                                \"hqr_cancellation_policies\": []\n                            },\n                            {\n                                \"hqr_room_name\": \"Room Standard\",\n                                \"hqr_class\": \"NRF\",\n                                \"hqr_amount\": 125.85,\n                                \"hqr_currency\": \"USD\",\n                                \"hqr_board_name\": \"ROOM ONLY\",\n                                \"hqr_rooms\": 1,\n                                \"hqr_adults\": 2,\n                                \"hqr_children\": null,\n                                \"hqr_cancellation_policies\": [\n                                    {\n                                        \"from\": \"2021-12-31T21:59:00:00:00\",\n                                        \"amount\": 72.46\n                                    },\n                                    {\n                                        \"from\": \"2021-12-05T21:59:00+00:00\",\n                                        \"amount\": 134.92\n                                    }\n                                ]\n                            }\n                        ]\n                    },\n                    \"product\": {\n                        \"pr_gid\": \"337b7f7fe27143e543c31b0b60688de0\",\n                        \"pr_type_id\": 2,\n                        \"pr_name\": null,\n                        \"pr_lead_id\": null,\n                        \"pr_description\": null,\n                        \"pr_status_id\": null,\n                        \"pr_service_fee_percent\": null,\n                        \"holder\": {\n                            \"ph_first_name\": \"Test 2\",\n                            \"ph_last_name\": \"Test 2\",\n                            \"ph_middle_name\": null,\n                            \"ph_email\": \"test+2@test.test\",\n                            \"ph_phone_number\": \"+19074861000\"\n                        }\n                    },\n                    \"productQuoteOptions\": []\n                }\n            }\n        ],\n        \"lead_data\": [\n           {\n               \"ld_field_key\": \"kayakclickid\",\n               \"ld_field_value\": \"example_value132\"\n           }\n        ]\n    },\n    \"technical\": {\n        \"action\": \"v2/offer/view\",\n        \"response_id\": 496,\n        \"request_dt\": \"2021-03-08 15:57:33\",\n        \"response_dt\": \"2021-03-08 15:57:33\",\n        \"execution_time\": 0.104,\n        \"memory_usage\": 1290648\n    },\n    \"request\": {\n        \"offerGid\": \"ea6dc06421db46b5a77e8505d0934f38\"\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n    \"status\": 422,\n    \"message\": \"Error\",\n    \"errors\": [\n        \"Not found Offer\"\n    ],\n    \"code\": \"18302\",\n    \"technical\": {\n        \"action\": \"v2/offer/view\",\n        \"response_id\": 11933860,\n        \"request_dt\": \"2020-02-03 13:07:10\",\n        \"response_dt\": \"2020-02-03 13:07:10\",\n        \"execution_time\": 0.015,\n        \"memory_usage\": 151792\n    },\n    \"request\": {\n        \"offerGid\": \"04d3fe3fc74d0514ee93e208a5x2bcf90\",\n        \"visitor\": {\n            \"id\": \"hdsjfghsd5489tertwhf289hfgkewr\",\n            \"ipAddress\": \"12.12.12.12\",\n            \"userAgent\": \"mozilea/asdfsdf/ as/dfgsdf gsdf gsdgf/ds\"\n        }\n    }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n    \"status\": 422,\n    \"message\": \"Validation error\",\n    \"errors\": {\n       \"visitor.ipAddress\": [\n            \"Ip Address cant be array.\"\n        ]\n    },\n    \"code\": \"18301\",\n    \"technical\": {\n         \"action\": \"v2/offer/view\",\n         \"response_id\": 11933854,\n         \"request_dt\": \"2020-02-03 12:44:13\",\n         \"response_dt\": \"2020-02-03 12:44:13\",\n         \"execution_time\": 0.013,\n         \"memory_usage\": 127680\n    },\n    \"request\": {\n         \"offerGid\": \"04d3fe3fc74d0514ee93e208a52bcf90\",\n         \"visitor\": {\n             \"id\": \"hdsjfghsd5489tertwhf289hfgkewr\",\n             \"ipAddress\": [],\n             \"userAgent\": \"mozilea/asdfsdf/ as/dfgsdf gsdf gsdgf/ds\"\n         }\n    }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n{\n    \"status\": 400,\n    \"message\": \"Load data error\",\n    \"errors\": [\n        \"Not found Offer data on POST request\"\n    ],\n    \"code\": \"18300\",\n    \"technical\": {\n        \"action\": \"v2/offer/view\",\n        \"response_id\": 11933856,\n        \"request_dt\": \"2020-02-03 12:49:20\",\n        \"response_dt\": \"2020-02-03 12:49:20\",\n        \"execution_time\": 0.017,\n        \"memory_usage\": 114232\n    },\n    \"request\": []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/OfferController.php",
    "groupTitle": "Offer"
  },
  {
    "type": "post",
    "url": "/v2/order/cancel",
    "title": "Cancel Order",
    "version": "0.2.0",
    "name": "CancelOrder",
    "group": "Order",
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
            "optional": false,
            "field": "gid",
            "description": "<p>Order gid</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n{\n    \"gid\": \"04d3fe3fc74d0514ee93e208a52bcf90\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n   \"status\": 200,\n   \"message\": \"OK\",\n   \"code\": 0,\n   \"technical\": {\n       \"action\": \"v2/order/cancel\",\n       \"response_id\": 15629,\n       \"request_dt\": \"2021-04-01 09:03:11\",\n       \"response_dt\": \"2021-04-01 09:03:11\",\n       \"execution_time\": 0.019,\n       \"memory_usage\": 186192\n   },\n   \"request\": {\n      \"gid\": \"04d3fe3fc74d0514ee93e208a52bcf90\"\n   }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n{\n      \"status\": 400,\n      \"message\": \"Load data error\",\n      \"errors\": [\n          \"Not found data on POST request\"\n      ],\n      \"code\": 10,\n      \"request\": {\n          ...\n      },\n      \"technical\": {\n          ...\n     }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n    \"status\": 422,\n    \"message\": \"Validation error\",\n    \"errors\": {\n         \"gid\": [\n           \"Gid is invalid.\"\n        ]\n    },\n    \"code\": 20,\n    \"technical\": {\n          ...\n    },\n    \"request\": {\n          ...\n    }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n    \"status\": 422,\n    \"message\": \"Error\",\n    \"errors\": {\n        \"The order is not available for processing.\"\n    },\n    \"code\": 30,\n    \"technical\": {\n          ...\n    },\n    \"request\": {\n          ...\n    }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n    \"status\": 422,\n    \"message\": \"Error\",\n    \"errors\": {\n        \"Unable to process flight cancellation.\"\n    },\n    \"code\": 40,\n    \"technical\": {\n          ...\n    },\n    \"request\": {\n          ...\n    }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n    \"status\": 422,\n    \"message\": \"Error\",\n    \"errors\": {\n        \"Unable to process hotel cancellation.\"\n    },\n    \"code\": 50,\n    \"technical\": {\n          ...\n    },\n    \"request\": {\n          ...\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/OrderController.php",
    "groupTitle": "Order"
  },
  {
    "type": "post",
    "url": "/v2/order/create",
    "title": "Create Order",
    "version": "0.2.0",
    "name": "CreateOrder",
    "group": "Order",
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
          "content": "{\n     \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n     \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n }",
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
            "size": "max 10",
            "optional": false,
            "field": "sourceCid",
            "description": "<p>Source cid</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 32",
            "optional": false,
            "field": "offerGid",
            "description": "<p>Offer gid</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 5",
            "optional": false,
            "field": "languageId",
            "description": "<p>Language Id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 2",
            "optional": false,
            "field": "marketCountry",
            "description": "<p>Market Country</p>"
          },
          {
            "group": "Parameter",
            "type": "Object[]",
            "optional": false,
            "field": "productQuotes",
            "description": "<p>Product Quotes</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 32",
            "optional": false,
            "field": "productQuotes.gid",
            "description": "<p>Product Quote Gid</p>"
          },
          {
            "group": "Parameter",
            "type": "Object[]",
            "optional": false,
            "field": "productQuotes.productOptions",
            "description": "<p>Quote Options</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": false,
            "field": "productQuotes.productOptions.productOptionKey",
            "description": "<p>Product option key</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": true,
            "field": "productQuotes.productOptions.name",
            "description": "<p>Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "productQuotes.productOptions.description",
            "description": "<p>Description</p>"
          },
          {
            "group": "Parameter",
            "type": "Decimal",
            "optional": false,
            "field": "productQuotes.productOptions.price",
            "description": "<p>Price</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "productQuotes.productOptions.json_data",
            "description": "<p>Original data</p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": false,
            "field": "productQuotes.productHolder",
            "description": "<p>Holder Info</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": false,
            "field": "productQuotes.productHolder.firstName",
            "description": "<p>Holder first name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": false,
            "field": "productQuotes.productHolder.lastName",
            "description": "<p>Holder last name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": true,
            "field": "productQuotes.productHolder.middleName",
            "description": "<p>Holder middle name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 100",
            "optional": false,
            "field": "productQuotes.productHolder.email",
            "description": "<p>Holder email</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 20",
            "optional": false,
            "field": "productQuotes.productHolder.phone",
            "description": "<p>Holder phone</p>"
          },
          {
            "group": "Parameter",
            "type": "Object[]",
            "optional": true,
            "field": "productQuotes.productHolder.data",
            "description": "<p>Quote options</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "productQuotes.productHolder.data.segment_uid",
            "description": "<p>Segment uid</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "productQuotes.productHolder.data.pax_uid",
            "description": "<p>Pax uid</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "productQuotes.productHolder.data.trip_uid",
            "description": "<p>Trip uid</p>"
          },
          {
            "group": "Parameter",
            "type": "Decimal",
            "optional": false,
            "field": "productQuotes.productHolder.data.total",
            "description": "<p>Total</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 5",
            "optional": false,
            "field": "productQuotes.productHolder.data.currency",
            "description": "<p>Currency</p>"
          },
          {
            "group": "Parameter",
            "type": "Decimal",
            "optional": false,
            "field": "productQuotes.productHolder.data.usd_total",
            "description": "<p>Total price in usd</p>"
          },
          {
            "group": "Parameter",
            "type": "Decimal",
            "optional": false,
            "field": "productQuotes.productHolder.data.base_price",
            "description": "<p>Base price in usd</p>"
          },
          {
            "group": "Parameter",
            "type": "Decimal",
            "optional": false,
            "field": "productQuotes.productHolder.data.markup_amount",
            "description": "<p>Markup amount</p>"
          },
          {
            "group": "Parameter",
            "type": "Decimal",
            "optional": false,
            "field": "productQuotes.productHolder.data.usd_base_price",
            "description": "<p>Base price in usd</p>"
          },
          {
            "group": "Parameter",
            "type": "Decimal",
            "optional": false,
            "field": "productQuotes.productHolder.data.usd_markup_amount",
            "description": "<p>Markup amount in usd</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 255",
            "optional": false,
            "field": "productQuotes.productHolder.data.display_name",
            "description": "<p>Display name</p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": false,
            "field": "payment",
            "description": "<p>Payment</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "payment.type",
            "description": "<p>Type</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 255",
            "optional": false,
            "field": "payment.transactionId",
            "description": "<p>Transaction Id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "format yyyy-mm-dd",
            "optional": false,
            "field": "payment.date",
            "description": "<p>Date</p>"
          },
          {
            "group": "Parameter",
            "type": "Decimal",
            "optional": false,
            "field": "payment.amount",
            "description": "<p>Amount</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 3",
            "optional": false,
            "field": "payment.currency",
            "description": "<p>Currency</p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": true,
            "field": "billingInfo",
            "description": "<p>BillingInfo</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": false,
            "field": "billingInfo.first_name",
            "description": "<p>First Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": false,
            "field": "billingInfo.last_name",
            "description": "<p>Last Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": false,
            "field": "billingInfo.middle_name",
            "description": "<p>Middle Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": false,
            "field": "billingInfo.address",
            "description": "<p>Address</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 2",
            "optional": false,
            "field": "billingInfo.country_id",
            "description": "<p>Country Id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": false,
            "field": "billingInfo.city",
            "description": "<p>City</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 40",
            "optional": false,
            "field": "billingInfo.state",
            "description": "<p>State</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 10",
            "optional": false,
            "field": "billingInfo.zip",
            "description": "<p>Zip</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 20",
            "optional": false,
            "field": "billingInfo.phone",
            "description": "<p>Phone <code>Deprecated</code></p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 160",
            "optional": false,
            "field": "billingInfo.email",
            "description": "<p>Email <code>Deprecated</code></p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": false,
            "field": "creditCard",
            "description": "<p>Credit Card</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": true,
            "field": "creditCard.holder_name",
            "description": "<p>Holder Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 20",
            "optional": false,
            "field": "creditCard.number",
            "description": "<p>Credit Card Number</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "creditCard.type",
            "description": "<p>Credit Card type</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 18",
            "optional": false,
            "field": "creditCard.expiration",
            "description": "<p>Credit Card expiration</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 4",
            "optional": false,
            "field": "creditCard.cvv",
            "description": "<p>Credit Card cvv</p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": true,
            "field": "Tips",
            "description": "<p>Tips</p>"
          },
          {
            "group": "Parameter",
            "type": "Decimal",
            "optional": false,
            "field": "Tips.total_amount",
            "description": "<p>Total Amount</p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": false,
            "field": "Paxes[]",
            "description": "<p>Paxes</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "Paxes.uid",
            "description": "<p>Uid</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 40",
            "optional": true,
            "field": "Paxes.first_name",
            "description": "<p>First Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 40",
            "optional": true,
            "field": "Paxes.last_name",
            "description": "<p>Last Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 40",
            "optional": true,
            "field": "Paxes.middle_name",
            "description": "<p>Middle Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 5",
            "optional": true,
            "field": "Paxes.nationality",
            "description": "<p>Nationality</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 1",
            "optional": true,
            "field": "Paxes.gender",
            "description": "<p>Gender</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "format yyyy-mm-dd",
            "optional": true,
            "field": "Paxes.birth_date",
            "description": "<p>Birth Date</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 100",
            "optional": true,
            "field": "Paxes.email",
            "description": "<p>Email</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 5",
            "optional": true,
            "field": "Paxes.language",
            "description": "<p>Language</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 5",
            "optional": true,
            "field": "Paxes.citizenship",
            "description": "<p>Citizenship</p>"
          },
          {
            "group": "Parameter",
            "type": "Object[]",
            "optional": false,
            "field": "contactsInfo",
            "description": "<p>BillingInfo</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": false,
            "field": "contactsInfo.first_name",
            "description": "<p>First Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": true,
            "field": "contactsInfo.last_name",
            "description": "<p>Last Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": true,
            "field": "contactsInfo.middle_name",
            "description": "<p>Middle Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 20",
            "optional": true,
            "field": "contactsInfo.phone",
            "description": "<p>Phone number</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 100",
            "optional": false,
            "field": "contactsInfo.email",
            "description": "<p>Email</p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": false,
            "field": "Request",
            "description": "<p>Request Data for BO</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n    \"sourceCid\": \"OVA102\",\n    \"offerGid\": \"73c8bf13111feff52794883446461740\",\n    \"languageId\": \"en-US\",\n    \"marketCountry\": \"US\",\n    \"productQuotes\": [\n        {\n            \"gid\": \"aebf921f5a64a7ac98d4942ace67e498\",\n            \"productOptions\": [\n                {\n                    \"productOptionKey\": \"travelGuard\",\n                    \"name\": \"Travel Guard\",\n                    \"description\": \"\",\n                    \"price\": 20,\n                    \"json_data\": \"\",\n                    \"data\": [\n                        {\n                            \"segment_uid\": \"fqs604635abf02ae\",\n                            \"pax_uid\": \"fp604635abe9c6a\",\n                            \"trip_uid\": \"fqt604635abed0e0\",\n                            \"total\": 2.00,\n                            \"currency\": \"USD\",\n                            \"usd_total\": 2.00,\n                            \"base_price\": 2.00,\n                            \"markup_amount\": 0,\n                            \"usd_base_price\": 2.00,\n                            \"usd_markup_amount\": 0,\n                            \"display_name\": \"Seat: 18E, CQ 7602\"\n                        }\n                    ]\n\n                }\n            ],\n            \"productHolder\": {\n                \"firstName\": \"Test\",\n                \"lastName\": \"Test\",\n                \"middleName\": \"\",\n                \"email\": \"test@test.test\",\n                \"phone\": \"+19074861000\"\n            }\n        },\n        {\n            \"gid\": \"6fcfc43e977dabffe6a979ebdaddfvr2\",\n            \"productHolder\": {\n                \"firstName\": \"Test 2\",\n                \"lastName\": \"Test 2\",\n                \"email\": \"test2@test.test\",\n                \"phone\": \"+19074861002\"\n            }\n        }\n    ],\n    \"payment\": {\n        \"type\": \"card\",\n        \"transactionId\": 1234567890,\n        \"date\": \"2021-03-20\",\n        \"amount\": 821.49,\n        \"currency\": \"USD\"\n    },\n    \"billingInfo\": {\n        \"first_name\": \"Barbara Elmore\",\n        \"middle_name\": \"\",\n        \"last_name\": \"T\",\n        \"address\": \"1013 Weda Cir\",\n        \"country_id\": \"US\",\n        \"city\": \"Mayfield\",\n        \"state\": \"KY\",\n        \"zip\": \"99999\",\n        \"phone\": \"+19074861000\",\n        \"email\": \"mike.kane@techork.com\"\n    },\n    \"creditCard\": {\n        \"holder_name\": \"Barbara Elmore\",\n        \"number\": \"1111111111111111\",\n        \"type\": \"Visa\",\n        \"expiration\": \"07 / 23\",\n        \"cvv\": \"324\"\n    },\n    \"tips\": {\n        \"total_amount\": 20\n    },\n    \"paxes\": [\n        {\n            \"uid\": \"fp6047195e67b7a\",\n            \"first_name\": \"Test name\",\n            \"last_name\": \"Test last name\",\n            \"middle_name\": \"Test middle name\",\n            \"nationality\": \"US\",\n            \"gender\": \"M\",\n            \"birth_date\": \"1963-04-07\",\n            \"email\": \"mike.kane@techork.com\",\n            \"language\": \"en-US\",\n            \"citizenship\": \"US\"\n        }\n    ],\n    \"contactsInfo\": [\n        {\n            \"first_name\": \"Barbara\",\n            \"last_name\": \"Elmore\",\n            \"middle_name\": \"\",\n            \"phone\": \"+19074861000\",\n            \"email\": \"barabara@test.com\"\n        },\n        {\n            \"first_name\": \"John\",\n            \"last_name\": \"Doe\",\n            \"middle_name\": \"\",\n            \"phone\": \"+19074865678\",\n            \"email\": \"john@test.com\"\n        }\n    ],\n    \"Request\": {\n        \"offerGid\": \"85a06c376a083f47e56b286b1265c160\",\n        \"offerUid\": \"of60264c1484090\",\n        \"apiKey\": \"038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826\",\n        \"source\": \"I1B1L1\",\n        \"subSource\": \"-\",\n        \"totalOrderAmount\": 821.49,\n        \"FlightRequest\": {\n            \"productGid\": \"c6ae37ae73380c773cadf28fc0af9db2\",\n            \"uid\": \"OE96040\",\n            \"email\": \"mike.kane@techork.com\",\n            \"marker\": null,\n            \"client_ip_address\": \"92.115.180.30\",\n            \"trip_protection_amount\": \"0\",\n            \"insurance_code\": \"P7\",\n            \"is_facilitate\": 0,\n            \"delay_change\": false,\n            \"is_b2b\": false,\n            \"uplift\": false,\n            \"alipay\": false,\n            \"user_country\": \"us\",\n            \"user_language\": \"en-US\",\n            \"user_time_format\": \"h:mm a\",\n            \"user_month_date_format\": {\n                \"long\": \"EEE MMM d\",\n                \"short\": \"MMM d\",\n                \"fullDateLong\": \"EEE MMM d\",\n                \"fullDateShort\": \"MMM d, YYYY\"\n            },\n            \"currency_symbol\": \"$\",\n            \"pnr\": null\n        },\n        \"HotelRequest\": {\n            \"productGid\": \"cdd82f2616f600f71a68e9399c51276e\"\n        },\n        \"DriverRequest\": {\n            \"productGid\": \"cdd82f2616f600f71a68e9399c51276e\"\n        },\n        \"AttractionRequest\": {\n            \"productGid\": \"cdd82f2616f600f71a68e9399c51276e\"\n        },\n        \"CruiseRequest\": {\n            \"productGid\": \"cdd82f2616f600f71a68e9399c51276e\"\n        },\n        \"Card\": {\n            \"user_id\": null,\n            \"nickname\": \"B****** E***** T\",\n            \"number\": \"************6444\",\n            \"type\": \"Visa\",\n            \"expiration_date\": \"07 / 2023\",\n            \"first_name\": \"Barbara Elmore\",\n            \"middle_name\": \"\",\n            \"last_name\": \"T\",\n            \"address\": \"1013 Weda Cir\",\n            \"country_id\": \"US\",\n            \"city\": \"Mayfield\",\n            \"state\": \"KY\",\n            \"zip\": \"99999\",\n            \"phone\": \"+19074861000\",\n            \"deleted\": null,\n            \"cvv\": \"***\",\n            \"auth_attempts\": null,\n            \"country\": \"United States\",\n            \"calling\": \"\",\n            \"client_ip_address\": \"92.115.180.30\",\n            \"email\": \"mike.kane@techork.com\",\n            \"document\": null\n        },\n        \"AirRouting\": {\n            \"results\": [\n                {\n                    \"gds\": \"S\",\n                    \"key\": \"2_T1ZBMTAxKlkxMDAwL0xBWFRQRTIwMjEtMDUtMTMvVFBFTEFYMjAyMS0wNi0yMCpQUn4jUFIxMDMjUFI4OTAjUFI4OTEjUFIxMDJ+bGM6ZW5fdXM=\",\n                    \"pcc\": \"8KI0\",\n                    \"cons\": \"GTT\",\n                    \"keys\": {\n                        \"services\": {\n                            \"support\": {\n                                \"amount\": 75\n                            }\n                        },\n                        \"seatHoldSeg\": {\n                            \"trip\": 0,\n                            \"seats\": 9,\n                            \"segment\": 0\n                        },\n                        \"verification\": {\n                            \"headers\": {\n                                \"X-Client-Ip\": \"92.115.180.30\",\n                                \"X-Kiv-Cust-Ip\": \"92.115.180.30\",\n                                \"X-Kiv-Cust-ipv\": \"0\",\n                                \"X-Kiv-Cust-ssid\": \"ovago-dev-0484692\",\n                                \"X-Kiv-Cust-direct\": \"true\",\n                                \"X-Kiv-Cust-browser\": \"desktop\"\n                            }\n                        }\n                    },\n                    \"meta\": {\n                        \"eip\": 0,\n                        \"bags\": 2,\n                        \"best\": false,\n                        \"lang\": \"en\",\n                        \"rank\": 6,\n                        \"group1\": \"LAXTPE:PRPR:0:TPELAX:PRPR:0:767.75\",\n                        \"country\": \"us\",\n                        \"fastest\": false,\n                        \"noavail\": false,\n                        \"cheapest\": true,\n                        \"searchId\": \"T1ZBMTAxWTEwMDB8TEFYVFBFMjAyMS0wNS0xM3xUUEVMQVgyMDIxLTA2LTIw\"\n                    },\n                    \"cabin\": \"Y\",\n                    \"trips\": [\n                        {\n                            \"tripId\": 1,\n                            \"duration\": 1150,\n                            \"segments\": [\n                                {\n                                    \"meal\": \"D\",\n                                    \"stop\": 0,\n                                    \"cabin\": \"Y\",\n                                    \"stops\": [],\n                                    \"baggage\": {\n                                        \"ADT\": {\n                                            \"carryOn\": true,\n                                            \"airlineCode\": \"PR\",\n                                            \"allowPieces\": 2,\n                                            \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                            \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\"\n                                        }\n                                    },\n                                    \"mileage\": 7305,\n                                    \"duration\": 870,\n                                    \"fareCode\": \"U9XBUS\",\n                                    \"segmentId\": 1,\n                                    \"arrivalTime\": \"2021-05-15 04:00\",\n                                    \"airEquipType\": \"773\",\n                                    \"bookingClass\": \"U\",\n                                    \"flightNumber\": \"103\",\n                                    \"departureTime\": \"2021-05-13 22:30\",\n                                    \"marriageGroup\": \"O\",\n                                    \"recheckBaggage\": false,\n                                    \"marketingAirline\": \"PR\",\n                                    \"operatingAirline\": \"PR\",\n                                    \"arrivalAirportCode\": \"MNL\",\n                                    \"departureAirportCode\": \"LAX\",\n                                    \"arrivalAirportTerminal\": \"2\",\n                                    \"departureAirportTerminal\": \"B\"\n                                },\n                                {\n                                    \"meal\": \"B\",\n                                    \"stop\": 0,\n                                    \"cabin\": \"Y\",\n                                    \"stops\": [],\n                                    \"baggage\": {\n                                        \"ADT\": {\n                                            \"carryOn\": true,\n                                            \"airlineCode\": \"PR\",\n                                            \"allowPieces\": 2,\n                                            \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                            \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\"\n                                        }\n                                    },\n                                    \"mileage\": 728,\n                                    \"duration\": 130,\n                                    \"fareCode\": \"U9XBUS\",\n                                    \"segmentId\": 2,\n                                    \"arrivalTime\": \"2021-05-15 08:40\",\n                                    \"airEquipType\": \"321\",\n                                    \"bookingClass\": \"U\",\n                                    \"flightNumber\": \"890\",\n                                    \"departureTime\": \"2021-05-15 06:30\",\n                                    \"marriageGroup\": \"I\",\n                                    \"recheckBaggage\": false,\n                                    \"marketingAirline\": \"PR\",\n                                    \"operatingAirline\": \"PR\",\n                                    \"arrivalAirportCode\": \"TPE\",\n                                    \"departureAirportCode\": \"MNL\",\n                                    \"arrivalAirportTerminal\": \"1\",\n                                    \"departureAirportTerminal\": \"1\"\n                                }\n                            ]\n                        },\n                        {\n                            \"tripId\": 2,\n                            \"duration\": 1490,\n                            \"segments\": [\n                                {\n                                    \"meal\": \"H\",\n                                    \"stop\": 0,\n                                    \"cabin\": \"Y\",\n                                    \"stops\": [],\n                                    \"baggage\": {\n                                        \"ADT\": {\n                                            \"carryOn\": true,\n                                            \"airlineCode\": \"PR\",\n                                            \"allowPieces\": 2,\n                                            \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                            \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\"\n                                        }\n                                    },\n                                    \"mileage\": 728,\n                                    \"duration\": 145,\n                                    \"fareCode\": \"U9XBUS\",\n                                    \"segmentId\": 1,\n                                    \"arrivalTime\": \"2021-06-20 12:05\",\n                                    \"airEquipType\": \"321\",\n                                    \"bookingClass\": \"U\",\n                                    \"flightNumber\": \"891\",\n                                    \"departureTime\": \"2021-06-20 09:40\",\n                                    \"marriageGroup\": \"O\",\n                                    \"recheckBaggage\": false,\n                                    \"marketingAirline\": \"PR\",\n                                    \"operatingAirline\": \"PR\",\n                                    \"arrivalAirportCode\": \"MNL\",\n                                    \"departureAirportCode\": \"TPE\",\n                                    \"arrivalAirportTerminal\": \"2\",\n                                    \"departureAirportTerminal\": \"1\"\n                                },\n                                {\n                                    \"meal\": \"D\",\n                                    \"stop\": 0,\n                                    \"cabin\": \"Y\",\n                                    \"stops\": [],\n                                    \"baggage\": {\n                                        \"ADT\": {\n                                            \"carryOn\": true,\n                                            \"airlineCode\": \"PR\",\n                                            \"allowPieces\": 2,\n                                            \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                            \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\"\n                                        }\n                                    },\n                                    \"mileage\": 7305,\n                                    \"duration\": 805,\n                                    \"fareCode\": \"U9XBUS\",\n                                    \"segmentId\": 2,\n                                    \"arrivalTime\": \"2021-06-20 19:30\",\n                                    \"airEquipType\": \"773\",\n                                    \"bookingClass\": \"U\",\n                                    \"flightNumber\": \"102\",\n                                    \"departureTime\": \"2021-06-20 21:05\",\n                                    \"marriageGroup\": \"I\",\n                                    \"recheckBaggage\": false,\n                                    \"marketingAirline\": \"PR\",\n                                    \"operatingAirline\": \"PR\",\n                                    \"arrivalAirportCode\": \"LAX\",\n                                    \"departureAirportCode\": \"MNL\",\n                                    \"arrivalAirportTerminal\": \"B\",\n                                    \"departureAirportTerminal\": \"1\"\n                                }\n                            ]\n                        }\n                    ],\n                    \"paxCnt\": 1,\n                    \"prices\": {\n                        \"comm\": 0,\n                        \"isCk\": false,\n                        \"ccCap\": 16.900002,\n                        \"markup\": 50,\n                        \"oMarkup\": {\n                            \"amount\": 50,\n                            \"currency\": \"USD\"\n                        },\n                        \"markupId\": 8833,\n                        \"totalTax\": 321.75,\n                        \"markupUid\": \"1c7afe8c-a34f-434e-8fa3-87b9b7b1ff4e\",\n                        \"totalPrice\": 767.75,\n                        \"lastTicketDate\": \"2021-03-31\"\n                    },\n                    \"currency\": \"USD\",\n                    \"fareType\": \"SR\",\n                    \"maxSeats\": 9,\n                    \"tripType\": \"RT\",\n                    \"penalties\": {\n                        \"list\": [\n                            {\n                                \"type\": \"re\",\n                                \"permitted\": false,\n                                \"applicability\": \"before\"\n                            },\n                            {\n                                \"type\": \"re\",\n                                \"permitted\": false,\n                                \"applicability\": \"after\"\n                            },\n                            {\n                                \"type\": \"ex\",\n                                \"amount\": 425,\n                                \"oAmount\": {\n                                    \"amount\": 425,\n                                    \"currency\": \"USD\"\n                                },\n                                \"permitted\": true,\n                                \"applicability\": \"before\"\n                            },\n                            {\n                                \"type\": \"ex\",\n                                \"amount\": 425,\n                                \"oAmount\": {\n                                    \"amount\": 425,\n                                    \"currency\": \"USD\"\n                                },\n                                \"permitted\": true,\n                                \"applicability\": \"after\"\n                            }\n                        ],\n                        \"refund\": false,\n                        \"exchange\": true\n                    },\n                    \"routingId\": 1,\n                    \"currencies\": [\n                        \"USD\"\n                    ],\n                    \"founded_dt\": \"2021-02-25 13:44:54.570\",\n                    \"passengers\": {\n                        \"ADT\": {\n                            \"cnt\": 1,\n                            \"tax\": 321.75,\n                            \"comm\": 0,\n                            \"ccCap\": 16.900002,\n                            \"price\": 767.75,\n                            \"codeAs\": \"JCB\",\n                            \"markup\": 50,\n                            \"occCap\": {\n                                \"amount\": 16.900002,\n                                \"currency\": \"USD\"\n                            },\n                            \"baseTax\": 271.75,\n                            \"oMarkup\": {\n                                \"amount\": 50,\n                                \"currency\": \"USD\"\n                            },\n                            \"baseFare\": 446,\n                            \"oBaseTax\": {\n                                \"amount\": 271.75,\n                                \"currency\": \"USD\"\n                            },\n                            \"oBaseFare\": {\n                                \"amount\": 446,\n                                \"currency\": \"USD\"\n                            },\n                            \"pubBaseFare\": 446\n                        }\n                    },\n                    \"ngsFeatures\": {\n                        \"list\": null,\n                        \"name\": \"\",\n                        \"stars\": 3\n                    },\n                    \"currencyRates\": {\n                        \"CADUSD\": {\n                            \"to\": \"USD\",\n                            \"from\": \"CAD\",\n                            \"rate\": 0.78417\n                        },\n                        \"DKKUSD\": {\n                            \"to\": \"USD\",\n                            \"from\": \"DKK\",\n                            \"rate\": 0.16459\n                        },\n                        \"EURUSD\": {\n                            \"to\": \"USD\",\n                            \"from\": \"EUR\",\n                            \"rate\": 1.23967\n                        },\n                        \"GBPUSD\": {\n                            \"to\": \"USD\",\n                            \"from\": \"GBP\",\n                            \"rate\": 1.37643\n                        },\n                        \"KRWUSD\": {\n                            \"to\": \"USD\",\n                            \"from\": \"KRW\",\n                            \"rate\": 0.00091\n                        },\n                        \"MYRUSD\": {\n                            \"to\": \"USD\",\n                            \"from\": \"MYR\",\n                            \"rate\": 0.25006\n                        },\n                        \"SEKUSD\": {\n                            \"to\": \"USD\",\n                            \"from\": \"SEK\",\n                            \"rate\": 0.12221\n                        },\n                        \"TWDUSD\": {\n                            \"to\": \"USD\",\n                            \"from\": \"TWD\",\n                            \"rate\": 0.03592\n                        },\n                        \"USDCAD\": {\n                            \"to\": \"CAD\",\n                            \"from\": \"USD\",\n                            \"rate\": 1.30086\n                        },\n                        \"USDDKK\": {\n                            \"to\": \"DKK\",\n                            \"from\": \"USD\",\n                            \"rate\": 6.19797\n                        },\n                        \"USDEUR\": {\n                            \"to\": \"EUR\",\n                            \"from\": \"USD\",\n                            \"rate\": 0.83926\n                        },\n                        \"USDGBP\": {\n                            \"to\": \"GBP\",\n                            \"from\": \"USD\",\n                            \"rate\": 0.75587\n                        },\n                        \"USDKRW\": {\n                            \"to\": \"KRW\",\n                            \"from\": \"USD\",\n                            \"rate\": 1117.1008\n                        },\n                        \"USDMYR\": {\n                            \"to\": \"MYR\",\n                            \"from\": \"USD\",\n                            \"rate\": 4.07943\n                        },\n                        \"USDSEK\": {\n                            \"to\": \"SEK\",\n                            \"from\": \"USD\",\n                            \"rate\": 8.34736\n                        },\n                        \"USDTWD\": {\n                            \"to\": \"TWD\",\n                            \"from\": \"USD\",\n                            \"rate\": 28.96525\n                        },\n                        \"USDUSD\": {\n                            \"to\": \"USD\",\n                            \"from\": \"USD\",\n                            \"rate\": 1\n                        }\n                    },\n                    \"validatingCarrier\": \"PR\"\n                }\n            ],\n            \"additionalInfo\": {\n                \"cabin\": {\n                    \"C\": \"Business\",\n                    \"F\": \"First\",\n                    \"J\": \"Premium Business\",\n                    \"P\": \"Premium First\",\n                    \"S\": \"Premium Economy\",\n                    \"Y\": \"Economy\"\n                },\n                \"airline\": {\n                    \"PR\": {\n                        \"name\": \"Philippine Airlines\"\n                    }\n                },\n                \"airport\": {\n                    \"LAX\": {\n                        \"city\": \"Los Angeles\",\n                        \"name\": \"Los Angeles International Airport\",\n                        \"country\": \"United States\"\n                    },\n                    \"MNL\": {\n                        \"city\": \"Manila\",\n                        \"name\": \"Ninoy Aquino International Airport\",\n                        \"country\": \"Philippines\"\n                    },\n                    \"TPE\": {\n                        \"city\": \"Taipei\",\n                        \"name\": \"Taiwan Taoyuan International Airport\",\n                        \"country\": \"Taiwan\"\n                    }\n                },\n                \"general\": {\n                    \"tripType\": \"rt\"\n                }\n            }\n        },\n        \"Passengers\": {\n            \"Flight\": [\n                {\n                    \"id\": null,\n                    \"user_id\": null,\n                    \"first_name\": \"Arthur\",\n                    \"middle_name\": \"\",\n                    \"last_name\": \"Davis\",\n                    \"birth_date\": \"1963-04-07\",\n                    \"gender\": \"M\",\n                    \"seats\": null,\n                    \"assistance\": null,\n                    \"nationality\": \"US\",\n                    \"passport_id\": null,\n                    \"passport_valid_date\": null,\n                    \"email\": null,\n                    \"codeAs\": null\n                }\n            ],\n            \"Hotel\": [\n                {\n                    \"first_name\": \"mike\",\n                    \"last_name\": \"kane\"\n                }\n            ],\n            \"Driver\": [\n                {\n                    \"first_name\": \"mike\",\n                    \"last_name\": \"kane\",\n                    \"age\": \"30-69\",\n                    \"birth_date\": \"1973-04-07\"\n                }\n            ],\n            \"Attraction\": [\n                {\n                    \"first_name\": \"mike\",\n                    \"last_name\": \"kane\",\n                    \"language_service\": \"US\"\n                }\n            ],\n            \"Cruise\": [\n                {\n                    \"first_name\": \"Arthur\",\n                    \"last_name\": \"Davis\",\n                    \"citizenship\": \"US\",\n                    \"birth_date\": \"1963-04-07\",\n                    \"gender\": \"M\"\n                }\n            ]\n        },\n        \"Insurance\": {\n            \"total_amount\": \"20\",\n            \"record_id\": \"396393\",\n            \"passengers\": [\n                {\n                    \"nameRef\": \"0\",\n                    \"amount\": 20\n                }\n            ]\n        },\n        \"Tip\": {\n            \"total_amount\": 20\n        },\n        \"AuxiliarProducts\": {\n            \"Flight\": {\n                \"basket\": {\n                    \"1c3df555-a2dc-4813-a055-2a8bf56fd8f1\": {\n                        \"basket_item_id\": \"1c3df555-a2dc-4813-a055-2a8bf56fd8f1\",\n                        \"benefits\": [],\n                        \"display_name\": \"10kg Bag\",\n                        \"price\": {\n                            \"base\": {\n                                \"amount\": 2000,\n                                \"currency\": \"USD\",\n                                \"decimal_places\": 2,\n                                \"in_original_currency\": {\n                                    \"amount\": 1820,\n                                    \"currency\": \"USD\",\n                                    \"decimal_places\": 2\n                                }\n                            },\n                            \"fees\": [],\n                            \"markups\": [\n                                {\n                                    \"amount\": 600,\n                                    \"currency\": \"USD\",\n                                    \"decimal_places\": 2,\n                                    \"in_original_currency\": {\n                                        \"amount\": 546,\n                                        \"currency\": \"USD\",\n                                        \"decimal_places\": 2\n                                    },\n                                    \"markup_type\": \"markup\"\n                                }\n                            ],\n                            \"taxes\": [\n                                {\n                                    \"amount\": 200,\n                                    \"currency\": \"USD\",\n                                    \"decimal_places\": 2,\n                                    \"in_original_currency\": {\n                                        \"amount\": 182,\n                                        \"currency\": \"USD\",\n                                        \"decimal_places\": 2\n                                    },\n                                    \"tax_type\": \"tax\"\n                                }\n                            ],\n                            \"total\": {\n                                \"amount\": 2400,\n                                \"currency\": \"USD\",\n                                \"decimal_places\": 2,\n                                \"in_original_currency\": {\n                                    \"amount\": 2184,\n                                    \"currency\": \"USD\",\n                                    \"decimal_places\": 2\n                                }\n                            }\n                        },\n                        \"product_details\": {\n                            \"journey_id\": \"1770bf8f-0c1c-4ba5-99f5-56e446fe79ba\",\n                            \"passenger_id\": \"p1\",\n                            \"size\": 150,\n                            \"size_unit\": \"cm\",\n                            \"weight\": 10,\n                            \"weight_unit\": \"kg\"\n                        },\n                        \"product_id\": \"741bcc97-c2fe-4820-b14d-f11f32e6fadb\",\n                        \"product_type\": \"bag\",\n                        \"quantity\": 1,\n                        \"ticket_id\": \"e8558737-2ec0-436f-89ec-00e7a20b3252\",\n                        \"validity\": {\n                            \"state\": \"valid\",\n                            \"valid_from\": \"2020-05-22T16:34:08Z\",\n                            \"valid_to\": \"2020-05-22T16:49:08Z\"\n                        }\n                    },\n                    \"2654f3f9-8990-4d2e-bdea-3b341ad5d1de\": {\n                        \"basket_item_id\": \"2654f3f9-8990-4d2e-bdea-3b341ad5d1de\",\n                        \"benefits\": [],\n                        \"display_name\": \"Seat 15C\",\n                        \"price\": {\n                            \"base\": {\n                                \"amount\": 2000,\n                                \"currency\": \"USD\",\n                                \"decimal_places\": 2,\n                                \"in_original_currency\": {\n                                    \"amount\": 1820,\n                                    \"currency\": \"USD\",\n                                    \"decimal_places\": 2\n                                }\n                            },\n                            \"fees\": [],\n                            \"markups\": [\n                                {\n                                    \"amount\": 400,\n                                    \"currency\": \"USD\",\n                                    \"decimal_places\": 2,\n                                    \"in_original_currency\": {\n                                        \"amount\": 364,\n                                        \"currency\": \"USD\",\n                                        \"decimal_places\": 2\n                                    },\n                                    \"markup_type\": \"markup\"\n                                }\n                            ],\n                            \"taxes\": [\n                                {\n                                    \"amount\": 200,\n                                    \"currency\": \"USD\",\n                                    \"decimal_places\": 2,\n                                    \"in_original_currency\": [],\n                                    \"tax_type\": \"tax\"\n                                }\n                            ],\n                            \"total\": {\n                                \"amount\": 2600,\n                                \"currency\": \"USD\",\n                                \"decimal_places\": 2,\n                                \"in_original_currency\": {\n                                    \"amount\": 2366,\n                                    \"currency\": \"USD\",\n                                    \"decimal_places\": 2\n                                }\n                            }\n                        },\n                        \"product_details\": {\n                            \"column\": \"C\",\n                            \"passenger_id\": \"p1\",\n                            \"row\": 15,\n                            \"segment_id\": \"1770bf8f-0c1c-4ba5-99f5-56e446fe79ba\"\n                        },\n                        \"product_id\": \"a17e10ca-0c9a-4691-9922-d664a3b52382\",\n                        \"product_type\": \"seat\",\n                        \"quantity\": 1,\n                        \"ticket_id\": \"e8558737-2ec0-436f-89ec-00e7a20b3252\",\n                        \"validity\": {\n                            \"state\": \"valid\",\n                            \"valid_from\": \"2020-05-22T16:34:08Z\",\n                            \"valid_to\": \"2020-05-22T16:49:08Z\"\n                        }\n                    },\n                    \"5d5e1bce-4577-4118-abcb-155823d8b4a3\": [],\n                    \"6acd57ba-ccb7-4e86-85e7-b3e586caeae2\": [],\n                    \"dffac4ba-73b9-4b1b-9334-001817fff0cf\": [],\n                    \"e960eff9-7628-4645-99d8-20a6e22f6419\": []\n                },\n                \"country\": \"US\",\n                \"currency\": \"USD\",\n                \"journeys\": [\n                    {\n                        \"journey_id\": \"aab8980e-b263-4624-ad40-d6e5e364b4e9\",\n                        \"segments\": [\n                            {\n                                \"arrival_airport\": \"LHR\",\n                                \"arrival_time\": \"2020-07-07T22:30:00Z\",\n                                \"departure_airport\": \"EDI\",\n                                \"departure_time\": \"2020-07-07T21:10:00Z\",\n                                \"fare_basis\": \"OTZ0RO/Y\",\n                                \"fare_class\": \"O\",\n                                \"fare_family\": \"Basic Economy\",\n                                \"marketing_airline\": \"BA\",\n                                \"marketing_flight_number\": \"1465\",\n                                \"number_of_stops\": 0,\n                                \"operating_airline\": \"BA\",\n                                \"operating_flight_number\": \"1465\",\n                                \"segment_id\": \"938d8e82-dd7c-4d85-8ab4-38fea8753f6f\"\n                            }\n                        ]\n                    },\n                    {\n                        \"journey_id\": \"1770bf8f-0c1c-4ba5-99f5-56e446fe79ba\",\n                        \"segments\": [\n                            {\n                                \"arrival_airport\": \"EDI\",\n                                \"arrival_time\": \"2020-07-14T08:35:00Z\",\n                                \"departure_airport\": \"LGW\",\n                                \"departure_time\": \"2020-07-14T07:05:00Z\",\n                                \"fare_basis\": \"NALZ0KO/Y\",\n                                \"fare_class\": \"N\",\n                                \"fare_family\": \"Basic Economy\",\n                                \"marketing_airline\": \"BA\",\n                                \"marketing_flight_number\": \"2500\",\n                                \"number_of_stops\": 0,\n                                \"operating_airline\": \"BA\",\n                                \"operating_flight_number\": \"2500\",\n                                \"segment_id\": \"7d693cb0-d6d8-49f0-9489-866b3d789215\"\n                            }\n                        ]\n                    }\n                ],\n                \"language\": \"en-US\",\n                \"orders\": [],\n                \"passengers\": [\n                    {\n                        \"first_names\": \"Vincent Willem\",\n                        \"passenger_id\": \"ee850c82-e150-4f35-b0c7-228064c2964b\",\n                        \"surname\": \"Van Gogh\"\n                    }\n                ],\n                \"tickets\": [\n                    {\n                        \"basket_item_ids\": [\n                            \"dffac4ba-73b9-4b1b-9334-001817fff0cf\",\n                            \"e960eff9-7628-4645-99d8-20a6e22f6419\",\n                            \"6acd57ba-ccb7-4e86-85e7-b3e586caeae2\",\n                            \"5d5e1bce-4577-4118-abcb-155823d8b4a3\"\n                        ],\n                        \"journey_ids\": [\n                            \"aab8980e-b263-4624-ad40-d6e5e364b4e9\"\n                        ],\n                        \"state\": \"in_basket\",\n                        \"ticket_basket_item_id\": \"dffac4ba-73b9-4b1b-9334-001817fff0cf\",\n                        \"ticket_id\": \"8c1c9fc8-d968-4733-93a8-6067bac2543f\"\n                    },\n                    {\n                        \"basket_item_ids\": [\n                            \"2654f3f9-8990-4d2e-bdea-3b341ad5d1de\",\n                            \"1c3df555-a2dc-4813-a055-2a8bf56fd8f1\"\n                        ],\n                        \"journey_ids\": [\n                            \"1770bf8f-0c1c-4ba5-99f5-56e446fe79ba\"\n                        ],\n                        \"offered_price\": {\n                            \"currency\": \"USD\",\n                            \"decimal_places\": 2,\n                            \"total\": 20000\n                        },\n                        \"state\": \"offered\",\n                        \"ticket_id\": \"e8558737-2ec0-436f-89ec-00e7a20b3252\"\n                    }\n                ],\n                \"trip_access_token\": \"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c\",\n                \"trip_id\": \"23259b86-3208-44c9-85cc-4b116a822bff\",\n                \"trip_state_hash\": \"69abcc117863186292bdf5f1c0d94db1e5227210935e6abe039cfb017cbefbee\"\n            },\n            \"Hotel\": [],\n            \"Driver\": [],\n            \"Attraction\": [],\n            \"Cruise\": []\n        },\n        \"Payment\": {\n            \"type\": \"CARD\",\n            \"transaction_id\": \"1234567890\",\n            \"card_id\": 234567,\n            \"auth_id\": 123456\n        }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n  {\n\"status\": 200,\n\"message\": \"OK\",\n\"data\": {\n\"order_gid\": \"ef75bfa7cc60af154c22c43e3732350f\"\n},\n\"technical\": {\n\"action\": \"v2/order/create\",\n\"response_id\": 327,\n\"request_dt\": \"2021-02-27 08:49:46\",\n\"response_dt\": \"2021-02-27 08:49:46\",\n\"execution_time\": 0.094,\n\"memory_usage\": 1356920\n}\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n\"status\": 422,\n\"message\": \"Validation error\",\n\"errors\": {\n\"payment.type\": [\n\"Type is invalid.\"\n]\n},\n\"code\": 0,\n\"technical\": {\n\"action\": \"v2/order/create\",\n\"response_id\": 328,\n\"request_dt\": \"2021-02-27 08:52:06\",\n\"response_dt\": \"2021-02-27 08:52:06\",\n\"execution_time\": 0.021,\n\"memory_usage\": 437656\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/OrderController.php",
    "groupTitle": "Order"
  },
  {
    "type": "post",
    "url": "/v2/order/create-c2b",
    "title": "Create Order c2b flow",
    "version": "1.0.0",
    "name": "CreateOrderClickToBook",
    "group": "Order",
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
          "content": "{\n     \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n     \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n }",
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
            "size": "max 10",
            "optional": false,
            "field": "sourceCid",
            "description": "<p>Source cid</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 7",
            "optional": false,
            "field": "bookingId",
            "description": "<p>Booking id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 255",
            "optional": false,
            "field": "fareId",
            "description": "<p>Unique value of order</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "allowedValues": [
              "\"success\"",
              "\"failed\""
            ],
            "optional": false,
            "field": "status",
            "description": "<p>Status</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 5",
            "optional": false,
            "field": "languageId",
            "description": "<p>Language Id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 2",
            "optional": false,
            "field": "marketCountry",
            "description": "<p>Market Country</p>"
          },
          {
            "group": "Parameter",
            "type": "Object[]",
            "optional": false,
            "field": "quotes",
            "description": "<p>Product quotes</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "quotes.productKey",
            "description": "<p>Product key</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "allowedValues": [
              "\"booked\"",
              "\"failed\""
            ],
            "optional": false,
            "field": "quotes.status",
            "description": "<p>Status</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "quotes.originSearchData",
            "description": "<p>Product quote origin search data</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "quotes.quoteOtaId",
            "description": "<p>Product quote custom id</p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": false,
            "field": "quotes.holder",
            "description": "<p>Holder Info</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": false,
            "field": "quotes.holder.firstName",
            "description": "<p>Holder first name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": false,
            "field": "quotes.holder.lastName",
            "description": "<p>Holder last name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": true,
            "field": "quotes.holder.middleName",
            "description": "<p>Holder middle name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 100",
            "optional": false,
            "field": "quotes.holder.email",
            "description": "<p>Holder email</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 20",
            "optional": false,
            "field": "quotes.holder.phone",
            "description": "<p>Holder phone</p>"
          },
          {
            "group": "Parameter",
            "type": "Object[]",
            "optional": false,
            "field": "quotes.options",
            "description": "<p>Quote Options</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": false,
            "field": "quotes.options.productOptionKey",
            "description": "<p>Product option key</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": true,
            "field": "quotes.options.name",
            "description": "<p>Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "quotes.options.description",
            "description": "<p>Description</p>"
          },
          {
            "group": "Parameter",
            "type": "Decimal",
            "optional": false,
            "field": "quotes.options.price",
            "description": "<p>Price</p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": true,
            "field": "quotes.flightPaxData",
            "description": "<p>[]      Flight pax data</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "allowedValues": [
              "\"ADT\"",
              "\"CHD\"",
              "\"INF\""
            ],
            "optional": false,
            "field": "quotes.flightPaxData.type",
            "description": "<p>Pax type</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 40",
            "optional": true,
            "field": "quotes.flightPaxData.first_name",
            "description": "<p>First Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 40",
            "optional": true,
            "field": "quotes.flightPaxData.last_name",
            "description": "<p>Last Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 40",
            "optional": true,
            "field": "quotes.flightPaxData.middle_name",
            "description": "<p>Middle Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 5",
            "optional": true,
            "field": "quotes.flightPaxData.nationality",
            "description": "<p>Nationality</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 1",
            "optional": true,
            "field": "quotes.flightPaxData.gender",
            "description": "<p>Gender</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "format yyyy-mm-dd",
            "optional": true,
            "field": "quotes.flightPaxData.birth_date",
            "description": "<p>Birth Date</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 100",
            "optional": true,
            "field": "quotes.flightPaxData.email",
            "description": "<p>Email</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 5",
            "optional": true,
            "field": "quotes.flightPaxData.language",
            "description": "<p>Language</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 5",
            "optional": true,
            "field": "quotes.flightPaxData.citizenship",
            "description": "<p>Citizenship</p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": true,
            "field": "quotes.hotelPaxData",
            "description": "<p>[]      Flight pax data</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "allowedValues": [
              "\"ADT\"",
              "\"CHD\""
            ],
            "optional": false,
            "field": "quotes.hotelPaxData.type",
            "description": "<p>Pax type</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 40",
            "optional": true,
            "field": "quotes.hotelPaxData.first_name",
            "description": "<p>First Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 40",
            "optional": true,
            "field": "quotes.hotelPaxData.last_name",
            "description": "<p>Last Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "format yyyy-mm-dd",
            "optional": true,
            "field": "quotes.hotelPaxData.birth_date",
            "description": "<p>Birth Date</p>"
          },
          {
            "group": "Parameter",
            "type": "integer",
            "optional": true,
            "field": "quotes.hotelPaxData.age",
            "description": "<p>Age</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "quotes.hotelPaxData.hotelRoomKey",
            "description": "<p>Hotel Room Key</p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": false,
            "field": "quotes.hotelRequest",
            "description": "<p>Hotel Request data <code>required for hotel quotes</code></p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "quotes.hotelRequest.destinationName",
            "description": "<p>Destination Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "quotes.hotelRequest.destinationCode",
            "description": "<p>Destination Code</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "quotes.hotelRequest.checkIn",
            "description": "<p>Check In Date <code>format: yyyy-mm-dd</code></p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "quotes.hotelRequest.checkOut",
            "description": "<p>Check Out Date <code>format: yyyy-mm-dd</code></p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": true,
            "field": "billingInfo",
            "description": "<p>BillingInfo</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": true,
            "field": "billingInfo.first_name",
            "description": "<p>First Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": true,
            "field": "billingInfo.last_name",
            "description": "<p>Last Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": true,
            "field": "billingInfo.middle_name",
            "description": "<p>Middle Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": true,
            "field": "billingInfo.address",
            "description": "<p>Address</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 2",
            "optional": true,
            "field": "billingInfo.country_id",
            "description": "<p>Country Id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": true,
            "field": "billingInfo.city",
            "description": "<p>City</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 40",
            "optional": true,
            "field": "billingInfo.state",
            "description": "<p>State</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 10",
            "optional": true,
            "field": "billingInfo.zip",
            "description": "<p>Zip</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 20",
            "optional": true,
            "field": "billingInfo.phone",
            "description": "<p>Phone <code>Deprecated</code></p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 160",
            "optional": true,
            "field": "billingInfo.email",
            "description": "<p>Email <code>Deprecated</code></p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": true,
            "field": "creditCard",
            "description": "<p>Credit Card</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": true,
            "field": "creditCard.holder_name",
            "description": "<p>Holder Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 20",
            "optional": false,
            "field": "creditCard.number",
            "description": "<p>Credit Card Number</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "creditCard.type",
            "description": "<p>Credit Card type</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 18",
            "optional": false,
            "field": "creditCard.expiration",
            "description": "<p>Credit Card expiration</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 4",
            "optional": false,
            "field": "creditCard.cvv",
            "description": "<p>Credit Card cvv</p>"
          },
          {
            "group": "Parameter",
            "type": "Object[]",
            "optional": false,
            "field": "contactsInfo",
            "description": "<p>BillingInfo</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": false,
            "field": "contactsInfo.first_name",
            "description": "<p>First Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": true,
            "field": "contactsInfo.last_name",
            "description": "<p>Last Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": true,
            "field": "contactsInfo.middle_name",
            "description": "<p>Middle Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 20",
            "optional": true,
            "field": "contactsInfo.phone",
            "description": "<p>Phone number</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 100",
            "optional": false,
            "field": "contactsInfo.email",
            "description": "<p>Email</p>"
          },
          {
            "group": "Parameter",
            "type": "Object",
            "optional": true,
            "field": "payment",
            "description": "<p>Payment info</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 3",
            "optional": true,
            "field": "payment.clientCurrency",
            "description": "<p>Client currency</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n{\n            \"sourceCid\": \"ACHUY23AS\",\n            \"bookingId\": \"WCJ12C\",\n            \"fareId\": \"A0EA9F-5cc2ce331e8bb3.16383647\",\n            \"status\": \"success\",\n            \"languageId\": \"en-US\",\n            \"marketCountry\": \"US\",\n            \"quotes\": [\n                {\n                    \"status\": \"booked\",\n                    \"productKey\": \"flight\",\n                    \"originSearchData\": \"{\\\"key\\\":\\\"2_QldLMTAxKlkxMDAwL0pGS1BBUjIwMjEtMDgtMDcqREx+I0RMOTE4MH5sYzplbl91cw==\\\",\\\"routingId\\\":1,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2021-04-05\\\",\\\"totalPrice\\\":354.2,\\\"totalTax\\\":229.2,\\\"comm\\\":0,\\\"isCk\\\":false,\\\"markupId\\\":0,\\\"markupUid\\\":\\\"\\\",\\\"markup\\\":0},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"ADT\\\",\\\"cnt\\\":1,\\\"baseFare\\\":125,\\\"pubBaseFare\\\":125,\\\"baseTax\\\":229.2,\\\"markup\\\":0,\\\"comm\\\":0,\\\"price\\\":354.2,\\\"tax\\\":229.2,\\\"oBaseFare\\\":{\\\"amount\\\":125,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":229.2,\\\"currency\\\":\\\"USD\\\"}}},\\\"penalties\\\":{\\\"exchange\\\":true,\\\"refund\\\":false,\\\"list\\\":[{\\\"type\\\":\\\"ex\\\",\\\"applicability\\\":\\\"before\\\",\\\"permitted\\\":true,\\\"amount\\\":0},{\\\"type\\\":\\\"ex\\\",\\\"applicability\\\":\\\"after\\\",\\\"permitted\\\":true,\\\"amount\\\":0},{\\\"type\\\":\\\"re\\\",\\\"applicability\\\":\\\"before\\\",\\\"permitted\\\":false},{\\\"type\\\":\\\"re\\\",\\\"applicability\\\":\\\"after\\\",\\\"permitted\\\":false}]},\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segments\\\":[{\\\"segmentId\\\":1,\\\"departureTime\\\":\\\"2021-08-07 16:30\\\",\\\"arrivalTime\\\":\\\"2021-08-08 05:55\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"9180\\\",\\\"bookingClass\\\":\\\"E\\\",\\\"duration\\\":445,\\\"departureAirportCode\\\":\\\"JFK\\\",\\\"departureAirportTerminal\\\":\\\"1\\\",\\\"arrivalAirportCode\\\":\\\"CDG\\\",\\\"arrivalAirportTerminal\\\":\\\"2E\\\",\\\"operatingAirline\\\":\\\"AF\\\",\\\"airEquipType\\\":\\\"77W\\\",\\\"marketingAirline\\\":\\\"DL\\\",\\\"marriageGroup\\\":\\\"O\\\",\\\"mileage\\\":3629,\\\"cabin\\\":\\\"Y\\\",\\\"cabinIsBasic\\\":true,\\\"brandId\\\":\\\"686562\\\",\\\"brandName\\\":\\\"BASIC ECONOMY\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"VH7L09B1\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"carryOn\\\":true,\\\"allowPieces\\\":0}},\\\"recheckBaggage\\\":false}],\\\"duration\\\":445}],\\\"maxSeats\\\":9,\\\"paxCnt\\\":1,\\\"validatingCarrier\\\":\\\"DL\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"cons\\\":\\\"GTT\\\",\\\"fareType\\\":\\\"PUB\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"currencies\\\":[\\\"USD\\\"],\\\"currencyRates\\\":{\\\"USDUSD\\\":{\\\"from\\\":\\\"USD\\\",\\\"to\\\":\\\"USD\\\",\\\"rate\\\":1}},\\\"keys\\\":{\\\"travelport\\\":{\\\"traceId\\\":\\\"9cbb17ae-40dd-4d94-83be-2f0eed47e9ad\\\",\\\"availabilitySources\\\":\\\"S\\\",\\\"type\\\":\\\"T\\\"},\\\"seatHoldSeg\\\":{\\\"trip\\\":0,\\\"segment\\\":0,\\\"seats\\\":9}},\\\"meta\\\":{\\\"eip\\\":0,\\\"noavail\\\":false,\\\"searchId\\\":\\\"QldLMTAxWTEwMDB8SkZLUEFSMjAyMS0wOC0wNw==\\\",\\\"lang\\\":\\\"en\\\",\\\"rank\\\":10,\\\"cheapest\\\":true,\\\"fastest\\\":false,\\\"best\\\":true,\\\"bags\\\":0,\\\"country\\\":\\\"us\\\",\\\"prod_types\\\":[\\\"PUB\\\"]}}\",\n                    \"options\": [\n                        {\n                            \"productOptionKey\": \"travelGuard\",\n                            \"name\": \"Travel Guard\",\n                            \"description\": \"\",\n                            \"price\": 20\n                        }\n                    ],\n                    \"flightPaxData\": [\n                        {\n                            \"first_name\": \"Test name\",\n                            \"last_name\": \"Test last name\",\n                            \"middle_name\": \"Test middle name\",\n                            \"nationality\": \"US\",\n                            \"gender\": \"M\",\n                            \"birth_date\": \"1963-04-07\",\n                            \"email\": \"mike.kane@techork.com\",\n                            \"language\": \"en-US\",\n                            \"citizenship\": \"US\",\n                            \"type\": \"ADT\"\n                        }\n                    ],\n                    \"quoteOtaId\": \"asdff43fsgfdsv343ddx\",\n                    \"holder\": {\n                        \"firstName\": \"Test\",\n                        \"lastName\": \"Test\",\n                        \"middleName\": \"Test\",\n                        \"email\": \"test@test.test\",\n                        \"phone\": \"+19074861000\"\n                    }\n                },\n                {\n                    \"status\": \"booked\",\n                    \"productKey\": \"hotel\",\n                    \"originSearchData\": \"{\\\"categoryName\\\":\\\"3 STARS\\\",\\\"destinationName\\\":\\\"Chisinau\\\",\\\"zoneName\\\":\\\"Chisinau\\\",\\\"minRate\\\":135.92,\\\"maxRate\\\":285.94,\\\"currency\\\":\\\"USD\\\",\\\"code\\\":148030,\\\"name\\\":\\\"Cosmos Hotel\\\",\\\"description\\\":\\\"The hotel is situated in the heart of Chisinau, the capital of Moldova. It is perfectly located for access to the business centre, cultural institutions and much more. Chisinau Airport is only 15 minutes away and the railway station is less than 5 minutes away from the hotel.\\\\n\\\\nThe city hotel offers a choice of 150 rooms, 24-hour reception and check-out services in the lobby, luggage storage, a hotel safe, currency exchange facility and a cloakroom. There is lift access to the upper floors as well as an on-site restaurant and conference facilities. Internet access, a laundry service (fees apply) and free parking in the car park are also on offer to guests during their stay.\\\\n\\\\nAll the rooms are furnished with double or king-size beds and provide an en suite bathroom with a shower. Air conditioning, central heating, satellite TV, a telephone, mini fridge, radio and free wireless Internet access are also on offer.\\\\n\\\\nThere is a golf course about 12 km from the hotel.\\\\n\\\\nThe hotel restaurant offers a wide selection of local and European cuisine. Breakfast is served as a buffet and lunch and dinner can be chosen la carte.\\\",\\\"countryCode\\\":\\\"MD\\\",\\\"stateCode\\\":\\\"MD\\\",\\\"destinationCode\\\":\\\"KIV\\\",\\\"zoneCode\\\":1,\\\"latitude\\\":47.014293,\\\"longitude\\\":28.853371,\\\"categoryCode\\\":\\\"3EST\\\",\\\"categoryGroupCode\\\":\\\"GRUPO3\\\",\\\"accomodationType\\\":{\\\"code\\\":\\\"HOTEL\\\"},\\\"boardCodes\\\":[\\\"BB\\\",\\\"AI\\\",\\\"HB\\\",\\\"FB\\\",\\\"RO\\\"],\\\"segmentCodes\\\":[],\\\"address\\\":\\\"NEGRUZZI, 2\\\",\\\"postalCode\\\":\\\"MD2001\\\",\\\"city\\\":\\\"CHISINAU\\\",\\\"email\\\":\\\"info@hotel-cosmos.com\\\",\\\"phones\\\":[{\\\"type\\\":\\\"PHONEBOOKING\\\",\\\"number\\\":\\\"+37322890054\\\"},{\\\"type\\\":\\\"PHONEHOTEL\\\",\\\"number\\\":\\\"+37322837505\\\"},{\\\"type\\\":\\\"FAXNUMBER\\\",\\\"number\\\":\\\"+37322542744\\\"}],\\\"images\\\":[{\\\"url\\\":\\\"14/148030/148030a_hb_a_001.jpg\\\",\\\"type\\\":\\\"GEN\\\"}],\\\"web\\\":\\\"http://hotel-cosmos.com/\\\",\\\"lastUpdate\\\":\\\"2020-11-23\\\",\\\"s2C\\\":\\\"1*\\\",\\\"ranking\\\":14,\\\"serviceType\\\":\\\"HOTELBEDS\\\",\\\"groupKey\\\":\\\"2118121725\\\",\\\"totalAmount\\\":341.32,\\\"totalMarkup\\\":26.69,\\\"totalPublicAmount\\\":347.99,\\\"totalSavings\\\":6.67,\\\"totalEarnings\\\":3.34,\\\"rates\\\":[{\\\"code\\\":\\\"ROO.ST\\\",\\\"name\\\":\\\"Room Standard\\\",\\\"key\\\":\\\"20210608|20210616|W|504|148030|ROO.ST|ID_B2B_76|BB|B2B|1~1~0||N@06~~24ebc~-829367492~N~~~NOR~C98A4E21F1184B3161702850635900AWUS0000029001400030824ebc\\\",\\\"class\\\":\\\"NOR\\\",\\\"allotment\\\":3,\\\"type\\\":\\\"RECHECK\\\",\\\"paymentType\\\":\\\"AT_WEB\\\",\\\"boardCode\\\":\\\"BB\\\",\\\"boardName\\\":\\\"BED AND BREAKFAST\\\",\\\"rooms\\\":1,\\\"adults\\\":1,\\\"markup\\\":16.62,\\\"amount\\\":205.4,\\\"publicAmmount\\\":209.55,\\\"savings\\\":4.15,\\\"earnings\\\":2.08},{\\\"code\\\":\\\"ROO.ST\\\",\\\"name\\\":\\\"Room Standard\\\",\\\"key\\\":\\\"20210608|20210616|W|504|148030|ROO.ST|ID_B2B_76|RO|B2B|1~2~0||N@06~~2557d~-972866252~N~~~NOR~C98A4E21F1184B3161702850635900AWUS000002900140003082557d\\\",\\\"class\\\":\\\"NOR\\\",\\\"allotment\\\":3,\\\"type\\\":\\\"RECHECK\\\",\\\"paymentType\\\":\\\"AT_WEB\\\",\\\"boardCode\\\":\\\"RO\\\",\\\"boardName\\\":\\\"ROOM ONLY\\\",\\\"rooms\\\":1,\\\"adults\\\":2,\\\"markup\\\":10.07,\\\"amount\\\":135.92,\\\"publicAmmount\\\":138.44,\\\"savings\\\":2.52,\\\"earnings\\\":1.26}]}\",\n\n                    \"quoteOtaId\": \"asdfw43wfdswef3x\",\n                    \"holder\": {\n                        \"firstName\": \"Test 2\",\n                        \"lastName\": \"Test 2\",\n                        \"email\": \"test+2@test.test\",\n                        \"phone\": \"+19074861000\"\n                    },\n                    \"hotelPaxData\": [\n                        {\n                            \"hotelRoomKey\": \"20210608|20210616|W|504|148030|ROO.ST|ID_B2B_76|RO|B2B|1~2~0||N@06~~2557d~-972866252~N~~~NOR~C98A4E21F1184B3161702850635900AWUS000002900140003082557d\",\n                            \"first_name\": \"Test\",\n                            \"last_name\": \"Test\",\n                            \"birth_date\": \"1963-04-07\",\n                            \"age\": \"45\",\n                            \"type\": \"ADT\"\n                        },\n                        {\n                            \"hotelRoomKey\": \"20210608|20210616|W|504|148030|ROO.ST|ID_B2B_76|RO|B2B|1~2~0||N@06~~2557d~-972866252~N~~~NOR~C98A4E21F1184B3161702850635900AWUS000002900140003082557d\",\n                            \"first_name\": \"Mary\",\n                            \"last_name\": \"Smith\",\n                            \"birth_date\": \"1963-04-07\",\n                            \"age\": \"32\",\n                            \"type\": \"ADT\"\n                        }\n                    ],\n                    \"hotelRequest\": {\n                        \"destinationCode\": \"BGO\",\n                        \"destinationName\": \"Norway, Bergen\",\n                        \"checkIn\": \"2021-09-10\",\n                        \"checkOut\": \"2021-09-30\"\n                    }\n                }\n            ],\n            \"creditCard\": {\n                \"holder_name\": \"Barbara Elmore\",\n                \"number\": \"1111111111111111\",\n                \"type\": \"Visas\",\n                \"expiration\": \"07 / 23\",\n                \"cvv\": \"324\"\n            },\n            \"billingInfo\": {\n                \"first_name\": \"Barbara Elmore\",\n                \"middle_name\": \"\",\n                \"last_name\": \"T\",\n                \"address\": \"1013 Weda Cir\",\n                \"country_id\": \"US\",\n                \"city\": \"Mayfield\",\n                \"state\": \"KY\",\n                \"zip\": \"99999\",\n                \"phone\": \"+19074861000\", -- deprecated, will be removed soon\n                \"email\": \"barabara@test.com\" -- deprecated, will be removed soon\n            },\n            \"contactsInfo\": [\n                {\n                    \"first_name\": \"Barbara\",\n                    \"last_name\": \"Elmore\",\n                    \"middle_name\": \"\",\n                    \"phone\": \"+19074861000\",\n                    \"email\": \"barabara@test.com\"\n                },\n                {\n                    \"first_name\": \"John\",\n                    \"last_name\": \"Doe\",\n                    \"middle_name\": \"\",\n                    \"phone\": \"+19074865678\",\n                    \"email\": \"john@test.com\"\n                }\n            ],\n            \"payment\": {\n                \"clientCurrency\": \"USD\"\n            }\n        }",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n            \"status\": 200,\n            \"message\": \"OK\",\n            \"data\": {\n                \"order_gid\": \"1588da7b87cd3b91cc1df4aed0d7aeba\"\n            }\n        }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n            \"status\": 422,\n            \"message\": \"Validation error\",\n            \"errors\": {\n                \"quotes.0.productKey\": [\n                    \"Product type not found by key: flights\"\n                ]\n            },\n            \"code\": 0\n        }",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n            \"status\": 422,\n            \"message\": \"test\",\n            \"detailError\": {\n                \"product\": \"Flight\",\n                \"quoteOtaId\": \"asdff43fsgfdsv343ddx\"\n            },\n            \"code\": 15901,\n            \"errors\": []\n        }",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n            \"status\": 422,\n            \"message\": \"Validation error\",\n            \"errors\": {\n                \"fareId\": [\n                    \"Fare Id \\\"A0EA9F-5cc2ce331e8bb3.16383647\\\" has already been taken.\"\n                ]\n            },\n            \"code\": 0\n        }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/OrderController.php",
    "groupTitle": "Order"
  },
  {
    "type": "post",
    "url": "/v2/order/create-proxy",
    "title": "Create Order Proxy",
    "version": "0.2.0",
    "name": "CreateOrderProxy",
    "group": "Order",
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
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n       \"status\": \"Failed\",\n       \"success\": [],\n       \"failure\": {\n             \"message\": \"Price Increase\"\n       },\n       \"priceInfo\": {\n          \"totalPrice\": 1389.87,\n          \"totalTax\": 684.58,\n          \"fareType\": \"PUB\",\n          \"bookingClass\": \"WWWW\",\n          \"currency\": \"USD\",\n          \"detail\": {\n              \"ADT\": {\n                  \"quantity\": 2,\n              \"totalFare\": 448.29,\n              \"baseTax\": 342.29,\n              \"baseFare\": 106,\n            }\n          }\n       },\n       \"errors\": [],\n       \"source\": {\n          \"type\": 1,\n          \"status\": 200\n       },\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
          "type": "json"
        },
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
    "groupTitle": "Order"
  },
  {
    "type": "get",
    "url": "/v2/order/get-file",
    "title": "Get File",
    "version": "0.2.0",
    "name": "GetFile",
    "group": "Order",
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
            "optional": false,
            "field": "uid",
            "description": "<p>File UID</p>"
          }
        ]
      }
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (404):",
          "content": "\nHTTP/1.1 404 Not Found\n{\n  \"name\": \"Not Found\",\n  \"message\": \"File is not found.\",\n  \"code\": 0,\n  \"status\": 404,\n  \"type\": \"yii\\\\web\\\\NotFoundHttpException\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/OrderController.php",
    "groupTitle": "Order"
  },
  {
    "type": "post",
    "url": "/v2/order/view",
    "title": "View Order",
    "version": "0.1.0",
    "name": "ViewOrder",
    "group": "Order",
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
            "optional": false,
            "field": "gid",
            "description": "<p>Order gid</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n{\n    \"gid\": \"04d3fe3fc74d0514ee93e208a52bcf90\",\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n    \"status\": 200,\n    \"message\": \"OK\",\n    \"order\": {\n        \"or_id\": 110,\n        \"or_gid\": \"a0758d1d8ded3efe62c465ad36987200\",\n        \"or_uid\": \"or6047198783406\",\n        \"or_name\": \"Order 1\",\n        \"or_description\": null,\n        \"or_status_id\": 3,\n        \"or_pay_status_id\": 1,\n        \"or_app_total\": \"229.00\",\n        \"or_app_markup\": null,\n        \"or_agent_markup\": null,\n        \"or_client_total\": \"229.00\",\n        \"or_client_currency\": \"USD\",\n        \"or_client_currency_rate\": \"1.00000\",\n        \"or_status_name\": \"Processing\",\n        \"or_pay_status_name\": \"Not paid\",\n        \"or_client_currency_symbol\": \"USD\",\n        \"or_files\": [],\n        \"or_request_uid\": \"OE96040\",\n        \"billing_info\": [\n            {\n                \"bi_first_name\": \"Barbara Elmore\",\n                \"bi_last_name\": \"T\",\n                \"bi_middle_name\": \"\",\n                \"bi_company_name\": null,\n                \"bi_address_line1\": \"1013 Weda Cir\",\n                \"bi_address_line2\": null,\n                \"bi_city\": \"Mayfield\",\n                \"bi_state\": \"KY\",\n                \"bi_country\": \"US\",\n                \"bi_zip\": \"99999\",\n                \"bi_contact_phone\": \"+19074861000\", -- deprecated, will be removed soon\n                \"bi_contact_email\": \"mike.kane@techork.com\", -- deprecated, will be removed soon\n                \"bi_contact_name\": null, -- deprecated, will be removed soon\n                \"bi_payment_method_id\": 1,\n                \"bi_country_name\": \"United States of America\",\n                \"bi_payment_method_name\": \"Credit / Debit Card\"\n            }\n        ],\n        \"quotes\": [\n            {\n                \"pq_gid\": \"80e1ebef3057d60ff3870fe0a1eb83ee\",\n                \"pq_name\": \"\",\n                \"pq_order_id\": 110,\n                \"pq_description\": null,\n                \"pq_status_id\": 3,\n                \"pq_price\": 209,\n                \"pq_origin_price\": 209,\n                \"pq_client_price\": 209,\n                \"pq_service_fee_sum\": 0,\n                \"pq_origin_currency\": \"USD\",\n                \"pq_client_currency\": \"USD\",\n                \"pq_status_name\": \"Applied\",\n                \"pq_files\": [],\n                \"data\": {\n                    \"fq_flight_id\": 49,\n                    \"fq_source_id\": null,\n                    \"fq_product_quote_id\": 162,\n                    \"fq_gds\": \"T\",\n                    \"fq_gds_pcc\": \"E9V\",\n                    \"fq_gds_offer_id\": null,\n                    \"fq_type_id\": 0,\n                    \"fq_cabin_class\": \"E\",\n                    \"fq_trip_type_id\": 1,\n                    \"fq_main_airline\": \"LO\",\n                    \"fq_fare_type_id\": 1,\n                    \"fq_origin_search_data\": \"{\\\"key\\\":\\\"2_U0FMMTAxKlkyMDAwL0tJVkxPTjIwMjEtMDktMTcqTE9+I0xPNTE0I0xPMjgxfmxjOmVuX3Vz\\\",\\\"routingId\\\":1,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2021-03-11\\\",\\\"totalPrice\\\":209,\\\"totalTax\\\":123,\\\"comm\\\":0,\\\"isCk\\\":false,\\\"markupId\\\":0,\\\"markupUid\\\":\\\"\\\",\\\"markup\\\":0},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"ADT\\\",\\\"cnt\\\":2,\\\"baseFare\\\":43,\\\"pubBaseFare\\\":43,\\\"baseTax\\\":61.5,\\\"markup\\\":0,\\\"comm\\\":0,\\\"price\\\":104.5,\\\"tax\\\":61.5,\\\"oBaseFare\\\":{\\\"amount\\\":43,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":61.5,\\\"currency\\\":\\\"USD\\\"}}},\\\"penalties\\\":{\\\"exchange\\\":true,\\\"refund\\\":false,\\\"list\\\":[{\\\"type\\\":\\\"ex\\\",\\\"applicability\\\":\\\"before\\\",\\\"permitted\\\":true,\\\"amount\\\":0},{\\\"type\\\":\\\"ex\\\",\\\"applicability\\\":\\\"after\\\",\\\"permitted\\\":true,\\\"amount\\\":0},{\\\"type\\\":\\\"re\\\",\\\"applicability\\\":\\\"before\\\",\\\"permitted\\\":false},{\\\"type\\\":\\\"re\\\",\\\"applicability\\\":\\\"after\\\",\\\"permitted\\\":false}]},\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segments\\\":[{\\\"segmentId\\\":1,\\\"departureTime\\\":\\\"2021-09-17 14:30\\\",\\\"arrivalTime\\\":\\\"2021-09-17 15:20\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"514\\\",\\\"bookingClass\\\":\\\"V\\\",\\\"duration\\\":110,\\\"departureAirportCode\\\":\\\"KIV\\\",\\\"departureAirportTerminal\\\":\\\"\\\",\\\"arrivalAirportCode\\\":\\\"WAW\\\",\\\"arrivalAirportTerminal\\\":\\\"\\\",\\\"operatingAirline\\\":\\\"LO\\\",\\\"airEquipType\\\":\\\"DH4\\\",\\\"marketingAirline\\\":\\\"LO\\\",\\\"marriageGroup\\\":\\\"I\\\",\\\"mileage\\\":508,\\\"cabin\\\":\\\"Y\\\",\\\"cabinIsBasic\\\":true,\\\"brandId\\\":\\\"685421\\\",\\\"brandName\\\":\\\"ECONOMY SAVER\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"V1SAV28\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"carryOn\\\":true,\\\"allowPieces\\\":0}},\\\"recheckBaggage\\\":false},{\\\"segmentId\\\":2,\\\"departureTime\\\":\\\"2021-09-18 07:30\\\",\\\"arrivalTime\\\":\\\"2021-09-18 09:25\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"281\\\",\\\"bookingClass\\\":\\\"V\\\",\\\"duration\\\":175,\\\"departureAirportCode\\\":\\\"WAW\\\",\\\"departureAirportTerminal\\\":\\\"\\\",\\\"arrivalAirportCode\\\":\\\"LHR\\\",\\\"arrivalAirportTerminal\\\":\\\"2\\\",\\\"operatingAirline\\\":\\\"LO\\\",\\\"airEquipType\\\":\\\"738\\\",\\\"marketingAirline\\\":\\\"LO\\\",\\\"marriageGroup\\\":\\\"O\\\",\\\"mileage\\\":893,\\\"cabin\\\":\\\"Y\\\",\\\"cabinIsBasic\\\":true,\\\"brandId\\\":\\\"685421\\\",\\\"brandName\\\":\\\"ECONOMY SAVER\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"V1SAV28\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"carryOn\\\":true,\\\"allowPieces\\\":0}},\\\"recheckBaggage\\\":false}],\\\"duration\\\":1255}],\\\"maxSeats\\\":5,\\\"paxCnt\\\":2,\\\"validatingCarrier\\\":\\\"LO\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"cons\\\":\\\"GTT\\\",\\\"fareType\\\":\\\"PUB\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"currencies\\\":[\\\"USD\\\"],\\\"currencyRates\\\":{\\\"USDUSD\\\":{\\\"from\\\":\\\"USD\\\",\\\"to\\\":\\\"USD\\\",\\\"rate\\\":1}},\\\"keys\\\":{\\\"travelport\\\":{\\\"traceId\\\":\\\"b3355dee-c859-4617-bca4-50046effc830\\\",\\\"availabilitySources\\\":\\\"S,S\\\",\\\"type\\\":\\\"T\\\"},\\\"seatHoldSeg\\\":{\\\"trip\\\":0,\\\"segment\\\":0,\\\"seats\\\":5}},\\\"ngsFeatures\\\":{\\\"stars\\\":1,\\\"name\\\":\\\"ECONOMY SAVER\\\",\\\"list\\\":[]},\\\"meta\\\":{\\\"eip\\\":0,\\\"noavail\\\":false,\\\"searchId\\\":\\\"U0FMMTAxWTIwMDB8S0lWTE9OMjAyMS0wOS0xNw==\\\",\\\"lang\\\":\\\"en\\\",\\\"rank\\\":6,\\\"cheapest\\\":true,\\\"fastest\\\":false,\\\"best\\\":false,\\\"bags\\\":0,\\\"country\\\":\\\"us\\\"},\\\"price\\\":104.5,\\\"originRate\\\":1,\\\"stops\\\":[1],\\\"time\\\":[{\\\"departure\\\":\\\"2021-09-17 14:30\\\",\\\"arrival\\\":\\\"2021-09-18 09:25\\\"}],\\\"bagFilter\\\":\\\"\\\",\\\"airportChange\\\":false,\\\"technicalStopCnt\\\":0,\\\"duration\\\":[1255],\\\"totalDuration\\\":1255,\\\"topCriteria\\\":\\\"cheapest\\\",\\\"rank\\\":6}\",\n                    \"fq_last_ticket_date\": \"2021-03-11\",\n                    \"fq_json_booking\": null,\n                    \"fq_ticket_json\": null,\n                    \"fq_type_name\": \"Base\",\n                    \"fq_fare_type_name\": \"Public\",\n                    \"flight\": {\n                        \"fl_product_id\": 78,\n                        \"fl_trip_type_id\": 1,\n                        \"fl_cabin_class\": \"E\",\n                        \"fl_adults\": 2,\n                        \"fl_children\": 0,\n                        \"fl_infants\": 0,\n                        \"fl_trip_type_name\": \"One Way\",\n                        \"fl_cabin_class_name\": \"Economy\"\n                    },\n                    \"trips\": [\n                        {\n                            \"fqt_id\": 103,\n                            \"fqt_uid\": \"fqt6047195e6a882\",\n                            \"fqt_key\": null,\n                            \"fqt_duration\": 1255,\n                            \"segments\": [\n                                {\n                                    \"fqs_uid\": \"fqs6047195e6be4b\",\n                                    \"fqs_departure_dt\": \"2021-09-17 14:30:00\",\n                                    \"fqs_arrival_dt\": \"2021-09-17 15:20:00\",\n                                    \"fqs_stop\": 0,\n                                    \"fqs_flight_number\": 514,\n                                    \"fqs_booking_class\": \"V\",\n                                    \"fqs_duration\": 110,\n                                    \"fqs_departure_airport_iata\": \"KIV\",\n                                    \"fqs_departure_airport_terminal\": \"\",\n                                    \"fqs_arrival_airport_iata\": \"WAW\",\n                                    \"fqs_arrival_airport_terminal\": \"\",\n                                    \"fqs_operating_airline\": \"LO\",\n                                    \"fqs_marketing_airline\": \"LO\",\n                                    \"fqs_air_equip_type\": \"DH4\",\n                                    \"fqs_marriage_group\": \"I\",\n                                    \"fqs_cabin_class\": \"Y\",\n                                    \"fqs_meal\": \"\",\n                                    \"fqs_fare_code\": \"V1SAV28\",\n                                    \"fqs_ticket_id\": null,\n                                    \"fqs_recheck_baggage\": 0,\n                                    \"fqs_mileage\": 508,\n                                    \"departureLocation\": \"Chisinau\",\n                                    \"arrivalLocation\": \"Warsaw\",\n                                    \"operating_airline\": \"LOT Polish Airlines\",\n                                    \"marketing_airline\": \"LOT Polish Airlines\",\n                                    \"baggages\": [\n                                        {\n                                            \"qsb_flight_pax_code_id\": 1,\n                                            \"qsb_flight_quote_segment_id\": 261,\n                                            \"qsb_airline_code\": null,\n                                            \"qsb_allow_pieces\": 0,\n                                            \"qsb_allow_weight\": null,\n                                            \"qsb_allow_unit\": null,\n                                            \"qsb_allow_max_weight\": null,\n                                            \"qsb_allow_max_size\": null\n                                        }\n                                    ]\n                                },\n                                {\n                                    \"fqs_uid\": \"fqs6047195e6d5a0\",\n                                    \"fqs_departure_dt\": \"2021-09-18 07:30:00\",\n                                    \"fqs_arrival_dt\": \"2021-09-18 09:25:00\",\n                                    \"fqs_stop\": 0,\n                                    \"fqs_flight_number\": 281,\n                                    \"fqs_booking_class\": \"V\",\n                                    \"fqs_duration\": 175,\n                                    \"fqs_departure_airport_iata\": \"WAW\",\n                                    \"fqs_departure_airport_terminal\": \"\",\n                                    \"fqs_arrival_airport_iata\": \"LHR\",\n                                    \"fqs_arrival_airport_terminal\": \"2\",\n                                    \"fqs_operating_airline\": \"LO\",\n                                    \"fqs_marketing_airline\": \"LO\",\n                                    \"fqs_air_equip_type\": \"738\",\n                                    \"fqs_marriage_group\": \"O\",\n                                    \"fqs_cabin_class\": \"Y\",\n                                    \"fqs_meal\": \"\",\n                                    \"fqs_fare_code\": \"V1SAV28\",\n                                    \"fqs_ticket_id\": null,\n                                    \"fqs_recheck_baggage\": 0,\n                                    \"fqs_mileage\": 893,\n                                    \"departureLocation\": \"Warsaw\",\n                                    \"arrivalLocation\": \"London\",\n                                    \"operating_airline\": \"LOT Polish Airlines\",\n                                    \"marketing_airline\": \"LOT Polish Airlines\",\n                                    \"baggages\": [\n                                        {\n                                            \"qsb_flight_pax_code_id\": 1,\n                                            \"qsb_flight_quote_segment_id\": 262,\n                                            \"qsb_airline_code\": null,\n                                            \"qsb_allow_pieces\": 0,\n                                            \"qsb_allow_weight\": null,\n                                            \"qsb_allow_unit\": null,\n                                            \"qsb_allow_max_weight\": null,\n                                            \"qsb_allow_max_size\": null\n                                        }\n                                    ]\n                                }\n                            ]\n                        }\n                    ],\n                    \"pax_prices\": [\n                        {\n                            \"qpp_fare\": \"43.00\",\n                            \"qpp_tax\": \"61.50\",\n                            \"qpp_system_mark_up\": \"0.00\",\n                            \"qpp_agent_mark_up\": \"0.00\",\n                            \"qpp_origin_fare\": \"43.00\",\n                            \"qpp_origin_currency\": \"USD\",\n                            \"qpp_origin_tax\": \"61.50\",\n                            \"qpp_client_currency\": \"USD\",\n                            \"qpp_client_fare\": \"43.00\",\n                            \"qpp_client_tax\": \"61.50\",\n                            \"paxType\": \"ADT\"\n                        }\n                    ],\n                    \"paxes\": [\n                        {\n                            \"fp_uid\": \"fp6047195e6767d\",\n                            \"fp_pax_id\": null,\n                            \"fp_pax_type\": \"ADT\",\n                            \"fp_first_name\": \"Alex\",\n                            \"fp_last_name\": \"Grub\",\n                            \"fp_middle_name\": \"\",\n                            \"fp_dob\": \"1963-04-07\"\n                        },\n                        {\n                            \"fp_uid\": \"fp6047195e67b7a\",\n                            \"fp_pax_id\": null,\n                            \"fp_pax_type\": \"ADT\",\n                            \"fp_first_name\": \"Test name\",\n                            \"fp_last_name\": \"Test last name\",\n                            \"fp_middle_name\": \"Test middle name\",\n                            \"fp_dob\": \"1963-04-07\"\n                        },\n                        {\n                            \"fp_uid\": \"fp6047302b6966f\",\n                            \"fp_pax_id\": null,\n                            \"fp_pax_type\": \"ADT\",\n                            \"fp_first_name\": null,\n                            \"fp_last_name\": null,\n                            \"fp_middle_name\": null,\n                            \"fp_dob\": null\n                        },\n                        {\n                            \"fp_uid\": \"fp6047302b69a86\",\n                            \"fp_pax_id\": null,\n                            \"fp_pax_type\": \"ADT\",\n                            \"fp_first_name\": null,\n                            \"fp_last_name\": null,\n                            \"fp_middle_name\": null,\n                            \"fp_dob\": null\n                        },\n                        {\n                            \"fp_uid\": \"fp60473031c44c4\",\n                            \"fp_pax_id\": null,\n                            \"fp_pax_type\": \"ADT\",\n                            \"fp_first_name\": null,\n                            \"fp_last_name\": null,\n                            \"fp_middle_name\": null,\n                            \"fp_dob\": null\n                        },\n                        {\n                            \"fp_uid\": \"fp60473031c47b9\",\n                            \"fp_pax_id\": null,\n                            \"fp_pax_type\": \"ADT\",\n                            \"fp_first_name\": null,\n                            \"fp_last_name\": null,\n                            \"fp_middle_name\": null,\n                            \"fp_dob\": null\n                        }\n                    ]\n                },\n                \"product\": {\n                    \"pr_gid\": null,\n                    \"pr_type_id\": 1,\n                    \"pr_name\": \"\",\n                    \"pr_lead_id\": 513110,\n                    \"pr_description\": \"\",\n                    \"pr_status_id\": null,\n                    \"pr_service_fee_percent\": null,\n                    \"holder\": {\n                        \"ph_first_name\": \"test\",\n                        \"ph_last_name\": \"test\",\n                        \"ph_email\": \"test@test.test\",\n                        \"ph_phone_number\": \"+19074861000\"\n                    }\n                },\n                \"productQuoteOptions\": [\n                    {\n                        \"pqo_name\": \"Travel Guard\",\n                        \"pqo_description\": \"\",\n                        \"pqo_status_id\": null,\n                        \"pqo_price\": 20,\n                        \"pqo_client_price\": 20,\n                        \"pqo_extra_markup\": null,\n                        \"pqo_request_data\": null,\n                        \"productOption\": {\n                            \"po_key\": \"travelGuard\",\n                            \"po_name\": \"Travel Guard\",\n                            \"po_description\": \"\"\n                        }\n                    }\n                ]\n            }\n        ]\n    },\n    \"technical\": {\n        \"action\": \"v2/order/view\",\n        \"response_id\": 507,\n        \"request_dt\": \"2021-03-09 12:10:22\",\n        \"response_dt\": \"2021-03-09 12:10:23\",\n        \"execution_time\": 0.122,\n        \"memory_usage\": 1563368\n    },\n    \"request\": {\n        \"gid\": \"a0758d1d8ded3efe62c465ad36987200\"\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n\"status\": 422,\n\"message\": \"Error\",\n\"errors\": [\n\"Order is not found\"\n],\n\"code\": 12100,\n\"technical\": {\n\"action\": \"v2/order/view\",\n\"response_id\": 397,\n\"request_dt\": \"2021-03-01 17:40:41\",\n\"response_dt\": \"2021-03-01 17:40:41\",\n\"execution_time\": 0.017,\n\"memory_usage\": 212976\n},\n\"request\": {\n\"gid\": \"5287f7f7ff5a28789518db64e946ea67s\"\n}\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n{\n    \"status\": 400,\n    \"message\": \"Load data error\",\n    \"errors\": [\n        \"Not found Order data on POST request\"\n    ],\n    \"code\": \"18300\",\n    \"technical\": {\n        \"action\": \"v2/order/view\",\n        \"response_id\": 11933856,\n        \"request_dt\": \"2020-02-03 12:49:20\",\n        \"response_dt\": \"2020-02-03 12:49:20\",\n        \"execution_time\": 0.017,\n        \"memory_usage\": 114232\n    },\n    \"request\": []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/OrderController.php",
    "groupTitle": "Order"
  },
  {
    "type": "post",
    "url": "/v2/payment/update-bo",
    "title": "Create/Update payments from BO",
    "version": "0.1.0",
    "name": "Update_payment",
    "group": "Payment",
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
            "size": "255",
            "optional": false,
            "field": "fareId",
            "description": "<p>Fare Id (Order identity)</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "payments",
            "description": "<p>Payments data array</p>"
          },
          {
            "group": "Parameter",
            "type": "float",
            "optional": false,
            "field": "payments.pay_amount",
            "description": "<p>Payment amount</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "3",
            "optional": false,
            "field": "payments.pay_currency",
            "description": "<p>Payment currency code (for example USD)</p>"
          },
          {
            "group": "Parameter",
            "type": "date",
            "optional": false,
            "field": "payments.pay_date",
            "description": "<p>Payment date (format Y-m-d)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "1..10",
            "allowedValues": [
              "Capture",
              "Refund",
              "Authorize"
            ],
            "optional": false,
            "field": "payments.pay_type",
            "description": "<p>Payment Type (&quot;Capture&quot;,&quot;Refund&quot;,&quot;Authorize&quot;)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "payments.pay_code",
            "description": "<p>Payment Identity</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "payments.pay_auth_id",
            "description": "<p>Payment transaction ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "100",
            "optional": true,
            "field": "payments.pay_method_key",
            "description": "<p>Payment method key (by default &quot;card&quot;)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "255",
            "optional": true,
            "field": "payments.pay_description",
            "description": "<p>Payment description</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "billingInfo",
            "description": "<p>Billing Info</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": false,
            "field": "billingInfo.first_name",
            "description": "<p>First Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": false,
            "field": "billingInfo.last_name",
            "description": "<p>Last Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": false,
            "field": "billingInfo.middle_name",
            "description": "<p>Middle Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": false,
            "field": "billingInfo.address",
            "description": "<p>Address</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 2",
            "optional": false,
            "field": "billingInfo.country_id",
            "description": "<p>Country Id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 30",
            "optional": false,
            "field": "billingInfo.city",
            "description": "<p>City</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 40",
            "optional": false,
            "field": "billingInfo.state",
            "description": "<p>State</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 10",
            "optional": false,
            "field": "billingInfo.zip",
            "description": "<p>Zip</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 20",
            "optional": false,
            "field": "billingInfo.phone",
            "description": "<p>Phone</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 160",
            "optional": false,
            "field": "billingInfo.email",
            "description": "<p>Email</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "creditCard",
            "description": "<p>Credit Card</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 50",
            "optional": true,
            "field": "creditCard.holder_name",
            "description": "<p>Holder Name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 20",
            "optional": false,
            "field": "creditCard.number",
            "description": "<p>Credit Card Number</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 20",
            "optional": true,
            "field": "creditCard.type",
            "description": "<p>Credit Card type (Visa,Master Card,American Express,Discover,Diners Club,JCB)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 18",
            "optional": false,
            "field": "creditCard.expiration",
            "description": "<p>Credit Card expiration</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "max 4",
            "optional": false,
            "field": "creditCard.cvv",
            "description": "<p>Credit Card cvv</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": " {\n           \"fareId\": \"or6061be5ec5c0e\",\n           \"payments\":[\n               {\n                   \"pay_amount\": 200.21,\n                   \"pay_currency\": \"USD\",\n                   \"pay_auth_id\": 728282,\n                   \"pay_type\": \"Capture\",\n                   \"pay_code\": \"ch_YYYYYYYYYYYYYYYYYYYYY\",\n                   \"pay_date\": \"2021-03-25\",\n                   \"pay_method_key\":\"card\",\n                   \"pay_description\": \"example description\",\n                   \"creditCard\": {\n                       \"holder_name\": \"Tester holder\",\n                       \"number\": \"111**********111\",\n                       \"type\": \"Visa\",\n                       \"expiration\": \"07 / 23\",\n                       \"cvv\": \"123\"\n                   },\n                   \"billingInfo\": {\n                       \"first_name\": \"Hobbit\",\n                       \"middle_name\": \"Hard\",\n                       \"last_name\": \"Lover\",\n                       \"address\": \"1013 Weda Cir\",\n                       \"country_id\": \"US\",\n                       \"city\": \"Gotham City\",\n                       \"state\": \"KY\",\n                       \"zip\": \"99999\",\n                       \"phone\": \"+19074861000\",\n                       \"email\": \"barabara@test.com\"\n                   }\n               },\n               {\n                   \"pay_amount\":200.21,\n                   \"pay_currency\":\"USD\",\n                   \"pay_auth_id\": 728283,\n                   \"pay_type\": \"Refund\",\n                   \"pay_code\":\"xx_XXXXXXXXXXXXXXXXXXXX\",\n                   \"pay_date\":\"2021-03-25\",\n                   \"pay_method_key\":\"card\",\n                   \"pay_description\": \"client is fraud\",\n                   \"creditCard\": {\n                       \"holder_name\": \"Tester holder\",\n                       \"number\": \"111**********111\",\n                       \"type\": \"Visa\",\n                       \"expiration\": \"07 / 23\",\n                       \"cvv\": \"321\"\n                   },\n                   \"billingInfo\": {\n                       \"first_name\": \"Eater\",\n                       \"middle_name\": \"Fresh\",\n                       \"last_name\": \"Sausage\",\n                       \"address\": \"1013 Weda Cir\",\n                       \"country_id\": \"US\",\n                       \"city\": \"Gotham City\",\n                       \"state\": \"KY\",\n                       \"zip\": \"99999\",\n                       \"phone\": \"+19074861000\",\n                       \"email\": \"test@test.com\"\n                   }\n               }\n           ]\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n       \"status\": 200,\n       \"message\": \"OK\",\n       \"data\": {\n           \"resultMessage\": \"Transaction processed codes(728282,728283)\"\n       },\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request\n{\n       \"status\": 400,\n       \"message\": \"Payment save is failed. Transaction already exist. Code:(728283)\",\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (500):",
          "content": "HTTP/1.1 500 Internal Server Error\n{\n       \"status\": \"Failed\",\n       \"source\": {\n           \"type\": 1,\n           \"status\": 500\n       },\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "HTTP/1.1 422 Unprocessable entity\n{\n       \"status\": \"Failed\",\n       \"message\": \"Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received\",\n       \"errors\": [\n             \"Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received\"\n       ],\n       \"code\": 0,\n       \"technical\": {\n          ...\n       },\n       \"request\": {\n          ...\n       }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/PaymentController.php",
    "groupTitle": "Payment"
  },
  {
    "type": "post",
    "url": "/v1/quote/create",
    "title": "Create Quote",
    "version": "0.1.0",
    "name": "CreateQuote",
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
            "optional": true,
            "field": "apiKey",
            "description": "<p>API Key for Project</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "Lead",
            "description": "<p>Lead data array</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Lead.uid",
            "description": "<p>uid</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "Lead.market_info_id",
            "description": "<p>market_info_id</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "Lead.bo_flight_id",
            "description": "<p>bo_flight_id</p>"
          },
          {
            "group": "Parameter",
            "type": "float",
            "optional": true,
            "field": "Lead.final_profit",
            "description": "<p>final_profit</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "Quote",
            "description": "<p>Quote data array</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Quote.uid",
            "description": "<p>uid</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Quote.record_locator",
            "description": "<p>record_locator</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Quote.pcc",
            "description": "<p>pcc</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Quote.cabin",
            "description": "<p>cabin</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "1",
            "optional": false,
            "field": "Quote.gds",
            "description": "<p>gds</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Quote.trip_type",
            "description": "<p>trip_type</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Quote.main_airline_code",
            "description": "<p>main_airline_code</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Quote.reservation_dump",
            "description": "<p>reservation_dump</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "Quote.status",
            "description": "<p>status</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Quote.check_payment",
            "description": "<p>check_payment</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Quote.fare_type",
            "description": "<p>fare_type</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "Quote.employee_name",
            "description": "<p>employee_name</p>"
          },
          {
            "group": "Parameter",
            "type": "bool",
            "optional": true,
            "field": "Quote.created_by_seller",
            "description": "<p>created_by_seller</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "Quote.type_id",
            "description": "<p>type_id</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": true,
            "field": "Quote.prod_types[]",
            "description": "<p>Quote labels</p>"
          },
          {
            "group": "Parameter",
            "type": "object",
            "optional": false,
            "field": "QuotePrice[]",
            "description": "<p>QuotePrice data array</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "QuotePrice.uid",
            "description": "<p>uid</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "QuotePrice.passenger_type",
            "description": "<p>passenger_type</p>"
          },
          {
            "group": "Parameter",
            "type": "float",
            "optional": true,
            "field": "QuotePrice.selling",
            "description": "<p>selling</p>"
          },
          {
            "group": "Parameter",
            "type": "float",
            "optional": true,
            "field": "QuotePrice.net",
            "description": "<p>net</p>"
          },
          {
            "group": "Parameter",
            "type": "float",
            "optional": true,
            "field": "QuotePrice.fare",
            "description": "<p>fare</p>"
          },
          {
            "group": "Parameter",
            "type": "float",
            "optional": true,
            "field": "QuotePrice.taxes",
            "description": "<p>taxes</p>"
          },
          {
            "group": "Parameter",
            "type": "float",
            "optional": true,
            "field": "QuotePrice.mark_up",
            "description": "<p>mark_up</p>"
          },
          {
            "group": "Parameter",
            "type": "float",
            "optional": true,
            "field": "QuotePrice.extra_mark_up",
            "description": "<p>extra_mark_up</p>"
          },
          {
            "group": "Parameter",
            "type": "float",
            "optional": true,
            "field": "QuotePrice.service_fee",
            "description": "<p>service_fee</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n     \"apiKey\": \"d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd\",\n     \"Lead\": {\n         \"uid\": \"5de486f15f095\",\n         \"market_info_id\": 52,\n         \"bo_flight_id\": 0,\n         \"final_profit\": 0\n     },\n     \"Quote\": {\n         \"uid\": \"5f207ec201b99\",\n         \"record_locator\": null,\n         \"pcc\": \"0RY9\",\n         \"cabin\": \"E\",\n         \"gds\": \"S\",\n         \"trip_type\": \"RT\",\n         \"main_airline_code\": \"UA\",\n         \"reservation_dump\": \"1 KL6123V 15OCT Q MCOAMS SS1   801P 1100A  16OCT F /DCKL /E \\n 2 KL1009L 18OCT S AMSLHR SS1  1015A 1045A /DCKL /E\",\n         \"status\": 1,\n         \"check_payment\": \"1\",\n         \"fare_type\": \"TOUR\",\n         \"employee_name\": \"Barry\",\n         \"created_by_seller\": false,\n         \"type_id\" : 0,\n         \"prod_types\" : [\"SEP\", \"TOUR\"]\n     },\n     \"QuotePrice\": [\n         {\n             \"uid\": \"expert.5f207ec222c86\",\n             \"passenger_type\": \"ADT\",\n             \"selling\": 696.19,\n             \"net\": 622.65,\n             \"fare\": 127,\n             \"taxes\": 495.65,\n             \"mark_up\": 50,\n             \"extra_mark_up\": 0,\n             \"service_fee\": 23.54\n         }\n     ]\n}",
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
            "description": "<p>Response Date &amp; Time</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n {\n     \"status\": \"Success\",\n     \"action\": \"v1/quote/create\",\n     \"response_id\": 11926893,\n     \"request_dt\": \"2020-09-22 05:05:54\",\n     \"response_dt\": \"2020-09-22 05:05:54\",\n     \"execution_time\": 0.193,\n     \"memory_usage\": 1647440\n }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 404 Not Found\n{\n    \"name\": \"Not Found\",\n    \"message\": \"Already Exist Quote UID: 5f207ec201b19\",\n    \"code\": 2,\n    \"status\": 404,\n    \"type\": \"yii\\\\web\\\\NotFoundHttpException\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/QuoteController.php",
    "groupTitle": "Quotes"
  },
  {
    "type": "post",
    "url": "/v1/quote/create-data",
    "title": "Create Flight Quote by origin search data",
    "version": "1.0.0",
    "name": "CreateQuoteData",
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
            "type": "Integer",
            "optional": false,
            "field": "lead_id",
            "description": "<p>Lead Id</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "origin_search_data",
            "description": "<p>Origin Search Data from air search service <code>Valid JSON</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "size": "..max 50",
            "optional": true,
            "field": "provider_project_key",
            "description": "<p>Project Key</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n            \"lead_id\": 513145,\n            \"origin_search_data\": \"{\\\"key\\\":\\\"2_U0FMMTAxKlkyMTAwL0tJVkxPTjIwMjEtMTEtMTcqUk9+I1JPMjAyI1JPMzkxfmxjOmVuX3Vz\\\",\\\"routingId\\\":1,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2021-05-05\\\",\\\"totalPrice\\\":408.9,\\\"totalTax\\\":99.9,\\\"comm\\\":0,\\\"isCk\\\":false,\\\"markupId\\\":0,\\\"markupUid\\\":\\\"\\\",\\\"markup\\\":0},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"JWZ\\\",\\\"cnt\\\":2,\\\"baseFare\\\":103,\\\"pubBaseFare\\\":103,\\\"baseTax\\\":33.3,\\\"markup\\\":0,\\\"comm\\\":0,\\\"price\\\":136.3,\\\"tax\\\":33.3,\\\"oBaseFare\\\":{\\\"amount\\\":103,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":33.3,\\\"currency\\\":\\\"USD\\\"}},\\\"CHD\\\":{\\\"codeAs\\\":\\\"JWC\\\",\\\"cnt\\\":1,\\\"baseFare\\\":103,\\\"pubBaseFare\\\":103,\\\"baseTax\\\":33.3,\\\"markup\\\":0,\\\"comm\\\":0,\\\"price\\\":136.3,\\\"tax\\\":33.3,\\\"oBaseFare\\\":{\\\"amount\\\":103,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":33.3,\\\"currency\\\":\\\"USD\\\"}}},\\\"penalties\\\":{\\\"exchange\\\":true,\\\"refund\\\":false,\\\"list\\\":[{\\\"type\\\":\\\"ex\\\",\\\"applicability\\\":\\\"before\\\",\\\"permitted\\\":true,\\\"amount\\\":72,\\\"oAmount\\\":{\\\"amount\\\":72,\\\"currency\\\":\\\"USD\\\"}},{\\\"type\\\":\\\"ex\\\",\\\"applicability\\\":\\\"after\\\",\\\"permitted\\\":true,\\\"amount\\\":72,\\\"oAmount\\\":{\\\"amount\\\":72,\\\"currency\\\":\\\"USD\\\"}},{\\\"type\\\":\\\"re\\\",\\\"applicability\\\":\\\"before\\\",\\\"permitted\\\":false},{\\\"type\\\":\\\"re\\\",\\\"applicability\\\":\\\"after\\\",\\\"permitted\\\":false}]},\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segments\\\":[{\\\"segmentId\\\":1,\\\"departureTime\\\":\\\"2021-11-17 09:30\\\",\\\"arrivalTime\\\":\\\"2021-11-17 10:45\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"202\\\",\\\"bookingClass\\\":\\\"E\\\",\\\"duration\\\":75,\\\"departureAirportCode\\\":\\\"KIV\\\",\\\"departureAirportTerminal\\\":\\\"\\\",\\\"arrivalAirportCode\\\":\\\"OTP\\\",\\\"arrivalAirportTerminal\\\":\\\"\\\",\\\"operatingAirline\\\":\\\"RO\\\",\\\"airEquipType\\\":\\\"AT7\\\",\\\"marketingAirline\\\":\\\"RO\\\",\\\"marriageGroup\\\":\\\"I\\\",\\\"mileage\\\":215,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"EOWSVRMD\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"carryOn\\\":true,\\\"allowPieces\\\":1},\\\"CHD\\\":{\\\"carryOn\\\":true,\\\"allowPieces\\\":1}},\\\"recheckBaggage\\\":false},{\\\"segmentId\\\":2,\\\"departureTime\\\":\\\"2021-11-17 12:20\\\",\\\"arrivalTime\\\":\\\"2021-11-17 14:05\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"391\\\",\\\"bookingClass\\\":\\\"E\\\",\\\"duration\\\":225,\\\"departureAirportCode\\\":\\\"OTP\\\",\\\"departureAirportTerminal\\\":\\\"\\\",\\\"arrivalAirportCode\\\":\\\"LHR\\\",\\\"arrivalAirportTerminal\\\":\\\"4\\\",\\\"operatingAirline\\\":\\\"RO\\\",\\\"airEquipType\\\":\\\"73H\\\",\\\"marketingAirline\\\":\\\"RO\\\",\\\"marriageGroup\\\":\\\"O\\\",\\\"mileage\\\":1292,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"EOWSVRGB\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"carryOn\\\":true,\\\"allowPieces\\\":1},\\\"CHD\\\":{\\\"carryOn\\\":true,\\\"allowPieces\\\":1}},\\\"recheckBaggage\\\":false}],\\\"duration\\\":395}],\\\"maxSeats\\\":3,\\\"paxCnt\\\":3,\\\"validatingCarrier\\\":\\\"RO\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"DVI\\\",\\\"cons\\\":\\\"GTT\\\",\\\"fareType\\\":\\\"PUB\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"currencies\\\":[\\\"USD\\\"],\\\"currencyRates\\\":{\\\"USDUSD\\\":{\\\"from\\\":\\\"USD\\\",\\\"to\\\":\\\"USD\\\",\\\"rate\\\":1}},\\\"keys\\\":{\\\"travelport\\\":{\\\"traceId\\\":\\\"908f70b5-cbe1-4800-89e2-1f0496cc1502\\\",\\\"availabilitySources\\\":\\\"A,A\\\",\\\"type\\\":\\\"T\\\"},\\\"seatHoldSeg\\\":{\\\"trip\\\":0,\\\"segment\\\":0,\\\"seats\\\":3}},\\\"meta\\\":{\\\"eip\\\":0,\\\"noavail\\\":false,\\\"searchId\\\":\\\"U0FMMTAxWTIxMDB8S0lWTE9OMjAyMS0xMS0xNw==\\\",\\\"lang\\\":\\\"en\\\",\\\"group1\\\":\\\"KIVLON:RORO:0:408.90\\\",\\\"rank\\\":10,\\\"cheapest\\\":true,\\\"fastest\\\":false,\\\"best\\\":true,\\\"bags\\\":1,\\\"country\\\":\\\"us\\\",\\\"prod_types\\\":[\\\"PUB\\\"]}}\",\n            \"provider_project_key\": \"hop2\"\n        }",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n            \"status\": 200,\n            \"message\": \"OK\",\n            \"data\": {\n                \"quote_uid\": \"609259bfe52b9\"\n            }\n        }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n            \"status\": 422,\n            \"message\": \"Validation error\",\n            \"errors\": {\n                \"lead_id\": [\n                    \"Lead Id is invalid.\"\n                ]\n            },\n            \"code\": 0\n        }",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Validation Error\n{\n            \"status\": 422,\n            \"message\": \"Error\",\n            \"errors\": [\n                \"Not found project relation by key: ovago\"\n            ],\n            \"code\": 0\n        }",
          "type": "json"
        },
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n\n{\n            \"status\": 400,\n            \"message\": \"Load data error\",\n            \"errors\": [\n                \"Not found data on POST request\"\n            ],\n            \"code\": 0\n        }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/QuoteController.php",
    "groupTitle": "Quotes"
  },
  {
    "type": "post",
    "url": "/v1/quote/create-key",
    "title": "Create Flight Quote by key",
    "version": "1.0.0",
    "name": "CreateQuoteKey",
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
            "type": "Integer",
            "optional": false,
            "field": "lead_id",
            "description": "<p>Lead Id</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "offer_search_key",
            "description": "<p>Search key</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "size": "..max 50",
            "optional": true,
            "field": "provider_project_key",
            "description": "<p>Project Key</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n            \"lead_id\": 513146,\n            \"offer_search_key\": \"2_U0FMMTAxKlkyMTAwL0tJVkxPTjIwMjEtMTEtMTcqUk9+I1JPMjAyI1JPMzkxfmxjOmVuX3Vz\",\n            \"provider_project_key\": \"hop2\"\n        }",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n            \"status\": 200,\n            \"message\": \"OK\",\n            \"data\": {\n                \"quote_uid\": \"609259bfe52b9\"\n            }\n        }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n            \"status\": 422,\n            \"message\": \"Validation error\",\n            \"errors\": {\n                \"lead_id\": [\n                    \"Lead Id is invalid.\"\n                ]\n            },\n            \"code\": 0\n        }",
          "type": "json"
        },
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Validation Error\n{\n            \"status\": 422,\n            \"message\": \"Error\",\n            \"errors\": [\n                \"Not found project relation by key: ovago\"\n            ],\n            \"code\": 0\n        }",
          "type": "json"
        },
        {
          "title": "Error-Response (400):",
          "content": "\nHTTP/1.1 400 Bad Request\n\n{\n            \"status\": 400,\n            \"message\": \"Load data error\",\n            \"errors\": [\n                \"Not found data on POST request\"\n            ],\n            \"code\": 0\n        }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/QuoteController.php",
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
            "type": "integer",
            "optional": false,
            "field": "lead_id",
            "description": "<p>Lead ID</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "lead_uid",
            "description": "<p>Lead UID</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "client_id",
            "description": "<p>Client ID</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "lead_type",
            "description": "<p><code>TYPE_ALTERNATIVE = 2, TYPE_FAILED_BOOK = 3</code></p>"
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
          "content": "HTTP/1.1 200 OK\n{\n  \"status\": \"Success\",\n  \"itinerary\": {\n      \"typeId\": 2,\n      \"typeName\": \"Alternative\",\n      \"tripType\": \"OW\",\n      \"mainCarrier\": \"WOW air\",\n      \"trips\": [\n          {\n              \"segments\": [\n                  {\n                      \"carrier\": \"WW\",\n                      \"airlineName\": \"WOW air\",\n                      \"departureAirport\": \"BOS\",\n                      \"arrivalAirport\": \"KEF\",\n                      \"departureDateTime\": {\n                          \"date\": \"2018-09-19 19:00:00.000000\",\n                          \"timezone_type\": 3,\n                          \"timezone\": \"UTC\"\n                      },\n                      \"arrivalDateTime\": {\n                          \"date\": \"2018-09-20 04:30:00.000000\",\n                          \"timezone_type\": 3,\n                          \"timezone\": \"UTC\"\n                      },\n                      \"flightNumber\": \"126\",\n                      \"bookingClass\": \"O\",\n                      \"departureCity\": \"Boston\",\n                      \"arrivalCity\": \"Reykjavik\",\n                      \"flightDuration\": 330,\n                      \"layoverDuration\": 0,\n                      \"cabin\": \"E\",\n                      \"departureCountry\": \"United States\",\n                      \"arrivalCountry\": \"Iceland\"\n                  },\n                  {\n                      \"carrier\": \"WW\",\n                      \"airlineName\": \"WOW air\",\n                      \"departureAirport\": \"KEF\",\n                      \"arrivalAirport\": \"LGW\",\n                      \"departureDateTime\": {\n                          \"date\": \"2018-09-20 15:30:00.000000\",\n                          \"timezone_type\": 3,\n                          \"timezone\": \"UTC\"\n                      },\n                      \"arrivalDateTime\": {\n                          \"date\": \"2018-09-20 19:50:00.000000\",\n                          \"timezone_type\": 3,\n                          \"timezone\": \"UTC\"\n                      },\n                      \"flightNumber\": \"814\",\n                      \"bookingClass\": \"N\",\n                      \"departureCity\": \"Reykjavik\",\n                      \"arrivalCity\": \"London\",\n                      \"flightDuration\": 200,\n                      \"layoverDuration\": 660,\n                      \"cabin\": \"E\",\n                      \"departureCountry\": \"Iceland\",\n                      \"arrivalCountry\": \"United Kingdom\"\n                  }\n              ],\n              \"totalDuration\": 1190,\n              \"routing\": \"BOS-KEF-LGW\",\n              \"title\": \"Boston - London\"\n          }\n      ],\n      \"price\": {\n          \"detail\": {\n              \"ADT\": {\n                  \"selling\": 350.2,\n                  \"fare\": 237,\n                  \"taxes\": 113.2,\n                  \"tickets\": 1\n              }\n          },\n          \"tickets\": 1,\n          \"selling\": 350.2,\n          \"amountPerPax\": 350.2,\n          \"fare\": 237,\n          \"mark_up\": 0,\n          \"taxes\": 113.2,\n          \"currency\": \"USD\",\n          \"isCC\": false\n      }\n  },\n \"itineraryOrigin\": {\n     \"uid\": \"5f207ec202212\",\n     \"typeId\": 1,\n     \"typeName\": \"Original\",\n     \"tripType\": \"OW\",\n     \"mainCarrier\": \"WOW air\",\n     \"trips\": [\n          {\n              \"segments\": [\n                  {\n                      \"carrier\": \"WW\",\n                      \"airlineName\": \"WOW air\",\n                      \"departureAirport\": \"BOS\",\n                      \"arrivalAirport\": \"KEF\",\n                      \"departureDateTime\": {\n                          \"date\": \"2018-09-19 19:00:00.000000\",\n                          \"timezone_type\": 3,\n                          \"timezone\": \"UTC\"\n                      },\n                      \"arrivalDateTime\": {\n                          \"date\": \"2018-09-20 04:30:00.000000\",\n                          \"timezone_type\": 3,\n                          \"timezone\": \"UTC\"\n                      },\n                      \"flightNumber\": \"126\",\n                      \"bookingClass\": \"O\",\n                      \"departureCity\": \"Boston\",\n                      \"arrivalCity\": \"Reykjavik\",\n                      \"flightDuration\": 330,\n                      \"layoverDuration\": 0,\n                      \"cabin\": \"E\",\n                      \"departureCountry\": \"United States\",\n                      \"arrivalCountry\": \"Iceland\"\n                  }\n              ],\n              \"totalDuration\": 1190,\n              \"routing\": \"BOS-KEF\",\n              \"title\": \"Boston - London\"\n          }\n      ],\n      \"price\": {\n          \"detail\": {\n              \"ADT\": {\n                  \"selling\": 350.2,\n                  \"fare\": 237,\n                  \"taxes\": 113.2,\n                  \"tickets\": 1\n              }\n          },\n          \"tickets\": 1,\n          \"selling\": 350.2,\n          \"amountPerPax\": 350.2,\n          \"fare\": 237,\n          \"mark_up\": 0,\n          \"taxes\": 113.2,\n          \"currency\": \"USD\",\n          \"isCC\": false\n      }\n  },\n  \"errors\": [],\n  \"uid\": \"5b7424e858e91\",\n  \"lead_id\": 123456,\n  \"lead_uid\": \"00jhk0017\",\n  \"client_id\": 1034,\n  \"client\": {\n      \"id\": 1034,\n      \"uuid\": \"35009a79-1a05-49d7-b876-2b884d0f825b\"\n   },\n  \"lead_delayed_charge\": 0,\n  \"lead_status\": \"sold\",\n  \"lead_type\": 2,\n  \"booked_quote_uid\": \"5b8ddfc56a15c\",\n  \"source_code\": \"38T556\",\n  \"check_payment\": true,\n  \"agentName\": \"admin\",\n  \"agentEmail\": \"assistant@wowfare.com\",\n  \"agentDirectLine\": \"+1 888 946 3882\",\n  \"visitor_log\": {\n      \"vl_source_cid\": \"string_abc\",\n      \"vl_ga_client_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n      \"vl_ga_user_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n      \"vl_customer_id\": \"3\",\n      \"vl_gclid\": \"gclid=TeSter-123#bookmark\",\n      \"vl_dclid\": \"CJKu8LrQxd4CFQ1qwQodmJIElw\",\n      \"vl_utm_source\": \"newsletter4\",\n      \"vl_utm_medium\": \"string_abc\",\n      \"vl_utm_campaign\": \"string_abc\",\n      \"vl_utm_term\": \"string_abc\",\n      \"vl_utm_content\": \"string_abc\",\n      \"vl_referral_url\": \"string_abc\",\n      \"vl_location_url\": \"string_abc\",\n      \"vl_user_agent\": \"string_abc\",\n      \"vl_ip_address\": \"127.0.0.1\",\n      \"vl_visit_dt\": \"2020-02-14 12:00:00\",\n      \"vl_created_dt\": \"2020-02-28 17:17:33\"\n  },\n \"lead\": {\n      \"additionalInformation\": [\n          {\n             \"pnr\": \"example_pnr\",\n              \"bo_sale_id\": \"example_sale_id\",\n             \"vtf_processed\": null,\n             \"tkt_processed\": null,\n             \"exp_processed\": null,\n             \"passengers\": [],\n             \"paxInfo\": []\n         }\n     ],\n     \"lead_data\": [\n         {\n             \"ld_field_key\": \"kayakclickid\",\n             \"ld_field_value\": \"example_value132\"\n         }\n     ]\n },\n  \"action\": \"v1/quote/get-info\",\n  \"response_id\": 173,\n  \"request_dt\": \"2018-08-16 06:42:03\",\n  \"response_dt\": \"2018-08-16 06:42:03\"\n}",
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
  },
  {
    "type": "post",
    "url": "/v1/offer-email/send-quote",
    "title": "Offer email Send Quote",
    "version": "0.1.0",
    "name": "SendQuote",
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
            "field": "quote_uid",
            "description": "<p>Quote UID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "template_key",
            "description": "<p>Template key</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "email_from",
            "description": "<p>Email from</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "email_from_name",
            "description": "<p>Email from name</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "email_to",
            "description": "<p>Email to</p>"
          },
          {
            "group": "Parameter",
            "type": "json",
            "optional": true,
            "field": "additional_data",
            "description": "<p>Additional data</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "5",
            "optional": true,
            "field": "language_id",
            "description": "<p>Language Id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "2",
            "optional": true,
            "field": "market_country_code",
            "description": "<p>Market country code</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n   \"quote_uid\": \"60910028642b8\",\n   \"template_key\": \"cl_offer\",\n   \"email_from\": \"from@test.com\",\n   \"email_from_name\": \"Tester\",\n   \"email_to\": \"to@test.com\",\n   \"language_id\": \"en-US\",\n   \"market_country_code\": \"RU\",\n   \"additional_data\": [\n       {\n           \"code\": \"PR\",\n           \"airline\": \"Philippine Airlines\"\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n   \"status\": 200,\n   \"message\": \"OK\",\n   \"data\": {\n       \"result\": \"Email sending. Mail ID(427561)\"\n   }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 404 Not Found\n{\n   \"status\": 422,\n   \"message\": \"Validation error\",\n   \"errors\": {\n       \"quote_uid\": [\n           \"Quote not found by Uid(60910028642b1)\"\n       ]\n   },\n   \"code\": 0\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (400):",
          "content": "HTTP/1.1 400 Bad Request\n{\n   \"name\": \"Bad Request\",\n   \"message\": \"POST data request is empty\",\n   \"code\": 2,\n   \"status\": 400,\n   \"type\": \"yii\\\\web\\\\BadRequestHttpException\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/OfferEmailController.php",
    "groupTitle": "Quotes"
  },
  {
    "type": "post",
    "url": "/v1/offer-sms/send-quote",
    "title": "Offer sms Send Quote",
    "version": "0.1.0",
    "name": "SendSmsQuote",
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
            "field": "quote_uid",
            "description": "<p>Quote UID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "template_key",
            "description": "<p>Template key</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "sms_from",
            "description": "<p>Sms from</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "50",
            "optional": false,
            "field": "sms_to",
            "description": "<p>Sms to</p>"
          },
          {
            "group": "Parameter",
            "type": "json",
            "optional": true,
            "field": "additional_data",
            "description": "<p>Additional data</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "5",
            "optional": true,
            "field": "language_id",
            "description": "<p>Language Id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "2",
            "optional": true,
            "field": "market_country_code",
            "description": "<p>Market country code</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n   \"quote_uid\": \"60910028642b8\",\n   \"template_key\": \"sms_client_offer\",\n   \"sms_from\": \"+16082175601\",\n   \"sms_to\": \"+16082175602\",\n   \"language_id\": \"en-US\",\n   \"market_country_code\": \"RU\",\n   \"additional_data\": [\n       {\n           \"code\": \"PR\",\n           \"airline\": \"Philippine Airlines\"\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n   \"status\": 200,\n   \"message\": \"OK\",\n   \"data\": {\n       \"result\": \"Sms sending. Mail ID(427561)\"\n   }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 404 Not Found\n{\n   \"status\": 422,\n   \"message\": \"Validation error\",\n   \"errors\": {\n       \"quote_uid\": [\n           \"Quote not found by Uid(60910028642b1)\"\n       ]\n   },\n   \"code\": 0\n}",
          "type": "json"
        },
        {
          "title": "Error-Response (400):",
          "content": "HTTP/1.1 400 Bad Request\n{\n   \"name\": \"Bad Request\",\n   \"message\": \"POST data request is empty\",\n   \"code\": 2,\n   \"status\": 400,\n   \"type\": \"yii\\\\web\\\\BadRequestHttpException\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v1/controllers/OfferSmsController.php",
    "groupTitle": "Quotes"
  },
  {
    "type": "post",
    "url": "/v2/quote/get-info",
    "title": "Get Quote",
    "version": "0.2.0",
    "name": "GetQuote",
    "group": "Quotes_v2",
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
            "type": "integer",
            "optional": false,
            "field": "lead_id",
            "description": "<p>Lead ID</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "lead_uid",
            "description": "<p>Lead UID</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "lead_type",
            "description": "<p><code>TYPE_ALTERNATIVE = 2, TYPE_FAILED_BOOK = 3</code></p>"
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
          "content": "HTTP/1.1 200 OK\n{\n  \"status\": \"Success\",\n  \"result\": {\n      \"prices\": {\n          \"totalPrice\": 2056.98,\n          \"totalTax\": 1058.98,\n          \"isCk\": true\n      },\n      \"passengers\": {\n          \"ADT\": {\n              \"cnt\": 2,\n              \"price\": 1028.49,\n              \"tax\": 529.49,\n              \"baseFare\": 499,\n              \"mark_up\": 20,\n              \"extra_mark_up\": 10\n          },\n          \"INF\": {\n              \"cnt\": 1,\n              \"price\": 0,\n              \"tax\": 0,\n              \"baseFare\": 0,\n              \"mark_up\": 0,\n              \"extra_mark_up\": 0\n          }\n      },\n      \"trips\": [\n          {\n              \"tripId\": 1,\n              \"segments\": [\n                  {\n                      \"segmentId\": 1,\n                      \"departureTime\": \"2019-12-06 16:20\",\n                      \"arrivalTime\": \"2019-12-06 17:57\",\n                      \"stop\": 0,\n                      \"stops\": null,\n                      \"flightNumber\": \"7312\",\n                      \"bookingClass\": \"T\",\n                      \"duration\": 97,\n                      \"departureAirportCode\": \"IND\",\n                      \"departureAirportTerminal\": \"\",\n                      \"arrivalAirportCode\": \"YYZ\",\n                      \"arrivalAirportTerminal\": \"\",\n                      \"operatingAirline\": \"AC\",\n                      \"airEquipType\": null,\n                      \"marketingAirline\": \"AC\",\n                      \"cabin\": \"Y\",\n                      \"ticket_id\": 1,\n                      \"baggage\": {\n                          \"\": {\n                              \"allowPieces\": 2,\n                              \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                              \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\",\n                              \"charge\": {\n                                  \"price\": 100,\n                                  \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                  \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                  \"firstPiece\": 1,\n                                  \"lastPiece\": 1\n                              }\n                          }\n                      }\n                  },\n                  {\n                      \"segmentId\": 2,\n                      \"departureTime\": \"2019-12-06 20:45\",\n                      \"arrivalTime\": \"2019-12-07 09:55\",\n                      \"stop\": 0,\n                      \"stops\": null,\n                      \"flightNumber\": \"880\",\n                      \"bookingClass\": \"T\",\n                      \"duration\": 430,\n                      \"departureAirportCode\": \"YYZ\",\n                      \"departureAirportTerminal\": \"\",\n                      \"arrivalAirportCode\": \"CDG\",\n                      \"arrivalAirportTerminal\": \"\",\n                      \"operatingAirline\": \"AC\",\n                      \"airEquipType\": null,\n                      \"marketingAirline\": \"AC\",\n                      \"cabin\": \"Y\",\n                      \"ticket_id\": 2,\n                      \"baggage\": {\n                          \"\": {\n                              \"allowPieces\": 2,\n                              \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                              \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\",\n                              \"charge\": {\n                                  \"price\": 100,\n                                  \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                  \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                  \"firstPiece\": 1,\n                                  \"lastPiece\": 1\n                              }\n                          }\n                      }\n                  },\n                  {\n                      \"segmentId\": 3,\n                      \"departureTime\": \"2019-12-07 13:40\",\n                      \"arrivalTime\": \"2019-12-07 19:05\",\n                      \"stop\": 0,\n                      \"stops\": null,\n                      \"flightNumber\": \"6692\",\n                      \"bookingClass\": \"T\",\n                      \"duration\": 265,\n                      \"departureAirportCode\": \"CDG\",\n                      \"departureAirportTerminal\": \"\",\n                      \"arrivalAirportCode\": \"IST\",\n                      \"arrivalAirportTerminal\": \"\",\n                      \"operatingAirline\": \"AC\",\n                      \"airEquipType\": null,\n                      \"marketingAirline\": \"AC\",\n                      \"cabin\": \"Y\",\n                      \"ticket_id\": 2,\n                      \"baggage\": {\n                          \"\": {\n                              \"allowPieces\": 2,\n                              \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                              \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\",\n                              \"charge\": {\n                                  \"price\": 100,\n                                  \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                  \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                  \"firstPiece\": 1,\n                                  \"lastPiece\": 1\n                              }\n                          }\n                      }\n                  }\n              ],\n              \"duration\": 1185\n          },\n          {\n              \"tripId\": 2,\n              \"segments\": [\n                  {\n                      \"segmentId\": 1,\n                      \"departureTime\": \"2019-12-25 09:15\",\n                      \"arrivalTime\": \"2019-12-25 10:35\",\n                      \"stop\": 0,\n                      \"stops\": null,\n                      \"flightNumber\": \"6681\",\n                      \"bookingClass\": \"T\",\n                      \"duration\": 140,\n                      \"departureAirportCode\": \"IST\",\n                      \"departureAirportTerminal\": \"\",\n                      \"arrivalAirportCode\": \"GVA\",\n                      \"arrivalAirportTerminal\": \"\",\n                      \"operatingAirline\": \"AC\",\n                      \"airEquipType\": null,\n                      \"marketingAirline\": \"AC\",\n                      \"cabin\": \"Y\",\n                      \"ticket_id\": 1,\n                      \"baggage\": {\n                          \"\": {\n                              \"allowPieces\": 1,\n                              \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡\",\n                              \"allowMaxWeight\": \"UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS\",\n                              \"charge\": {\n                                  \"price\": 100,\n                                  \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                  \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND\",\n                                  \"firstPiece\": 1,\n                                  \"lastPiece\": 1\n                              }\n                          }\n                      }\n                  },\n                  {\n                      \"segmentId\": 2,\n                      \"departureTime\": \"2019-12-25 12:00\",\n                      \"arrivalTime\": \"2019-12-25 17:34\",\n                      \"stop\": 0,\n                      \"stops\": null,\n                      \"flightNumber\": \"835\",\n                      \"bookingClass\": \"T\",\n                      \"duration\": 694,\n                      \"departureAirportCode\": \"GVA\",\n                      \"departureAirportTerminal\": \"\",\n                      \"arrivalAirportCode\": \"YYZ\",\n                      \"arrivalAirportTerminal\": \"\",\n                      \"operatingAirline\": \"AC\",\n                      \"airEquipType\": null,\n                      \"marketingAirline\": \"AC\",\n                      \"cabin\": \"Y\",\n                      \"ticket_id\": 2,\n                      \"baggage\": {\n                          \"\": {\n                              \"allowPieces\": 1,\n                              \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡\",\n                              \"allowMaxWeight\": \"UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS\",\n                              \"charge\": {\n                                  \"price\": 100,\n                                  \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                  \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND\",\n                                  \"firstPiece\": 1,\n                                  \"lastPiece\": 1\n                              }\n                          }\n                      }\n                  },\n                  {\n                      \"segmentId\": 3,\n                      \"departureTime\": \"2019-12-25 20:55\",\n                      \"arrivalTime\": \"2019-12-25 22:37\",\n                      \"stop\": 0,\n                      \"stops\": null,\n                      \"flightNumber\": \"7313\",\n                      \"bookingClass\": \"T\",\n                      \"duration\": 102,\n                      \"departureAirportCode\": \"YYZ\",\n                      \"departureAirportTerminal\": \"\",\n                      \"arrivalAirportCode\": \"IND\",\n                      \"arrivalAirportTerminal\": \"\",\n                      \"operatingAirline\": \"AC\",\n                      \"airEquipType\": null,\n                      \"marketingAirline\": \"AC\",\n                      \"cabin\": \"Y\",\n                      \"ticket_id\": 2,\n                      \"baggage\": {\n                          \"\": {\n                              \"allowPieces\": 1,\n                              \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡\",\n                              \"allowMaxWeight\": \"UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS\",\n                              \"charge\": {\n                                  \"price\": 100,\n                                  \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                  \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND\",\n                                  \"firstPiece\": 1,\n                                  \"lastPiece\": 1\n                              }\n                          }\n                      }\n                  }\n              ],\n              \"duration\": 1222\n          }\n      ],\n      \"validatingCarrier\": \"AC\",\n      \"fareType\": \"PUB\",\n      \"tripType\": \"RT\",\n      \"currency\": \"USD\",\n      \"currencyRate\": 1\n  },\n  \"errors\": [],\n  \"uid\": \"5cb97d1c78486\",\n  \"lead_id\": 92322,\n  \"lead_uid\": \"5cb8735a502f5\",\n  \"lead_expiration_dt\": \"2021-02-23 20:12:12\",\n  \"lead_delayed_charge\": 0,\n  \"lead_status\": null,\n  \"lead_type\": 2,\n  \"booked_quote_uid\": null,\n  \"source_code\": \"38T556\",\n  \"agentName\": \"admin\",\n  \"agentEmail\": \"admin@wowfare.com\",\n  \"agentDirectLine\": \"\",\n  \"generalEmail\": \"info@wowfare.com\",\n  \"generalDirectLine\": \"+37379731662\",\n  \"typeId\": 2,\n  \"typeName\": \"Alternative\",\n  \"client\": {\n      \"uuid\": \"35009a79-1a05-49d7-b876-2b884d0f825b\"\n      \"client_id\": 331968,\n      \"first_name\": \"Johann\",\n      \"middle_name\": \"Sebastian\",\n      \"last_name\": \"Bach\",\n      \"phones\": [\n          \"+13152572166\"\n      ],\n      \"emails\": [\n          \"example@test.com\",\n          \"bah@gmail.com\"\n      ]\n  },\n  \"quote\": {\n      \"id\": 382366,\n      \"uid\": \"5d43e1ec36372\",\n      \"lead_id\": 178363,\n      \"employee_id\": 167,\n      \"record_locator\": \"\",\n      \"pcc\": \"DFWG32100\",\n      \"cabin\": \"E\",\n      \"gds\": \"A\",\n      \"trip_type\": \"OW\",\n      \"main_airline_code\": \"SU\",\n      \"reservation_dump\": \"1  SU1845T  22AUG  KIVSVO    255A    555A  TH\",\n      \"status\": 5,\n      \"check_payment\": 1,\n      \"fare_type\": \"PUB\",\n      \"created\": \"2019-08-02 07:10:36\",\n      \"updated\": \"2019-08-05 08:58:18\",\n      \"created_by_seller\": 1,\n      \"employee_name\": \"alex.connor2\",\n      \"last_ticket_date\": \"2019-08-09 00:00:00\",\n      \"service_fee_percent\": null,\n      \"pricing_info\": null,\n      \"alternative\": 1,\n      \"tickets\": \"[{\\\"key\\\":\\\"02_QVdBUlFBKlkxMDAwL05ZQ01BRDIwMTktMDgtMjYvTUFETllDMjAxOS0wOS0wNipVQX4jVUE1MSNVQTUw\\\",\\\"routingId\\\":0,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-08-11\\\",\\\"totalPrice\\\":392.73,\\\"totalTax\\\":272.73,\\\"markup\\\":50,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"JWZ\\\",\\\"cnt\\\":1,\\\"price\\\":392.73,\\\"tax\\\":272.73,\\\"baseFare\\\":120,\\\"pubBaseFare\\\":120,\\\"baseTax\\\":222.73,\\\"markup\\\":50,\\\"refundPenalty\\\":\\\"\\\",\\\"changePenalty\\\":\\\"Percentage: 100.00%\\\",\\\"endorsementPenalty\\\":\\\"\\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":120,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":222.73,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}}},\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"UA\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"fareType\\\":\\\"SR\\\",\\\"tripType\\\":\\\"RT\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segmentIds\\\":[1]},{\\\"tripId\\\":2,\\\"segmentIds\\\":[3]}]},{\\\"key\\\":\\\"02_QVdBUlFBKlkxMDAwL01BRFZJRTIwMTktMDgtMjcvVklFTUFEMjAxOS0wOS0wNSpMWH4jTFgyMDI3I0xYMzU2OCNMWDM1NjMjTFgyMDQ4\\\",\\\"routingId\\\":0,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-08-09\\\",\\\"totalPrice\\\":305.3,\\\"totalTax\\\":184.3,\\\"markup\\\":50,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"ADT\\\",\\\"cnt\\\":1,\\\"price\\\":305.3,\\\"tax\\\":184.3,\\\"baseFare\\\":121,\\\"pubBaseFare\\\":121,\\\"baseTax\\\":134.3,\\\"markup\\\":50,\\\"refundPenalty\\\":\\\"Percentage: 100.00%\\\",\\\"changePenalty\\\":\\\"Percentage: 100.00%\\\",\\\"endorsementPenalty\\\":\\\"\\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":121,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":134.3,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}}},\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"LX\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"fareType\\\":\\\"PUB\\\",\\\"tripType\\\":\\\"RT\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segmentIds\\\":[2,3]},{\\\"tripId\\\":2,\\\"segmentIds\\\":[1,2]}]}]\",\n      \"origin_search_data\": \"{\\\"key\\\":\\\"01_U0FMMTAxKlkxMDAwL0pGS0ZSQTIwMTktMTEtMjEqUk9+I1FSNzA0I1FSMjI3I1JPMjk4I1JPMzAxOjNkMjBiYzI5LWIzMmItNGJhOC05OTljLTQ4ZTFlYWI1NGU1Ng==\\\",\\\"routingId\\\":306,\\\"gdsOfferId\\\":\\\"3d20bc29-b32b-4ba8-999c-48e1eab54e56\\\",\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-11-23\\\",\\\"totalPrice\\\":670.35,\\\"totalTax\\\":367.35,\\\"markup\\\":100,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":100,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"JWZ\\\",\\\"cnt\\\":1,\\\"price\\\":670.35,\\\"tax\\\":367.35,\\\"baseFare\\\":303,\\\"pubBaseFare\\\":303,\\\"baseTax\\\":267.35,\\\"markup\\\":100,\\\"refundPenalty\\\":\\\"Amount: USD375.00 Percentage: 100.00%\\\",\\\"changePenalty\\\":\\\"Amount: USD260.00 Percentage: 100.00%\\\",\\\"endorsementPenalty\\\":\\\" \\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":303,\\\"currency\\\":\\\"USD\\\"},\\\"oPubBaseFare\\\":{\\\"amount\\\":303,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":267.35,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":100,\\\"currency\\\":\\\"USD\\\"}}},\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segments\\\":[{\\\"segmentId\\\":1,\\\"departureTime\\\":\\\"2019-11-21 09:45\\\",\\\"arrivalTime\\\":\\\"2019-11-22 06:00\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"704\\\",\\\"bookingClass\\\":\\\"N\\\",\\\"duration\\\":735,\\\"departureAirportCode\\\":\\\"JFK\\\",\\\"departureAirportTerminal\\\":\\\"7\\\",\\\"arrivalAirportCode\\\":\\\"DOH\\\",\\\"arrivalAirportTerminal\\\":\\\"\\\",\\\"operatingAirline\\\":\\\"QR\\\",\\\"airEquipType\\\":\\\"351\\\",\\\"marketingAirline\\\":\\\"QR\\\",\\\"marriageGroup\\\":\\\"I\\\",\\\"mileage\\\":6689,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"NLUSN1RO\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"allowPieces\\\":2}},\\\"recheckBaggage\\\":false},{\\\"segmentId\\\":2,\\\"departureTime\\\":\\\"2019-11-22 07:10\\\",\\\"arrivalTime\\\":\\\"2019-11-22 11:25\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"227\\\",\\\"bookingClass\\\":\\\"N\\\",\\\"duration\\\":315,\\\"departureAirportCode\\\":\\\"DOH\\\",\\\"departureAirportTerminal\\\":\\\"\\\",\\\"arrivalAirportCode\\\":\\\"SOF\\\",\\\"arrivalAirportTerminal\\\":\\\"2\\\",\\\"operatingAirline\\\":\\\"QR\\\",\\\"airEquipType\\\":\\\"320\\\",\\\"marketingAirline\\\":\\\"QR\\\",\\\"marriageGroup\\\":\\\"O\\\",\\\"mileage\\\":1999,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"NLUSN1RO\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"allowPieces\\\":2}},\\\"recheckBaggage\\\":false},{\\\"segmentId\\\":3,\\\"departureTime\\\":\\\"2019-11-22 19:45\\\",\\\"arrivalTime\\\":\\\"2019-11-22 20:50\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"298\\\",\\\"bookingClass\\\":\\\"T\\\",\\\"duration\\\":65,\\\"departureAirportCode\\\":\\\"SOF\\\",\\\"departureAirportTerminal\\\":\\\"2\\\",\\\"arrivalAirportCode\\\":\\\"OTP\\\",\\\"arrivalAirportTerminal\\\":\\\"\\\",\\\"operatingAirline\\\":\\\"RO\\\",\\\"airEquipType\\\":\\\"AT7\\\",\\\"marketingAirline\\\":\\\"RO\\\",\\\"marriageGroup\\\":\\\"I\\\",\\\"mileage\\\":185,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"TOWSVR\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"allowPieces\\\":1}},\\\"recheckBaggage\\\":true},{\\\"segmentId\\\":4,\\\"departureTime\\\":\\\"2019-11-23 08:35\\\",\\\"arrivalTime\\\":\\\"2019-11-23 10:15\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"301\\\",\\\"bookingClass\\\":\\\"T\\\",\\\"duration\\\":160,\\\"departureAirportCode\\\":\\\"OTP\\\",\\\"departureAirportTerminal\\\":\\\"\\\",\\\"arrivalAirportCode\\\":\\\"FRA\\\",\\\"arrivalAirportTerminal\\\":\\\"2\\\",\\\"operatingAirline\\\":\\\"RO\\\",\\\"airEquipType\\\":\\\"73W\\\",\\\"marketingAirline\\\":\\\"RO\\\",\\\"marriageGroup\\\":\\\"O\\\",\\\"mileage\\\":903,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"TOWSVR\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"allowPieces\\\":1}},\\\"recheckBaggage\\\":false}],\\\"duration\\\":2550}],\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"RO\\\",\\\"gds\\\":\\\"G\\\",\\\"pcc\\\":\\\"NA\\\",\\\"cons\\\":\\\"GIS\\\",\\\"fareType\\\":\\\"NA\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"currencies\\\":[\\\"USD\\\"],\\\"currencyRates\\\":{\\\"USDUSD\\\":{\\\"from\\\":\\\"USD\\\",\\\"to\\\":\\\"USD\\\",\\\"rate\\\":1}},\\\"tickets\\\":[{\\\"key\\\":\\\"02_QVdBUlFBKlkxMDAwL0pGS1NPRjIwMTktMTEtMjEqUVJ+I1FSNzA0I1FSMjI3\\\",\\\"routingId\\\":0,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-11-21\\\",\\\"totalPrice\\\":388.8,\\\"totalTax\\\":267.8,\\\"markup\\\":50,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"JWZ\\\",\\\"cnt\\\":1,\\\"price\\\":388.8,\\\"tax\\\":267.8,\\\"baseFare\\\":121,\\\"pubBaseFare\\\":121,\\\"baseTax\\\":217.8,\\\"markup\\\":50,\\\"refundPenalty\\\":\\\"Amount: USD375.00 \\\",\\\"changePenalty\\\":\\\"Amount: USD260.00\\\",\\\"endorsementPenalty\\\":\\\"\\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":121,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":217.8,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}}},\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"QR\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"fareType\\\":\\\"SR\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segmentIds\\\":[1,2]}]},{\\\"key\\\":\\\"01_QVdBUlFBKlkxMDAwL1NPRkZSQTIwMTktMTEtMjIqUk9+I1JPMjk4I1JPMzAx\\\",\\\"routingId\\\":0,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-10-19\\\",\\\"totalPrice\\\":265.6,\\\"totalTax\\\":83.6,\\\"markup\\\":50,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"ADT\\\",\\\"cnt\\\":1,\\\"price\\\":265.6,\\\"tax\\\":83.6,\\\"baseFare\\\":182,\\\"pubBaseFare\\\":182,\\\"baseTax\\\":33.6,\\\"markup\\\":50,\\\"refundPenalty\\\":\\\"Percentage: 100.00%\\\",\\\"changePenalty\\\":\\\"Percentage: 100.00%\\\",\\\"endorsementPenalty\\\":\\\"\\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":182,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":33.6,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}}},\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"RO\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"fareType\\\":\\\"PUB\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segmentIds\\\":[3,4]}]}]}\",\n      \"typeId\": 2,\n      \"typeName\": \"Alternative\"\n  },\n  \"itineraryOrigin\": {\n     \"uid\": \"5f207ec202212\",\n     \"typeId\": 1,\n     \"typeName\": \"Original\"\n  },\n  \"visitor_log\": {\n      \"vl_source_cid\": \"string_abc\",\n      \"vl_ga_client_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n      \"vl_ga_user_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n      \"vl_customer_id\": \"3\",\n      \"vl_gclid\": \"gclid=TeSter-123#bookmark\",\n      \"vl_dclid\": \"CJKu8LrQxd4CFQ1qwQodmJIElw\",\n      \"vl_utm_source\": \"newsletter4\",\n      \"vl_utm_medium\": \"string_abc\",\n      \"vl_utm_campaign\": \"string_abc\",\n      \"vl_utm_term\": \"string_abc\",\n      \"vl_utm_content\": \"string_abc\",\n      \"vl_referral_url\": \"string_abc\",\n      \"vl_location_url\": \"string_abc\",\n      \"vl_user_agent\": \"string_abc\",\n      \"vl_ip_address\": \"127.0.0.1\",\n      \"vl_visit_dt\": \"2020-02-14 12:00:00\",\n      \"vl_created_dt\": \"2020-02-28 17:17:33\"\n  },\n  \"lead\": {\n      \"additionalInformation\": [\n          {\n             \"pnr\": \"example_pnr\",\n              \"bo_sale_id\": \"example_sale_id\",\n             \"vtf_processed\": null,\n             \"tkt_processed\": null,\n             \"exp_processed\": null,\n             \"passengers\": [],\n             \"paxInfo\": []\n         }\n     ],\n     \"lead_data\": [\n         {\n             \"ld_field_key\": \"kayakclickid\",\n             \"ld_field_value\": \"example_value132\"\n         }\n     ]\n  },\n  \"action\": \"v2/quote/get-info\",\n  \"response_id\": 298939,\n  \"request_dt\": \"2019-04-25 13:12:44\",\n  \"response_dt\": \"2019-04-25 13:12:44\"\n}",
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
    "groupTitle": "Quotes_v2"
  },
  {
    "type": "get",
    "url": "/v2/user-group/list",
    "title": "Get User Groups",
    "version": "0.2.0",
    "name": "UserGroupList",
    "group": "UserGroup",
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
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Accept-Encoding",
            "description": ""
          },
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "If-Modified-Since",
            "description": "<p>Format <code> day-name, day month year hour:minute:second GMT</code></p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\"\n}",
          "type": "json"
        },
        {
          "title": "Header-Example (If-Modified-Since):",
          "content": "{\n    \"Authorization\": \"Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==\",\n    \"Accept-Encoding\": \"Accept-Encoding: gzip, deflate\",\n    \"If-Modified-Since\": \"Mon, 23 Dec 2019 08:17:54 GMT\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n  {\n      \"status\": 200,\n      \"message\": \"OK\",\n      \"data\": {\n          \"user-group\": [\n              {\n                  \"ug_id\": 1,\n                  \"ug_key\": \"ug1\",\n                  \"ug_name\": \"Bucuresti Team\",\n                 \"ug_disable\": 0,\n                  \"ug_updated_dt\": \"2018-12-18 09:17:45\"\n              },\n              {\n                  \"ug_id\": 2,\n                  \"ug_key\": \"ug2\",\n                  \"ug_name\": \"100J Team\",\n                 \"ug_disable\": 0,\n                  \"ug_updated_dt\": \"2018-12-18 09:17:59\"\n              },\n              {\n                  \"ug_id\": 3,\n                  \"ug_key\": \"ug3\",\n                  \"ug_name\": \"Pro Team\",\n                  \"ug_disable\": 1,\n                  \"ug_updated_dt\": \"2018-12-18 09:18:10\"\n              },\n          ]\n      },\n      \"technical\": {\n          \"action\": \"v2/user-group/list\",\n          \"response_id\": 8080269,\n          \"request_dt\": \"2020-02-27 15:00:43\",\n          \"response_dt\": \"2020-02-27 15:00:43\",\n          \"execution_time\": 0.006,\n          \"memory_usage\": 189944\n      },\n      \"request\": []\n  }",
          "type": "json"
        },
        {
          "title": "Not Modified-Response (304):",
          "content": "\nHTTP/1.1 304 Not Modified\nCache-Control: public, max-age=3600\nLast-Modified: Mon, 23 Dec 2019 08:17:53 GMT",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (405):",
          "content": "\nHTTP/1.1 405 Method Not Allowed\n  {\n      \"name\": \"Method Not Allowed\",\n      \"message\": \"Method Not Allowed. This URL can only handle the following request methods: GET.\",\n      \"code\": 0,\n      \"status\": 405,\n      \"type\": \"yii\\\\web\\\\MethodNotAllowedHttpException\"\n  }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/UserGroupController.php",
    "groupTitle": "UserGroup"
  }
] });
