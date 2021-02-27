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
    "type": "get",
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
    "type": "get",
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
          "content": "{\n   \"apiKey\": \"d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd\",\n   \"lead\": {\n       \"flights\": [\n           {\n               \"origin\": \"KIV\",\n               \"destination\": \"DME\",\n               \"departure\": \"2018-10-13 13:50:00\",\n           },\n           {\n               \"origin\": \"DME\",\n               \"destination\": \"KIV\",\n               \"departure\": \"2018-10-18 10:54:00\",\n           }\n       ],\n       \"emails\": [\n         \"email1@gmail.com\",\n         \"email2@gmail.com\",\n       ],\n       \"phones\": [\n         \"+373-69-487523\",\n         \"022-45-7895-89\",\n       ],\n       \"source_id\": 38,\n       \"sub_sources_code\": \"BBM101\",\n       \"adults\": 1,\n       \"client_first_name\": \"Alexandr\",\n       \"client_last_name\": \"Freeman\",\n       \"user_language\": \"en-GB\",\n       \"expire_at\": \"2020-01-20 12:12:12\",\n       \"visitor_log\": [\n              {\n                  \"vl_source_cid\": \"string_abc\",\n                  \"vl_ga_client_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n                  \"vl_ga_user_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n                  \"vl_customer_id\": \"3\",\n                  \"vl_gclid\": \"gclid=TeSter-123#bookmark\",\n                  \"vl_dclid\": \"CJKu8LrQxd4CFQ1qwQodmJIElw\",\n                  \"vl_utm_source\": \"newsletter4\",\n                  \"vl_utm_medium\": \"string_abc\",\n                  \"vl_utm_campaign\": \"string_abc\",\n                  \"vl_utm_term\": \"string_abc\",\n                  \"vl_utm_content\": \"string_abc\",\n                  \"vl_referral_url\": \"string_abc\",\n                  \"vl_location_url\": \"string_abc\",\n                  \"vl_user_agent\": \"string_abc\",\n                  \"vl_ip_address\": \"127.0.0.1\",\n                  \"vl_visit_dt\": \"2020-02-14 12:00:00\"\n              },\n              {\n                  \"vl_source_cid\": \"string_abc\",\n                  \"vl_ga_client_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n                  \"vl_ga_user_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n                  \"vl_customer_id\": \"3\",\n                  \"vl_gclid\": \"gclid=TeSter-123#bookmark\",\n                  \"vl_dclid\": \"CJKu8LrQxd4CFQ1qwQodmJIElw\",\n                  \"vl_utm_source\": \"newsletter4\",\n                  \"vl_utm_medium\": \"string_abc\",\n                  \"vl_utm_campaign\": \"string_abc\",\n                  \"vl_utm_term\": \"string_abc\",\n                  \"vl_utm_content\": \"string_abc\",\n                  \"vl_referral_url\": \"string_abc\",\n                  \"vl_location_url\": \"string_abc\",\n                  \"vl_user_agent\": \"string_abc\",\n                  \"vl_ip_address\": \"127.0.0.1\",\n                  \"vl_visit_dt\": \"2020-02-14 12:00:00\"\n              }\n       ]\n   },\n   \"Client\": {\n       \"name\": \"Alexandr\",\n       \"phone\": \"+373-69-487523\",\n       \"email\": \"email1@gmail.com\",\n       \"client_ip\": \"127.0.0.1\",\n       \"uuid\": \"35009a79-1a05-49d7-b876-2b884d0f825b\"\n   }\n}",
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
          "content": "    HTTP/1.1 200 OK\n{\n  \"status\": 200,\n  \"name\": \"Success\",\n  \"code\": 0,\n  \"message\": \"\",\n  \"data\": {\n      \"response\": {\n          \"lead\": {\n              \"client_id\": 11,\n              \"employee_id\": null,\n              \"status\": 1,\n              \"uid\": \"5b73b80eaf69b\",\n              \"gid\": \"65df1546edccce15518e929e5af1a4\",\n              \"project_id\": 6,\n              \"source_id\": \"38\",\n              \"trip_type\": \"RT\",\n              \"cabin\": \"E\",\n              \"adults\": \"1\",\n              \"children\": 0,\n              \"infants\": 0,\n              \"notes_for_experts\": null,\n              \"created\": \"2018-08-15 05:20:14\",\n              \"updated\": \"2018-08-15 05:20:14\",\n              \"request_ip\": \"127.0.0.1\",\n              \"request_ip_detail\": \"{\\\"ip\\\":\\\"127.0.0.1\\\",\\\"city\\\":\\\"North Pole\\\",\\\"postal\\\":\\\"99705\\\",\\\"state\\\":\\\"Alaska\\\",\\\"state_code\\\":\\\"AK\\\",\\\"country\\\":\\\"United States\\\",\\\"country_code\\\":\\\"US\\\",\\\"location\\\":\\\"64.7548317,-147.3431046\\\",\\\"timezone\\\":{\\\"id\\\":\\\"America\\\\/Anchorage\\\",\\\"location\\\":\\\"61.21805,-149.90028\\\",\\\"country_code\\\":\\\"US\\\",\\\"country_name\\\":\\\"United States of America\\\",\\\"iso3166_1_alpha_2\\\":\\\"US\\\",\\\"iso3166_1_alpha_3\\\":\\\"USA\\\",\\\"un_m49_code\\\":\\\"840\\\",\\\"itu\\\":\\\"USA\\\",\\\"marc\\\":\\\"xxu\\\",\\\"wmo\\\":\\\"US\\\",\\\"ds\\\":\\\"USA\\\",\\\"phone_prefix\\\":\\\"1\\\",\\\"fifa\\\":\\\"USA\\\",\\\"fips\\\":\\\"US\\\",\\\"gual\\\":\\\"259\\\",\\\"ioc\\\":\\\"USA\\\",\\\"currency_alpha_code\\\":\\\"USD\\\",\\\"currency_country_name\\\":\\\"UNITED STATES\\\",\\\"currency_minor_unit\\\":\\\"2\\\",\\\"currency_name\\\":\\\"US Dollar\\\",\\\"currency_code\\\":\\\"840\\\",\\\"independent\\\":\\\"Yes\\\",\\\"capital\\\":\\\"Washington\\\",\\\"continent\\\":\\\"NA\\\",\\\"tld\\\":\\\".us\\\",\\\"languages\\\":\\\"en-US,es-US,haw,fr\\\",\\\"geoname_id\\\":\\\"6252001\\\",\\\"edgar\\\":\\\"\\\"},\\\"datetime\\\":{\\\"date\\\":\\\"08\\\\/14\\\\/2018\\\",\\\"date_time\\\":\\\"08\\\\/14\\\\/2018 21:20:15\\\",\\\"date_time_txt\\\":\\\"Tuesday, August 14, 2018 21:20:15\\\",\\\"date_time_wti\\\":\\\"Tue, 14 Aug 2018 21:20:15 -0800\\\",\\\"date_time_ymd\\\":\\\"2018-08-14T21:20:15-08:00\\\",\\\"time\\\":\\\"21:20:15\\\",\\\"month\\\":\\\"8\\\",\\\"month_wilz\\\":\\\"08\\\",\\\"month_abbr\\\":\\\"Aug\\\",\\\"month_full\\\":\\\"August\\\",\\\"month_days\\\":\\\"31\\\",\\\"day\\\":\\\"14\\\",\\\"day_wilz\\\":\\\"14\\\",\\\"day_abbr\\\":\\\"Tue\\\",\\\"day_full\\\":\\\"Tuesday\\\",\\\"year\\\":\\\"2018\\\",\\\"year_abbr\\\":\\\"18\\\",\\\"hour_12_wolz\\\":\\\"9\\\",\\\"hour_12_wilz\\\":\\\"09\\\",\\\"hour_24_wolz\\\":\\\"21\\\",\\\"hour_24_wilz\\\":\\\"21\\\",\\\"hour_am_pm\\\":\\\"pm\\\",\\\"minutes\\\":\\\"20\\\",\\\"seconds\\\":\\\"15\\\",\\\"week\\\":\\\"33\\\",\\\"offset_seconds\\\":\\\"-28800\\\",\\\"offset_minutes\\\":\\\"-480\\\",\\\"offset_hours\\\":\\\"-8\\\",\\\"offset_gmt\\\":\\\"-08:00\\\",\\\"offset_tzid\\\":\\\"America\\\\/Anchorage\\\",\\\"offset_tzab\\\":\\\"AKDT\\\",\\\"offset_tzfull\\\":\\\"Alaska Daylight Time\\\",\\\"tz_string\\\":\\\"AKST+9AKDT,M3.2.0\\\\/2,M11.1.0\\\\/2\\\",\\\"dst\\\":\\\"true\\\",\\\"dst_observes\\\":\\\"true\\\",\\\"timeday_spe\\\":\\\"evening\\\",\\\"timeday_gen\\\":\\\"evening\\\"}}\",\n              \"offset_gmt\": \"-08.00\",\n              \"snooze_for\": null,\n              \"rating\": null,\n              \"id\": 7\n          },\n          \"flights\": [\n              {\n                  \"origin\": \"BOS\",\n                  \"destination\": \"LGW\",\n                  \"departure\": \"2018-09-19\"\n              },\n              {\n                  \"origin\": \"LGW\",\n                  \"destination\": \"BOS\",\n                  \"departure\": \"2018-09-22\"\n              }\n          ],\n          \"emails\": [\n              \"chalpet@gmail.com\",\n              \"chalpet2@gmail.com\"\n          ],\n          \"phones\": [\n              \"+373-69-98-698\",\n              \"+373-69-98-698\"\n          ],\n         \"client\": {\n             \"uuid\": \"35009a79-1a05-49d7-b876-2b884d0f825b\"\n             \"client_id\": 331968,\n             \"first_name\": \"Johann\",\n             \"middle_name\": \"Sebastian\",\n             \"last_name\": \"Bach\",\n             \"phones\": [\n                \"+13152572166\"\n             ],\n             \"emails\": [\n                \"example@test.com\",\n                \"bah@gmail.com\"\n             ]\n          }\n      },\n      \"request\": {\n          \"client_id\": null,\n          \"employee_id\": null,\n          \"status\": null,\n          \"uid\": null,\n          \"project_id\": 6,\n          \"source_id\": \"38\",\n          \"trip_type\": null,\n          \"cabin\": null,\n          \"adults\": \"1\",\n          \"children\": null,\n          \"infants\": null,\n          \"notes_for_experts\": null,\n          \"created\": null,\n          \"updated\": null,\n          \"request_ip\": null,\n          \"request_ip_detail\": null,\n          \"offset_gmt\": null,\n          \"snooze_for\": null,\n          \"rating\": null,\n          \"flights\": [\n              {\n                  \"origin\": \"BOS\",\n                  \"destination\": \"LGW\",\n                  \"departure\": \"2018-09-19\"\n              },\n              {\n                  \"origin\": \"LGW\",\n                  \"destination\": \"BOS\",\n                  \"departure\": \"2018-09-22\"\n              }\n          ],\n          \"emails\": [\n              \"chalpet@gmail.com\",\n              \"chalpet2@gmail.com\"\n          ],\n          \"phones\": [\n              \"+373-69-98-698\",\n              \"+373-69-98-698\"\n          ],\n          \"client_first_name\": \"Alexandr\",\n          \"client_last_name\": \"Freeman\"\n      }\n  },\n  \"action\": \"v1/lead/create\",\n  \"response_id\": 42,\n  \"request_dt\": \"2018-08-15 05:20:14\",\n  \"response_dt\": \"2018-08-15 05:20:15\"\n}",
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
            "size": "15",
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
            "description": "<p>Client phone or Client email is required</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "size": "160",
            "optional": false,
            "field": "lead.client.email",
            "description": "<p>Client email or Client phone is required</p>"
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
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "\n{\n     \"lead\": {\n          \"client\": {\n              \"phone\": \"+37369333333\",\n              \"email\": \"email@email.com\",\n              \"uuid\" : \"af5246f1-094f-4fde-ada3-bd7298621613\"\n          },\n          \"uid\": \"WD6q53PO3b\",\n          \"status\": 14,\n          \"source_code\": \"JIVOCH\",\n          \"cabin\": \"E\",\n          \"adults\": 2,\n          \"children\": 2,\n          \"infants\": 2,\n          \"request_ip\": \"12.12.12.12\",\n          \"discount_id\": \"123123\",\n          \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36\",\n          \"flight_id\": 12457,\n          \"user_language\": \"en-GB\",\n          \"expire_at\": \"2020-01-20 12:12:12\",\n          \"flights\": [\n              {\n                  \"origin\": \"NYC\",\n                  \"destination\": \"LON\",\n                  \"departure\": \"2019-12-16\"\n              },\n              {\n                  \"origin\": \"LON\",\n                  \"destination\": \"NYC\",\n                  \"departure\": \"2019-12-17\"\n              },\n              {\n                  \"origin\": \"LON\",\n                  \"destination\": \"NYC\",\n                  \"departure\": \"2019-12-18\"\n              }\n          ]\n      }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n{\n      \"status\": 200,\n      \"message\": \"OK\",\n      \"data\": {\n         \"lead\": {\n              \"id\": 370949,\n              \"uid\": \"WD6q53PO3b\",\n              \"gid\": \"63e1505f4a8a87e6651048e3e3eae4e1\",\n              \"client_id\": 1034,\n              \"client\": {\n                 \"uuid\": \"35009a79-1a05-49d7-b876-2b884d0f825b\"\n                 \"client_id\": 331968,\n                 \"first_name\": \"Johann\",\n                 \"middle_name\": \"Sebastian\",\n                 \"last_name\": \"Bach\",\n                 \"phones\": [\n                     \"+13152572166\"\n                 ],\n                 \"emails\": [\n                     \"example@test.com\",\n                     \"bah@gmail.com\"\n                 ]\n             }\n          }\n      }\n      \"request\": {\n          \"lead\": {\n             \"client\": {\n                  \"phone\": \"+37369636963\",\n                  \"email\": \"example@test.com\",\n                  \"uuid\" : \"af5246f1-094f-4fde-ada3-bd7298621613\"\n              },\n              \"uid\": \"WD6q53PO3b\",\n              \"status\": 14,\n              \"source_code\": \"JIVOCH\",\n              \"cabin\": \"E\",\n              \"adults\": 2,\n              \"children\": 2,\n              \"infants\": 2,\n              \"request_ip\": \"12.12.12.12\",\n              \"discount_id\": \"123123\",\n              \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36\",\n              \"flight_id\": 12457,\n              \"user_language\": \"en-GB\",\n              \"expire_at\": \"2020-01-20 12:12:12\",\n              \"flights\": [\n                  {\n                      \"origin\": \"NYC\",\n                      \"destination\": \"LON\",\n                      \"departure\": \"2019-12-16\"\n                  },\n                  {\n                      \"origin\": \"LON\",\n                      \"destination\": \"NYC\",\n                      \"departure\": \"2019-12-17\"\n                  },\n                  {\n                      \"origin\": \"LON\",\n                      \"destination\": \"NYC\",\n                      \"departure\": \"2019-12-18\"\n                  }\n              ]\n          }\n      },\n      \"technical\": {\n          \"action\": \"v2/lead/create\",\n          \"response_id\": 11930215,\n          \"request_dt\": \"2019-12-30 12:22:20\",\n          \"response_dt\": \"2019-12-30 12:22:21\",\n          \"execution_time\": 0.055,\n          \"memory_usage\": 1394416\n      }\n}",
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
          "content": "\nHTTP/1.1 200 OK\n  {\n      \"status\": 200,\n      \"message\": \"OK\",\n      \"offer\": {\n          \"of_gid\": \"04d3fe3fc74d0514ee93e208a52bcf90\",\n          \"of_uid\": \"of5e2c5ea22b0f1\",\n          \"of_name\": \"Offer 2\",\n          \"of_lead_id\": 371096,\n          \"of_status_id\": 1,\n          \"of_client_currency\": \"EUR\",\n          \"of_client_currency_rate\": 1,\n          \"of_app_total\": 0,\n          \"of_client_total\": 0,\n          \"quotes\": [\n              {\n                  \"pq_gid\": \"6fcfc43e977dabffe6a979ebda22a281\",\n                  \"pq_name\": \"\",\n                  \"pq_order_id\": 10,\n                  \"pq_description\": null,\n                  \"pq_status_id\": 1,\n                  \"pq_price\": 92.3,\n                  \"pq_origin_price\": 92.3,\n                  \"pq_client_price\": 92.3,\n                  \"pq_service_fee_sum\": null,\n                  \"pq_origin_currency\": \"USD\",\n                  \"pq_client_currency\": \"USD\",\n                  \"data\": {\n                      \"fq_flight_id\": 21,\n                      \"fq_source_id\": null,\n                      \"fq_product_quote_id\": 6,\n                      \"fq_gds\": \"A\",\n                      \"fq_gds_pcc\": \"DFWG32100\",\n                      \"fq_gds_offer_id\": null,\n                      \"fq_type_id\": 0,\n                      \"fq_cabin_class\": \"E\",\n                      \"fq_trip_type_id\": 1,\n                      \"fq_main_airline\": \"SU\",\n                      \"fq_fare_type_id\": 1,\n                      \"fq_last_ticket_date\": \"2020-01-25\",\n                      \"fq_origin_search_data\": \"description field: json encoded origin data from search api\",\n                      \"flight\": {\n                          \"fl_product_id\": 33,\n                          \"fl_trip_type_id\": 1,\n                          \"fl_cabin_class\": \"E\",\n                          \"fl_adults\": 2,\n                          \"fl_children\": 0,\n                          \"fl_infants\": 0\n                      },\n                      \"segments\": [\n                          {\n                              \"fqs_departure_dt\": \"2020-01-30 01:35:00\",\n                              \"fqs_arrival_dt\": \"2020-01-30 05:45:00\",\n                              \"fqs_stop\": 0,\n                              \"fqs_flight_number\": 1845,\n                              \"fqs_booking_class\": \"R\",\n                              \"fqs_duration\": 190,\n                              \"fqs_departure_airport_iata\": \"KIV\",\n                              \"fqs_departure_airport_terminal\": \"\",\n                              \"fqs_arrival_airport_iata\": \"SVO\",\n                              \"fqs_arrival_airport_terminal\": \"D\",\n                              \"fqs_operating_airline\": \"SU\",\n                              \"fqs_marketing_airline\": \"SU\",\n                              \"fqs_air_equip_type\": \"32A\",\n                              \"fqs_marriage_group\": \"\",\n                              \"fqs_cabin_class\": \"Y\",\n                              \"fqs_meal\": \"\",\n                              \"fqs_fare_code\": \"RNO\",\n                              \"fqs_ticket_id\": null,\n                              \"fqs_recheck_baggage\": 0,\n                              \"fqs_mileage\": null,\n                              \"departureLocation\": \"Chisinau\",\n                              \"arrivalLocation\": \"Bucharest\",\n                              \"baggages\": [\n                                  {\n                                      \"qsb_flight_pax_code_id\": 1,\n                                      \"qsb_flight_quote_segment_id\": 2,\n                                      \"qsb_airline_code\": null,\n                                      \"qsb_allow_pieces\": 0,\n                                      \"qsb_allow_weight\": null,\n                                      \"qsb_allow_unit\": null,\n                                      \"qsb_allow_max_weight\": null,\n                                      \"qsb_allow_max_size\": null\n                                  }\n                              ]\n                          }\n                      ],\n                      \"pax_prices\": [\n                         {\n                              \"qpp_fare\": \"43.00\",\n                              \"qpp_tax\": \"49.30\",\n                              \"qpp_system_mark_up\": \"0.00\",\n                              \"qpp_agent_mark_up\": \"0.00\",\n                              \"qpp_origin_fare\": \"43.00\",\n                              \"qpp_origin_currency\": \"USD\",\n                              \"qpp_origin_tax\": \"49.30\",\n                              \"qpp_client_currency\": \"USD\",\n                              \"qpp_client_fare\": null,\n                              \"qpp_client_tax\": null,\n                              \"paxType\":\"ADT\"\n                          }\n                      ]\n                  },\n                  \"product\": {\n                      \"pr_type_id\": 1,\n                      \"pr_name\": \"\",\n                      \"pr_lead_id\": 371096,\n                      \"pr_description\": \"\",\n                      \"pr_status_id\": null,\n                      \"pr_service_fee_percent\": null\n                  },\n                  \"productQuoteOptions\": []\n              },\n              {\n                  \"pq_gid\": \"16fb0f9565b9cb87280a348c75c05128\",\n                  \"pq_name\": \"DBL.ST\",\n                  \"pq_order_id\": null,\n                  \"pq_description\": null,\n                  \"pq_status_id\": 1,\n                  \"pq_price\": 349.99,\n                  \"pq_origin_price\": 349.99,\n                  \"pq_client_price\": 349.99,\n                  \"pq_service_fee_sum\": 0,\n                  \"pq_origin_currency\": \"USD\",\n                  \"pq_client_currency\": \"USD\",\n                  \"data\": {\n                     \"hotel_request\": {\n                                \"ph_check_in_date\": \"2021-06-10\",\n                                \"ph_check_out_date\": \"2021-06-18\",\n                                \"ph_destination_code\": \"LON\",\n                                \"ph_destination_label\": \"United Kingdom, London\",\n                                \"destination_city\": \"London\"\n                            },\n                      \"hotel\": {\n                          \"hl_name\": \"Manzil Hotel\",\n                          \"hl_star\": \"2*\",\n                          \"hl_category_name\": \"2 STARS\",\n                          \"hl_destination_name\": \"Casablanca\",\n                          \"hl_zone_name\": \"Casablanca\",\n                          \"hl_country_code\": \"MA\",\n                          \"hl_state_code\": \"07\",\n                          \"hl_description\": \"The Hotel is ideally located in the neighborhood of Roches Noires district and close to the activity area of ​​Ain Sebaa...\",\n                          \"hl_address\": \"RUE DES FRANCAIS, ROCHES NOIRES,38  \",\n                          \"hl_postal_code\": \"20000\",\n                          \"hl_city\": \"CASABLANCA\",\n                          \"hl_email\": \"resa@manzilhotels.com\",\n                          \"hl_web\": \"\",\n                          \"hl_phone_list\": [\n                             {\n                                  \"type\": \"PHONEBOOKING\",\n                                  \"number\": \"00212522242020\"\n                              },\n                              {\n                                  \"type\": \"PHONEHOTEL\",\n                                  \"number\": \"00212522242020\"\n                              },\n                              {\n                                  \"type\": \"FAXNUMBER\",\n                                  \"number\": \"00212522242020\"\n                              }\n                          ],\n                          \"hl_image_list\": [\n                              {\n                                  \"url\": \"59/590133/590133a_hb_a_001.jpg\",\n                                  \"type\": \"GEN\"\n                              }\n                          ],\n                          \"hl_image_base_url\": \"\"\n                      },\n                      \"rooms\": [\n                          {\n                              \"hqr_room_name\": \"DOUBLE STANDARD\",\n                              \"hqr_class\": \"NOR\",\n                              \"hqr_amount\": 349.99,\n                              \"hqr_currency\": \"USD\",\n                              \"hqr_cancel_amount\": 293.58,\n                              \"hqr_cancel_from_dt\": \"2020-05-14 20:59:00\",\n                              \"hqr_board_name\": \"ROOM ONLY\",\n                              \"hqr_rooms\": 1,\n                              \"hqr_adults\": 1,\n                              \"hqr_children\": 1\n                          }\n                      ]\n                  },\n                  \"product\": {\n                      \"pr_type_id\": 2,\n                      \"pr_name\": \"ee\",\n                      \"pr_lead_id\": 371096,\n                      \"pr_description\": \"rrr\",\n                      \"pr_status_id\": 1,\n                      \"pr_service_fee_percent\": 3.50\n                  },\n                  \"productQuoteOptions\": [\n                      {\n                          \"pqo_name\": \"1323\",\n                          \"pqo_description\": \"\",\n                          \"pqo_status_id\": 1,\n                          \"pqo_price\": 10.00,\n                          \"pqo_client_price\": 15.00,\n                          \"pqo_extra_markup\": 10.00\n                      },\n                      {\n                          \"pqo_name\": \"tests\",\n                          \"pqo_description\": \"swe we \",\n                          \"pqo_status_id\": 1,\n                          \"pqo_price\": 12,\n                          \"pqo_client_price\": null,\n                          \"pqo_extra_markup\": 1\n                      }\n                  ]\n              },\n              {\n                  \"pq_gid\": \"1576705c738f49538f9335ae89528c75\",\n                  \"pq_name\": \"4A\",\n                  \"pq_order_id\": null,\n                  \"pq_description\": null,\n                  \"pq_status_id\": 2,\n                  \"pq_price\": 557.87,\n                  \"pq_origin_price\": 539,\n                  \"pq_client_price\": 513.29,\n                  \"pq_service_fee_sum\": 18.87,\n                  \"pq_origin_currency\": \"USD\",\n                  \"pq_client_currency\": \"EUR\",\n                  \"data\": {\n                      \"cruiseLine\": {\n                          \"code\": \"CV\",\n                          \"name\": \"Carnival Cruise Lines\"\n                      },\n                      \"departureDate\": \"2021-07-01\",\n                      \"returnDate\": \"2021-07-05\",\n                      \"destination\": \"Caribbean\",\n                      \"subDestination\": \"Western Caribbean\",\n                      \"ship\": {\n                          \"code\": \"BR\",\n                          \"name\": \"Carnival Breeze\"\n                      },\n                      \"cabin\": {\n                          \"code\": \"4A\",\n                          \"name\": \"Interior\",\n                          \"price\": 539,\n                          \"imgUrl\": \"https://mediaim.expedia.com/cruise/cv-br-2020-02-01/a7f6f5bd-aef2-417e-87d2-fb3fce2baeba.jpg?impolicy=resizecrop&ra=fit&rw=500\",\n                          \"experience\": \"INSIDE\"\n                      },\n                      \"departureName\": \"Galveston\",\n                      \"crq_data_json\": \"description field: json origin data from search api\"\n                  },\n                  \"product\": {\n                      \"pr_type_id\": 4,\n                      \"pr_name\": \"Cruise test 1\",\n                      \"pr_lead_id\": 513177,\n                      \"pr_description\": \"\",\n                      \"pr_status_id\": null,\n                      \"pr_service_fee_percent\": null\n                  },\n                  \"productQuoteOptions\": []\n              },\n              {\n                 \"pq_gid\":\"1f2a619ee37af592dfd6c927ef00b795\",\n                 \"pq_name\":\"Boat Tour in Miami with a Free Drink\",\n                 \"pq_order_id\":null,\n                 \"pq_description\":null,\n                 \"pq_status_id\":2,\n                 \"pq_price\":28,\n                 \"pq_origin_price\":28,\n                 \"pq_client_price\":28,\n                 \"pq_service_fee_sum\":0,\n                 \"pq_origin_currency\":\"USD\",\n                 \"pq_client_currency\":\"USD\",\n                 \"data\": {\n                     \"atnq_booking_id\":null,\n                     \"atnq_attraction_name\":\"Boat Tour in Miami with a Free Drink\",\n                     \"atnq_supplier_name\":\"Miami Tour Company\",\n                     \"atnq_json_response\": \"description field: json origin data from search api\",\n                     \"search_request\": {\n                         \"atn_product_id\":234,\n                         \"atn_date_from\":\"2021-06-17\",\n                         \"atn_date_to\":\"2021-06-24\",\n                         \"atn_destination\":\"Italy, Rome\",\n                         \"atn_destination_code\":\"ROE\"\n                      }\n                   },\n                  \"product\": {\n                         \"pr_type_id\":5,\n                         \"pr_name\":\"\",\n                         \"pr_lead_id\":15356,\n                         \"pr_description\":\"\",\n                         \"pr_status_id\":null,\n                         \"pr_service_fee_percent\":null\n                   },\n                   \"productQuoteOptions\":[]\n                 },\n             {\n                 \"pq_gid\":\"1f2a619ee37af592dfd6c927ef00b795\",\n                 \"pq_name\":\"Boat Tour in Miami with a Free Drink\",\n                 \"pq_order_id\":null,\n                 \"pq_description\":\"example\",\n                 \"pq_status_id\":2,\n                 \"pq_price\":28,\n                 \"pq_origin_price\":28,\n                 \"pq_client_price\":28,\n                 \"pq_service_fee_sum\":0,\n                 \"pq_origin_currency\":\"USD\",\n                 \"pq_client_currency\":\"USD\",\n                 \"data\": {\n                     \"rcq_model_name\":\"Nissan Rogue or similar\",\n                     \"rcq_category\":\"Midsize SUV\",\n                     \"rcq_image_url\":\"https://example.com/inmage.jpg\",\n                     \"rcq_vendor_name\":\"Hertz Rental Company\",\n                     \"rcq_vendor_logo_url\":\"https://example.com/logo.jpg\",\n                     \"rcq_options\": {\n                         \"doors\":4,\n                         \"person\":5,\n                         \"ac_unit\":\"Air Conditioning\",\n                         \"mileage\":\"Unlimited mileage\",\n                         \"cleanliness\":\"Enhanced cleaning\",\n                         \"transmission\":\"Automatic\"\n                      },\n                     \"search_request\": {\n                         \"prc_product_id\":234,\n                         \"prc_pick_up_date\":\"2021-06-17\",\n                         \"prc_drop_off_date\":\"2021-06-24\",\n                         \"prc_pick_up_code\":\"KIV\",\n                         \"prc_drop_off_code\":\"KIV\",\n                         \"prc_pick_up_time\":\"01:45:00\",\n                         \"prc_drop_off_time\":\"04:00:00\"\n                      }\n                   },\n                  \"product\": {\n                         \"pr_type_id\":5,\n                         \"pr_name\":\"offer for my best client\",\n                         \"pr_lead_id\":15356,\n                         \"pr_description\":\"\",\n                         \"pr_status_id\":null,\n                         \"pr_service_fee_percent\":null\n                   },\n                   \"productQuoteOptions\":[]\n                 }\n\n          ]\n      },\n      \"technical\": {\n          \"action\": \"v2/offer/view\",\n          \"response_id\": 11933859,\n          \"request_dt\": \"2020-02-03 12:53:50\",\n          \"response_dt\": \"2020-02-03 12:53:50\",\n          \"execution_time\": 0.034,\n          \"memory_usage\": 1255920\n      },\n      \"request\": {\n          \"offerGid\": \"04d3fe3fc74d0514ee93e208a52bcf90\",\n          \"visitor\": {\n              \"id\": \"hdsjfghsd5489tertwhf289hfgkewr\",\n              \"ipAddress\": \"12.12.12.12\",\n              \"userAgent\": \"mozilea/asdfsdf/ as/dfgsdf gsdf gsdgf/ds\"\n          }\n      }\n  }",
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
            "type": "String",
            "optional": false,
            "field": "offerGid",
            "description": "<p>Offer gid</p>"
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
            "type": "String",
            "optional": false,
            "field": "productQuotes.gid",
            "description": "<p>Product Quote Gid</p>"
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
            "type": "String",
            "optional": false,
            "field": "payment.type",
            "description": "<p>Type</p>"
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "payment.transactionId",
            "description": "<p>Transaction Id</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
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
            "type": "String",
            "optional": false,
            "field": "payment.currency",
            "description": "<p>Currency</p>"
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
          "content": "\n{\n    \"offerGid\": \"73c8bf13111feff52794883446461740\",\n    \"productQuotes\": [\n    {\n    \"gid\": \"aebf921f5a64a7ac98d4942ace67e498\"\n    },\n    {\n    \"gid\": \"6fcfc43e977dabffe6a979ebdaddfvr2\"\n    }\n    ],\n    \"payment\": {\n        \"type\": \"card\",\n        \"transactionId\": 1234567890,\n        \"date\": \"2021-03-20\",\n        \"amount\": 821.49,\n        \"currency\": \"USD\"\n    },\n    \"Request\": {\n    \"offerGid\": \"85a06c376a083f47e56b286b1265c160\",\n    \"offerUid\": \"of60264c1484090\",\n    \"apiKey\": \"038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826\",\n    \"source\": \"I1B1L1\",\n    \"subSource\": \"-\",\n    \"totalOrderAmount\": 821.49,\n    \"FlightRequest\": {\n    \"productGid\": \"c6ae37ae73380c773cadf28fc0af9db2\",\n    \"uid\": \"OE96040\",\n    \"email\": \"mike.kane@techork.com\",\n    \"marker\": null,\n    \"client_ip_address\": \"92.115.180.30\",\n    \"trip_protection_amount\": \"0\",\n    \"insurance_code\": \"P7\",\n    \"is_facilitate\": 0,\n    \"delay_change\": false,\n    \"is_b2b\": false,\n    \"uplift\": false,\n    \"alipay\": false,\n    \"user_country\": \"us\",\n    \"user_language\": \"en-US\",\n    \"user_time_format\": \"h:mm a\",\n    \"user_month_date_format\": {\n    \"long\": \"EEE MMM d\",\n    \"short\": \"MMM d\",\n    \"fullDateLong\": \"EEE MMM d\",\n    \"fullDateShort\": \"MMM d, YYYY\"\n    },\n    \"currency_symbol\": \"$\",\n    \"pnr\": null\n    },\n    \"HotelRequest\": {\n    \"productGid\": \"cdd82f2616f600f71a68e9399c51276e\"\n    },\n    \"DriverRequest\": {\n    \"productGid\": \"cdd82f2616f600f71a68e9399c51276e\"\n    },\n    \"AttractionRequest\": {\n    \"productGid\": \"cdd82f2616f600f71a68e9399c51276e\"\n    },\n    \"CruiseRequest\": {\n    \"productGid\": \"cdd82f2616f600f71a68e9399c51276e\"\n    },\n    \"Card\": {\n    \"user_id\": null,\n    \"nickname\": \"B****** E***** T\",\n    \"number\": \"************6444\",\n    \"type\": \"Visa\",\n    \"expiration_date\": \"07 \\/ 2023\",\n    \"first_name\": \"Barbara Elmore\",\n    \"middle_name\": \"\",\n    \"last_name\": \"T\",\n    \"address\": \"1013 Weda Cir\",\n    \"country_id\": \"US\",\n    \"city\": \"Mayfield\",\n    \"state\": \"KY\",\n    \"zip\": \"99999\",\n    \"phone\": \"+19074861000\",\n    \"deleted\": null,\n    \"cvv\": \"***\",\n    \"auth_attempts\": null,\n    \"country\": \"United States\",\n    \"calling\": \"\",\n    \"client_ip_address\": \"92.115.180.30\",\n    \"email\": \"mike.kane@techork.com\",\n    \"document\": null\n    },\n    \"AirRouting\": {\n    \"results\": [\n    {\n    \"gds\": \"S\",\n    \"key\": \"2_T1ZBMTAxKlkxMDAwL0xBWFRQRTIwMjEtMDUtMTMvVFBFTEFYMjAyMS0wNi0yMCpQUn4jUFIxMDMjUFI4OTAjUFI4OTEjUFIxMDJ+bGM6ZW5fdXM=\",\n    \"pcc\": \"8KI0\",\n    \"cons\": \"GTT\",\n    \"keys\": {\n    \"services\": {\n    \"support\": {\n    \"amount\": 75\n    }\n    },\n    \"seatHoldSeg\": {\n    \"trip\": 0,\n    \"seats\": 9,\n    \"segment\": 0\n    },\n    \"verification\": {\n    \"headers\": {\n    \"X-Client-Ip\": \"92.115.180.30\",\n    \"X-Kiv-Cust-Ip\": \"92.115.180.30\",\n    \"X-Kiv-Cust-ipv\": \"0\",\n    \"X-Kiv-Cust-ssid\": \"ovago-dev-0484692\",\n    \"X-Kiv-Cust-direct\": \"true\",\n    \"X-Kiv-Cust-browser\": \"desktop\"\n    }\n    }\n    },\n    \"meta\": {\n    \"eip\": 0,\n    \"bags\": 2,\n    \"best\": false,\n    \"lang\": \"en\",\n    \"rank\": 6,\n    \"group1\": \"LAXTPE:PRPR:0:TPELAX:PRPR:0:767.75\",\n    \"country\": \"us\",\n    \"fastest\": false,\n    \"noavail\": false,\n    \"cheapest\": true,\n    \"searchId\": \"T1ZBMTAxWTEwMDB8TEFYVFBFMjAyMS0wNS0xM3xUUEVMQVgyMDIxLTA2LTIw\"\n    },\n    \"cabin\": \"Y\",\n    \"trips\": [\n    {\n    \"tripId\": 1,\n    \"duration\": 1150,\n    \"segments\": [\n    {\n    \"meal\": \"D\",\n    \"stop\": 0,\n    \"cabin\": \"Y\",\n    \"stops\": [],\n    \"baggage\": {\n    \"ADT\": {\n    \"carryOn\": true,\n    \"airlineCode\": \"PR\",\n    \"allowPieces\": 2,\n    \"allowMaxSize\": \"UP TO 62 LINEAR INCHES\\/158 LINEAR CENTIMETERS\",\n    \"allowMaxWeight\": \"UP TO 50 POUNDS\\/23 KILOGRAMS\"\n    }\n    },\n    \"mileage\": 7305,\n    \"duration\": 870,\n    \"fareCode\": \"U9XBUS\",\n    \"segmentId\": 1,\n    \"arrivalTime\": \"2021-05-15 04:00\",\n    \"airEquipType\": \"773\",\n    \"bookingClass\": \"U\",\n    \"flightNumber\": \"103\",\n    \"departureTime\": \"2021-05-13 22:30\",\n    \"marriageGroup\": \"O\",\n    \"recheckBaggage\": false,\n    \"marketingAirline\": \"PR\",\n    \"operatingAirline\": \"PR\",\n    \"arrivalAirportCode\": \"MNL\",\n    \"departureAirportCode\": \"LAX\",\n    \"arrivalAirportTerminal\": \"2\",\n    \"departureAirportTerminal\": \"B\"\n    },\n    {\n    \"meal\": \"B\",\n    \"stop\": 0,\n    \"cabin\": \"Y\",\n    \"stops\": [],\n    \"baggage\": {\n    \"ADT\": {\n    \"carryOn\": true,\n    \"airlineCode\": \"PR\",\n    \"allowPieces\": 2,\n    \"allowMaxSize\": \"UP TO 62 LINEAR INCHES\\/158 LINEAR CENTIMETERS\",\n    \"allowMaxWeight\": \"UP TO 50 POUNDS\\/23 KILOGRAMS\"\n    }\n    },\n    \"mileage\": 728,\n    \"duration\": 130,\n    \"fareCode\": \"U9XBUS\",\n    \"segmentId\": 2,\n    \"arrivalTime\": \"2021-05-15 08:40\",\n    \"airEquipType\": \"321\",\n    \"bookingClass\": \"U\",\n    \"flightNumber\": \"890\",\n    \"departureTime\": \"2021-05-15 06:30\",\n    \"marriageGroup\": \"I\",\n    \"recheckBaggage\": false,\n    \"marketingAirline\": \"PR\",\n    \"operatingAirline\": \"PR\",\n    \"arrivalAirportCode\": \"TPE\",\n    \"departureAirportCode\": \"MNL\",\n    \"arrivalAirportTerminal\": \"1\",\n    \"departureAirportTerminal\": \"1\"\n    }\n    ]\n    },\n    {\n    \"tripId\": 2,\n    \"duration\": 1490,\n    \"segments\": [\n    {\n    \"meal\": \"H\",\n    \"stop\": 0,\n    \"cabin\": \"Y\",\n    \"stops\": [],\n    \"baggage\": {\n    \"ADT\": {\n    \"carryOn\": true,\n    \"airlineCode\": \"PR\",\n    \"allowPieces\": 2,\n    \"allowMaxSize\": \"UP TO 62 LINEAR INCHES\\/158 LINEAR CENTIMETERS\",\n    \"allowMaxWeight\": \"UP TO 50 POUNDS\\/23 KILOGRAMS\"\n    }\n    },\n    \"mileage\": 728,\n    \"duration\": 145,\n    \"fareCode\": \"U9XBUS\",\n    \"segmentId\": 1,\n    \"arrivalTime\": \"2021-06-20 12:05\",\n    \"airEquipType\": \"321\",\n    \"bookingClass\": \"U\",\n    \"flightNumber\": \"891\",\n    \"departureTime\": \"2021-06-20 09:40\",\n    \"marriageGroup\": \"O\",\n    \"recheckBaggage\": false,\n    \"marketingAirline\": \"PR\",\n    \"operatingAirline\": \"PR\",\n    \"arrivalAirportCode\": \"MNL\",\n    \"departureAirportCode\": \"TPE\",\n    \"arrivalAirportTerminal\": \"2\",\n    \"departureAirportTerminal\": \"1\"\n    },\n    {\n    \"meal\": \"D\",\n    \"stop\": 0,\n    \"cabin\": \"Y\",\n    \"stops\": [],\n    \"baggage\": {\n    \"ADT\": {\n    \"carryOn\": true,\n    \"airlineCode\": \"PR\",\n    \"allowPieces\": 2,\n    \"allowMaxSize\": \"UP TO 62 LINEAR INCHES\\/158 LINEAR CENTIMETERS\",\n    \"allowMaxWeight\": \"UP TO 50 POUNDS\\/23 KILOGRAMS\"\n    }\n    },\n    \"mileage\": 7305,\n    \"duration\": 805,\n    \"fareCode\": \"U9XBUS\",\n    \"segmentId\": 2,\n    \"arrivalTime\": \"2021-06-20 19:30\",\n    \"airEquipType\": \"773\",\n    \"bookingClass\": \"U\",\n    \"flightNumber\": \"102\",\n    \"departureTime\": \"2021-06-20 21:05\",\n    \"marriageGroup\": \"I\",\n    \"recheckBaggage\": false,\n    \"marketingAirline\": \"PR\",\n    \"operatingAirline\": \"PR\",\n    \"arrivalAirportCode\": \"LAX\",\n    \"departureAirportCode\": \"MNL\",\n    \"arrivalAirportTerminal\": \"B\",\n    \"departureAirportTerminal\": \"1\"\n    }\n    ]\n    }\n    ],\n    \"paxCnt\": 1,\n    \"prices\": {\n    \"comm\": 0,\n    \"isCk\": false,\n    \"ccCap\": 16.900002,\n    \"markup\": 50,\n    \"oMarkup\": {\n    \"amount\": 50,\n    \"currency\": \"USD\"\n    },\n    \"markupId\": 8833,\n    \"totalTax\": 321.75,\n    \"markupUid\": \"1c7afe8c-a34f-434e-8fa3-87b9b7b1ff4e\",\n    \"totalPrice\": 767.75,\n    \"lastTicketDate\": \"2021-03-31\"\n    },\n    \"currency\": \"USD\",\n    \"fareType\": \"SR\",\n    \"maxSeats\": 9,\n    \"tripType\": \"RT\",\n    \"penalties\": {\n    \"list\": [\n    {\n    \"type\": \"re\",\n    \"permitted\": false,\n    \"applicability\": \"before\"\n    },\n    {\n    \"type\": \"re\",\n    \"permitted\": false,\n    \"applicability\": \"after\"\n    },\n    {\n    \"type\": \"ex\",\n    \"amount\": 425,\n    \"oAmount\": {\n    \"amount\": 425,\n    \"currency\": \"USD\"\n    },\n    \"permitted\": true,\n    \"applicability\": \"before\"\n    },\n    {\n    \"type\": \"ex\",\n    \"amount\": 425,\n    \"oAmount\": {\n    \"amount\": 425,\n    \"currency\": \"USD\"\n    },\n    \"permitted\": true,\n    \"applicability\": \"after\"\n    }\n    ],\n    \"refund\": false,\n    \"exchange\": true\n    },\n    \"routingId\": 1,\n    \"currencies\": [\n    \"USD\"\n    ],\n    \"founded_dt\": \"2021-02-25 13:44:54.570\",\n    \"passengers\": {\n    \"ADT\": {\n    \"cnt\": 1,\n    \"tax\": 321.75,\n    \"comm\": 0,\n    \"ccCap\": 16.900002,\n    \"price\": 767.75,\n    \"codeAs\": \"JCB\",\n    \"markup\": 50,\n    \"occCap\": {\n    \"amount\": 16.900002,\n    \"currency\": \"USD\"\n    },\n    \"baseTax\": 271.75,\n    \"oMarkup\": {\n    \"amount\": 50,\n    \"currency\": \"USD\"\n    },\n    \"baseFare\": 446,\n    \"oBaseTax\": {\n    \"amount\": 271.75,\n    \"currency\": \"USD\"\n    },\n    \"oBaseFare\": {\n    \"amount\": 446,\n    \"currency\": \"USD\"\n    },\n    \"pubBaseFare\": 446\n    }\n    },\n    \"ngsFeatures\": {\n    \"list\": null,\n    \"name\": \"\",\n    \"stars\": 3\n    },\n    \"currencyRates\": {\n    \"CADUSD\": {\n    \"to\": \"USD\",\n    \"from\": \"CAD\",\n    \"rate\": 0.78417\n    },\n    \"DKKUSD\": {\n    \"to\": \"USD\",\n    \"from\": \"DKK\",\n    \"rate\": 0.16459\n    },\n    \"EURUSD\": {\n    \"to\": \"USD\",\n    \"from\": \"EUR\",\n    \"rate\": 1.23967\n    },\n    \"GBPUSD\": {\n    \"to\": \"USD\",\n    \"from\": \"GBP\",\n    \"rate\": 1.37643\n    },\n    \"KRWUSD\": {\n    \"to\": \"USD\",\n    \"from\": \"KRW\",\n    \"rate\": 0.00091\n    },\n    \"MYRUSD\": {\n    \"to\": \"USD\",\n    \"from\": \"MYR\",\n    \"rate\": 0.25006\n    },\n    \"SEKUSD\": {\n    \"to\": \"USD\",\n    \"from\": \"SEK\",\n    \"rate\": 0.12221\n    },\n    \"TWDUSD\": {\n    \"to\": \"USD\",\n    \"from\": \"TWD\",\n    \"rate\": 0.03592\n    },\n    \"USDCAD\": {\n    \"to\": \"CAD\",\n    \"from\": \"USD\",\n    \"rate\": 1.30086\n    },\n    \"USDDKK\": {\n    \"to\": \"DKK\",\n    \"from\": \"USD\",\n    \"rate\": 6.19797\n    },\n    \"USDEUR\": {\n    \"to\": \"EUR\",\n    \"from\": \"USD\",\n    \"rate\": 0.83926\n    },\n    \"USDGBP\": {\n    \"to\": \"GBP\",\n    \"from\": \"USD\",\n    \"rate\": 0.75587\n    },\n    \"USDKRW\": {\n    \"to\": \"KRW\",\n    \"from\": \"USD\",\n    \"rate\": 1117.1008\n    },\n    \"USDMYR\": {\n    \"to\": \"MYR\",\n    \"from\": \"USD\",\n    \"rate\": 4.07943\n    },\n    \"USDSEK\": {\n    \"to\": \"SEK\",\n    \"from\": \"USD\",\n    \"rate\": 8.34736\n    },\n    \"USDTWD\": {\n    \"to\": \"TWD\",\n    \"from\": \"USD\",\n    \"rate\": 28.96525\n    },\n    \"USDUSD\": {\n    \"to\": \"USD\",\n    \"from\": \"USD\",\n    \"rate\": 1\n    }\n    },\n    \"validatingCarrier\": \"PR\"\n    }\n    ],\n    \"additionalInfo\": {\n    \"cabin\": {\n    \"C\": \"Business\",\n    \"F\": \"First\",\n    \"J\": \"Premium Business\",\n    \"P\": \"Premium First\",\n    \"S\": \"Premium Economy\",\n    \"Y\": \"Economy\"\n    },\n    \"airline\": {\n    \"PR\": {\n    \"name\": \"Philippine Airlines\"\n    }\n    },\n    \"airport\": {\n    \"LAX\": {\n    \"city\": \"Los Angeles\",\n    \"name\": \"Los Angeles International Airport\",\n    \"country\": \"United States\"\n    },\n    \"MNL\": {\n    \"city\": \"Manila\",\n    \"name\": \"Ninoy Aquino International Airport\",\n    \"country\": \"Philippines\"\n    },\n    \"TPE\": {\n    \"city\": \"Taipei\",\n    \"name\": \"Taiwan Taoyuan International Airport\",\n    \"country\": \"Taiwan\"\n    }\n    },\n    \"general\": {\n    \"tripType\": \"rt\"\n    }\n    }\n    },\n    \"Passengers\": {\n    \"Flight\": [\n    {\n    \"id\": null,\n    \"user_id\": null,\n    \"first_name\": \"Arthur\",\n    \"middle_name\": \"\",\n    \"last_name\": \"Davis\",\n    \"birth_date\": \"1963-04-07\",\n    \"gender\": \"M\",\n    \"seats\": null,\n    \"assistance\": null,\n    \"nationality\": \"US\",\n    \"passport_id\": null,\n    \"passport_valid_date\": null,\n    \"email\": null,\n    \"codeAs\": null\n    }\n    ],\n    \"Hotel\": [\n    {\n    \"first_name\": \"mike\",\n    \"last_name\": \"kane\"\n    }\n    ],\n    \"Driver\": [\n    {\n    \"first_name\": \"mike\",\n    \"last_name\": \"kane\",\n    \"age\": \"30-69\",\n    \"birth_date\": \"1973-04-07\"\n    }\n    ],\n    \"Attraction\": [\n    {\n    \"first_name\": \"mike\",\n    \"last_name\": \"kane\",\n    \"language_service\": \"US\"\n    }\n    ],\n    \"Cruise\": [\n    {\n    \"first_name\": \"Arthur\",\n    \"last_name\": \"Davis\",\n    \"citizenship\": \"US\",\n    \"birth_date\": \"1963-04-07\",\n    \"gender\": \"M\"\n    }\n    ]\n    },\n    \"Insurance\": {\n    \"total_amount\": \"20\",\n    \"record_id\": \"396393\",\n    \"passengers\": [\n    {\n    \"nameRef\": \"0\",\n    \"amount\": 20\n    }\n    ]\n    },\n    \"Tip\": {\n    \"total_amount\": 20\n    },\n    \"AuxiliarProducts\": {\n    \"Flight\": {\n    \"basket\": {\n    \"1c3df555-a2dc-4813-a055-2a8bf56fd8f1\": {\n    \"basket_item_id\": \"1c3df555-a2dc-4813-a055-2a8bf56fd8f1\",\n    \"benefits\": [],\n    \"display_name\": \"10kg Bag\",\n    \"price\": {\n    \"base\": {\n    \"amount\": 2000,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2,\n    \"in_original_currency\": {\n    \"amount\": 1820,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2\n    }\n    },\n    \"fees\": [],\n    \"markups\": [\n    {\n    \"amount\": 600,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2,\n    \"in_original_currency\": {\n    \"amount\": 546,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2\n    },\n    \"markup_type\": \"markup\"\n    }\n    ],\n    \"taxes\": [\n    {\n    \"amount\": 200,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2,\n    \"in_original_currency\": {\n    \"amount\": 182,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2\n    },\n    \"tax_type\": \"tax\"\n    }\n    ],\n    \"total\": {\n    \"amount\": 2400,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2,\n    \"in_original_currency\": {\n    \"amount\": 2184,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2\n    }\n    }\n    },\n    \"product_details\": {\n    \"journey_id\": \"1770bf8f-0c1c-4ba5-99f5-56e446fe79ba\",\n    \"passenger_id\": \"p1\",\n    \"size\": 150,\n    \"size_unit\": \"cm\",\n    \"weight\": 10,\n    \"weight_unit\": \"kg\"\n    },\n    \"product_id\": \"741bcc97-c2fe-4820-b14d-f11f32e6fadb\",\n    \"product_type\": \"bag\",\n    \"quantity\": 1,\n    \"ticket_id\": \"e8558737-2ec0-436f-89ec-00e7a20b3252\",\n    \"validity\": {\n    \"state\": \"valid\",\n    \"valid_from\": \"2020-05-22T16:34:08Z\",\n    \"valid_to\": \"2020-05-22T16:49:08Z\"\n    }\n    },\n    \"2654f3f9-8990-4d2e-bdea-3b341ad5d1de\": {\n    \"basket_item_id\": \"2654f3f9-8990-4d2e-bdea-3b341ad5d1de\",\n    \"benefits\": [],\n    \"display_name\": \"Seat 15C\",\n    \"price\": {\n    \"base\": {\n    \"amount\": 2000,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2,\n    \"in_original_currency\": {\n    \"amount\": 1820,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2\n    }\n    },\n    \"fees\": [],\n    \"markups\": [\n    {\n    \"amount\": 400,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2,\n    \"in_original_currency\": {\n    \"amount\": 364,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2\n    },\n    \"markup_type\": \"markup\"\n    }\n    ],\n    \"taxes\": [\n    {\n    \"amount\": 200,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2,\n    \"in_original_currency\": [],\n    \"tax_type\": \"tax\"\n    }\n    ],\n    \"total\": {\n    \"amount\": 2600,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2,\n    \"in_original_currency\": {\n    \"amount\": 2366,\n    \"currency\": \"USD\",\n    \"decimal_places\": 2\n    }\n    }\n    },\n    \"product_details\": {\n    \"column\": \"C\",\n    \"passenger_id\": \"p1\",\n    \"row\": 15,\n    \"segment_id\": \"1770bf8f-0c1c-4ba5-99f5-56e446fe79ba\"\n    },\n    \"product_id\": \"a17e10ca-0c9a-4691-9922-d664a3b52382\",\n    \"product_type\": \"seat\",\n    \"quantity\": 1,\n    \"ticket_id\": \"e8558737-2ec0-436f-89ec-00e7a20b3252\",\n    \"validity\": {\n    \"state\": \"valid\",\n    \"valid_from\": \"2020-05-22T16:34:08Z\",\n    \"valid_to\": \"2020-05-22T16:49:08Z\"\n    }\n    },\n    \"5d5e1bce-4577-4118-abcb-155823d8b4a3\": [],\n    \"6acd57ba-ccb7-4e86-85e7-b3e586caeae2\": [],\n    \"dffac4ba-73b9-4b1b-9334-001817fff0cf\": [],\n    \"e960eff9-7628-4645-99d8-20a6e22f6419\": []\n    },\n    \"country\": \"US\",\n    \"currency\": \"USD\",\n    \"journeys\": [\n    {\n    \"journey_id\": \"aab8980e-b263-4624-ad40-d6e5e364b4e9\",\n    \"segments\": [\n    {\n    \"arrival_airport\": \"LHR\",\n    \"arrival_time\": \"2020-07-07T22:30:00Z\",\n    \"departure_airport\": \"EDI\",\n    \"departure_time\": \"2020-07-07T21:10:00Z\",\n    \"fare_basis\": \"OTZ0RO\\/Y\",\n    \"fare_class\": \"O\",\n    \"fare_family\": \"Basic Economy\",\n    \"marketing_airline\": \"BA\",\n    \"marketing_flight_number\": \"1465\",\n    \"number_of_stops\": 0,\n    \"operating_airline\": \"BA\",\n    \"operating_flight_number\": \"1465\",\n    \"segment_id\": \"938d8e82-dd7c-4d85-8ab4-38fea8753f6f\"\n    }\n    ]\n    },\n    {\n    \"journey_id\": \"1770bf8f-0c1c-4ba5-99f5-56e446fe79ba\",\n    \"segments\": [\n    {\n    \"arrival_airport\": \"EDI\",\n    \"arrival_time\": \"2020-07-14T08:35:00Z\",\n    \"departure_airport\": \"LGW\",\n    \"departure_time\": \"2020-07-14T07:05:00Z\",\n    \"fare_basis\": \"NALZ0KO\\/Y\",\n    \"fare_class\": \"N\",\n    \"fare_family\": \"Basic Economy\",\n    \"marketing_airline\": \"BA\",\n    \"marketing_flight_number\": \"2500\",\n    \"number_of_stops\": 0,\n    \"operating_airline\": \"BA\",\n    \"operating_flight_number\": \"2500\",\n    \"segment_id\": \"7d693cb0-d6d8-49f0-9489-866b3d789215\"\n    }\n    ]\n    }\n    ],\n    \"language\": \"en-US\",\n    \"orders\": [],\n    \"passengers\": [\n    {\n    \"first_names\": \"Vincent Willem\",\n    \"passenger_id\": \"ee850c82-e150-4f35-b0c7-228064c2964b\",\n    \"surname\": \"Van Gogh\"\n    }\n    ],\n    \"tickets\": [\n    {\n    \"basket_item_ids\": [\n    \"dffac4ba-73b9-4b1b-9334-001817fff0cf\",\n    \"e960eff9-7628-4645-99d8-20a6e22f6419\",\n    \"6acd57ba-ccb7-4e86-85e7-b3e586caeae2\",\n    \"5d5e1bce-4577-4118-abcb-155823d8b4a3\"\n    ],\n    \"journey_ids\": [\n    \"aab8980e-b263-4624-ad40-d6e5e364b4e9\"\n    ],\n    \"state\": \"in_basket\",\n    \"ticket_basket_item_id\": \"dffac4ba-73b9-4b1b-9334-001817fff0cf\",\n    \"ticket_id\": \"8c1c9fc8-d968-4733-93a8-6067bac2543f\"\n    },\n    {\n    \"basket_item_ids\": [\n    \"2654f3f9-8990-4d2e-bdea-3b341ad5d1de\",\n    \"1c3df555-a2dc-4813-a055-2a8bf56fd8f1\"\n    ],\n    \"journey_ids\": [\n    \"1770bf8f-0c1c-4ba5-99f5-56e446fe79ba\"\n    ],\n    \"offered_price\": {\n    \"currency\": \"USD\",\n    \"decimal_places\": 2,\n    \"total\": 20000\n    },\n    \"state\": \"offered\",\n    \"ticket_id\": \"e8558737-2ec0-436f-89ec-00e7a20b3252\"\n    }\n    ],\n    \"trip_access_token\": \"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c\",\n    \"trip_id\": \"23259b86-3208-44c9-85cc-4b116a822bff\",\n    \"trip_state_hash\": \"69abcc117863186292bdf5f1c0d94db1e5227210935e6abe039cfb017cbefbee\"\n    },\n    \"Hotel\": [],\n    \"Driver\": [],\n    \"Attraction\": [],\n    \"Cruise\": []\n    },\n    \"Payment\": {\n    \"type\": \"CARD\",\n    \"transaction_id\": \"1234567890\",\n    \"card_id\": 234567,\n    \"auth_id\": 123456\n    }\n    }\n    }",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\nHTTP/1.1 200 OK\n  {\n            \"status\": 200,\n            \"message\": \"OK\",\n            \"data\": {\n                \"order_gid\": \"ef75bfa7cc60af154c22c43e3732350f\"\n            },\n            \"technical\": {\n                \"action\": \"v2/order/create\",\n                \"response_id\": 327,\n                \"request_dt\": \"2021-02-27 08:49:46\",\n                \"response_dt\": \"2021-02-27 08:49:46\",\n                \"execution_time\": 0.094,\n                \"memory_usage\": 1356920\n            }\n         }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response (422):",
          "content": "\nHTTP/1.1 422 Unprocessable entity\n{\n            \"status\": 422,\n            \"message\": \"Validation error\",\n            \"errors\": {\n                \"payment.type\": [\n                    \"Type is invalid.\"\n                ]\n            },\n            \"code\": 0,\n            \"technical\": {\n                \"action\": \"v2/order/create\",\n                \"response_id\": 328,\n                \"request_dt\": \"2021-02-27 08:52:06\",\n                \"response_dt\": \"2021-02-27 08:52:06\",\n                \"execution_time\": 0.021,\n                \"memory_usage\": 437656\n            }\n        }",
          "type": "json"
        }
      ]
    },
    "filename": "webapi/modules/v2/controllers/OrderController.php",
    "groupTitle": "Orders"
  },
  {
    "type": "post",
    "url": "/v2/order/create-proxy",
    "title": "Create Order Proxy",
    "version": "0.2.0",
    "name": "CreateOrderProxy",
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
    "groupTitle": "Orders"
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
          "content": "{\n     \"apiKey\": \"d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd\",\n     \"Lead\": {\n         \"uid\": \"5de486f15f095\",\n         \"market_info_id\": 52,\n         \"bo_flight_id\": 0,\n         \"final_profit\": 0\n     },\n     \"Quote\": {\n         \"uid\": \"5f207ec201b99\",\n         \"record_locator\": null,\n         \"pcc\": \"0RY9\",\n         \"cabin\": \"E\",\n         \"gds\": \"S\",\n         \"trip_type\": \"RT\",\n         \"main_airline_code\": \"UA\",\n         \"reservation_dump\": \"1 KL6123V 15OCT Q MCOAMS SS1   801P 1100A  16OCT F /DCKL /E \\n 2 KL1009L 18OCT S AMSLHR SS1  1015A 1045A /DCKL /E\",\n         \"status\": 1,\n         \"check_payment\": \"1\",\n         \"fare_type\": \"TOUR\",\n         \"employee_name\": \"Barry\",\n         \"created_by_seller\": false,\n         \"type_id\" : 0\n     },\n     \"QuotePrice\": [\n         {\n             \"uid\": \"expert.5f207ec222c86\",\n             \"passenger_type\": \"ADT\",\n             \"selling\": 696.19,\n             \"net\": 622.65,\n             \"fare\": 127,\n             \"taxes\": 495.65,\n             \"mark_up\": 50,\n             \"extra_mark_up\": 0,\n             \"service_fee\": 23.54\n         }\n     ]\n}",
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
          "content": "HTTP/1.1 200 OK\n{\n  \"status\": \"Success\",\n  \"itinerary\": {\n      \"typeId\": 2,\n      \"typeName\": \"Alternative\",\n      \"tripType\": \"OW\",\n      \"mainCarrier\": \"WOW air\",\n      \"trips\": [\n          {\n              \"segments\": [\n                  {\n                      \"carrier\": \"WW\",\n                      \"airlineName\": \"WOW air\",\n                      \"departureAirport\": \"BOS\",\n                      \"arrivalAirport\": \"KEF\",\n                      \"departureDateTime\": {\n                          \"date\": \"2018-09-19 19:00:00.000000\",\n                          \"timezone_type\": 3,\n                          \"timezone\": \"UTC\"\n                      },\n                      \"arrivalDateTime\": {\n                          \"date\": \"2018-09-20 04:30:00.000000\",\n                          \"timezone_type\": 3,\n                          \"timezone\": \"UTC\"\n                      },\n                      \"flightNumber\": \"126\",\n                      \"bookingClass\": \"O\",\n                      \"departureCity\": \"Boston\",\n                      \"arrivalCity\": \"Reykjavik\",\n                      \"flightDuration\": 330,\n                      \"layoverDuration\": 0,\n                      \"cabin\": \"E\",\n                      \"departureCountry\": \"United States\",\n                      \"arrivalCountry\": \"Iceland\"\n                  },\n                  {\n                      \"carrier\": \"WW\",\n                      \"airlineName\": \"WOW air\",\n                      \"departureAirport\": \"KEF\",\n                      \"arrivalAirport\": \"LGW\",\n                      \"departureDateTime\": {\n                          \"date\": \"2018-09-20 15:30:00.000000\",\n                          \"timezone_type\": 3,\n                          \"timezone\": \"UTC\"\n                      },\n                      \"arrivalDateTime\": {\n                          \"date\": \"2018-09-20 19:50:00.000000\",\n                          \"timezone_type\": 3,\n                          \"timezone\": \"UTC\"\n                      },\n                      \"flightNumber\": \"814\",\n                      \"bookingClass\": \"N\",\n                      \"departureCity\": \"Reykjavik\",\n                      \"arrivalCity\": \"London\",\n                      \"flightDuration\": 200,\n                      \"layoverDuration\": 660,\n                      \"cabin\": \"E\",\n                      \"departureCountry\": \"Iceland\",\n                      \"arrivalCountry\": \"United Kingdom\"\n                  }\n              ],\n              \"totalDuration\": 1190,\n              \"routing\": \"BOS-KEF-LGW\",\n              \"title\": \"Boston - London\"\n          }\n      ],\n      \"price\": {\n          \"detail\": {\n              \"ADT\": {\n                  \"selling\": 350.2,\n                  \"fare\": 237,\n                  \"taxes\": 113.2,\n                  \"tickets\": 1\n              }\n          },\n          \"tickets\": 1,\n          \"selling\": 350.2,\n          \"amountPerPax\": 350.2,\n          \"fare\": 237,\n          \"mark_up\": 0,\n          \"taxes\": 113.2,\n          \"currency\": \"USD\",\n          \"isCC\": false\n      }\n  },\n \"itineraryOrigin\": {\n     \"uid\": \"5f207ec202212\",\n     \"typeId\": 1,\n     \"typeName\": \"Original\",\n     \"tripType\": \"OW\",\n     \"mainCarrier\": \"WOW air\",\n     \"trips\": [\n          {\n              \"segments\": [\n                  {\n                      \"carrier\": \"WW\",\n                      \"airlineName\": \"WOW air\",\n                      \"departureAirport\": \"BOS\",\n                      \"arrivalAirport\": \"KEF\",\n                      \"departureDateTime\": {\n                          \"date\": \"2018-09-19 19:00:00.000000\",\n                          \"timezone_type\": 3,\n                          \"timezone\": \"UTC\"\n                      },\n                      \"arrivalDateTime\": {\n                          \"date\": \"2018-09-20 04:30:00.000000\",\n                          \"timezone_type\": 3,\n                          \"timezone\": \"UTC\"\n                      },\n                      \"flightNumber\": \"126\",\n                      \"bookingClass\": \"O\",\n                      \"departureCity\": \"Boston\",\n                      \"arrivalCity\": \"Reykjavik\",\n                      \"flightDuration\": 330,\n                      \"layoverDuration\": 0,\n                      \"cabin\": \"E\",\n                      \"departureCountry\": \"United States\",\n                      \"arrivalCountry\": \"Iceland\"\n                  }\n              ],\n              \"totalDuration\": 1190,\n              \"routing\": \"BOS-KEF\",\n              \"title\": \"Boston - London\"\n          }\n      ],\n      \"price\": {\n          \"detail\": {\n              \"ADT\": {\n                  \"selling\": 350.2,\n                  \"fare\": 237,\n                  \"taxes\": 113.2,\n                  \"tickets\": 1\n              }\n          },\n          \"tickets\": 1,\n          \"selling\": 350.2,\n          \"amountPerPax\": 350.2,\n          \"fare\": 237,\n          \"mark_up\": 0,\n          \"taxes\": 113.2,\n          \"currency\": \"USD\",\n          \"isCC\": false\n      }\n  },\n  \"errors\": [],\n  \"uid\": \"5b7424e858e91\",\n  \"lead_id\": 123456,\n  \"lead_uid\": \"00jhk0017\",\n  \"client_id\": 1034,\n  \"client\": {\n      \"id\": 1034,\n      \"uuid\": \"35009a79-1a05-49d7-b876-2b884d0f825b\"\n   },\n  \"lead_delayed_charge\": 0,\n  \"lead_status\": \"sold\",\n  \"booked_quote_uid\": \"5b8ddfc56a15c\",\n  \"source_code\": \"38T556\",\n  \"check_payment\": true,\n  \"agentName\": \"admin\",\n  \"agentEmail\": \"assistant@wowfare.com\",\n  \"agentDirectLine\": \"+1 888 946 3882\",\n  \"visitor_log\": {\n      \"vl_source_cid\": \"string_abc\",\n      \"vl_ga_client_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n      \"vl_ga_user_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n      \"vl_customer_id\": \"3\",\n      \"vl_gclid\": \"gclid=TeSter-123#bookmark\",\n      \"vl_dclid\": \"CJKu8LrQxd4CFQ1qwQodmJIElw\",\n      \"vl_utm_source\": \"newsletter4\",\n      \"vl_utm_medium\": \"string_abc\",\n      \"vl_utm_campaign\": \"string_abc\",\n      \"vl_utm_term\": \"string_abc\",\n      \"vl_utm_content\": \"string_abc\",\n      \"vl_referral_url\": \"string_abc\",\n      \"vl_location_url\": \"string_abc\",\n      \"vl_user_agent\": \"string_abc\",\n      \"vl_ip_address\": \"127.0.0.1\",\n      \"vl_visit_dt\": \"2020-02-14 12:00:00\",\n      \"vl_created_dt\": \"2020-02-28 17:17:33\"\n  },\n \"lead\": {\n      \"additionalInformation\": [\n          {\n             \"pnr\": \"example_pnr\",\n              \"bo_sale_id\": \"example_sale_id\",\n             \"vtf_processed\": null,\n             \"tkt_processed\": null,\n             \"exp_processed\": null,\n             \"passengers\": [],\n             \"paxInfo\": []\n         }\n     ]\n },\n  \"action\": \"v1/quote/get-info\",\n  \"response_id\": 173,\n  \"request_dt\": \"2018-08-16 06:42:03\",\n  \"response_dt\": \"2018-08-16 06:42:03\"\n}",
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
          "content": "HTTP/1.1 200 OK\n{\n  \"status\": \"Success\",\n  \"result\": {\n      \"prices\": {\n          \"totalPrice\": 2056.98,\n          \"totalTax\": 1058.98,\n          \"isCk\": true\n      },\n      \"passengers\": {\n          \"ADT\": {\n              \"cnt\": 2,\n              \"price\": 1028.49,\n              \"tax\": 529.49,\n              \"baseFare\": 499,\n              \"mark_up\": 20,\n              \"extra_mark_up\": 10\n          },\n          \"INF\": {\n              \"cnt\": 1,\n              \"price\": 0,\n              \"tax\": 0,\n              \"baseFare\": 0,\n              \"mark_up\": 0,\n              \"extra_mark_up\": 0\n          }\n      },\n      \"trips\": [\n          {\n              \"tripId\": 1,\n              \"segments\": [\n                  {\n                      \"segmentId\": 1,\n                      \"departureTime\": \"2019-12-06 16:20\",\n                      \"arrivalTime\": \"2019-12-06 17:57\",\n                      \"stop\": 0,\n                      \"stops\": null,\n                      \"flightNumber\": \"7312\",\n                      \"bookingClass\": \"T\",\n                      \"duration\": 97,\n                      \"departureAirportCode\": \"IND\",\n                      \"departureAirportTerminal\": \"\",\n                      \"arrivalAirportCode\": \"YYZ\",\n                      \"arrivalAirportTerminal\": \"\",\n                      \"operatingAirline\": \"AC\",\n                      \"airEquipType\": null,\n                      \"marketingAirline\": \"AC\",\n                      \"cabin\": \"Y\",\n                      \"ticket_id\": 1,\n                      \"baggage\": {\n                          \"\": {\n                              \"allowPieces\": 2,\n                              \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                              \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\",\n                              \"charge\": {\n                                  \"price\": 100,\n                                  \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                  \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                  \"firstPiece\": 1,\n                                  \"lastPiece\": 1\n                              }\n                          }\n                      }\n                  },\n                  {\n                      \"segmentId\": 2,\n                      \"departureTime\": \"2019-12-06 20:45\",\n                      \"arrivalTime\": \"2019-12-07 09:55\",\n                      \"stop\": 0,\n                      \"stops\": null,\n                      \"flightNumber\": \"880\",\n                      \"bookingClass\": \"T\",\n                      \"duration\": 430,\n                      \"departureAirportCode\": \"YYZ\",\n                      \"departureAirportTerminal\": \"\",\n                      \"arrivalAirportCode\": \"CDG\",\n                      \"arrivalAirportTerminal\": \"\",\n                      \"operatingAirline\": \"AC\",\n                      \"airEquipType\": null,\n                      \"marketingAirline\": \"AC\",\n                      \"cabin\": \"Y\",\n                      \"ticket_id\": 2,\n                      \"baggage\": {\n                          \"\": {\n                              \"allowPieces\": 2,\n                              \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                              \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\",\n                              \"charge\": {\n                                  \"price\": 100,\n                                  \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                  \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                  \"firstPiece\": 1,\n                                  \"lastPiece\": 1\n                              }\n                          }\n                      }\n                  },\n                  {\n                      \"segmentId\": 3,\n                      \"departureTime\": \"2019-12-07 13:40\",\n                      \"arrivalTime\": \"2019-12-07 19:05\",\n                      \"stop\": 0,\n                      \"stops\": null,\n                      \"flightNumber\": \"6692\",\n                      \"bookingClass\": \"T\",\n                      \"duration\": 265,\n                      \"departureAirportCode\": \"CDG\",\n                      \"departureAirportTerminal\": \"\",\n                      \"arrivalAirportCode\": \"IST\",\n                      \"arrivalAirportTerminal\": \"\",\n                      \"operatingAirline\": \"AC\",\n                      \"airEquipType\": null,\n                      \"marketingAirline\": \"AC\",\n                      \"cabin\": \"Y\",\n                      \"ticket_id\": 2,\n                      \"baggage\": {\n                          \"\": {\n                              \"allowPieces\": 2,\n                              \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                              \"allowMaxWeight\": \"UP TO 50 POUNDS/23 KILOGRAMS\",\n                              \"charge\": {\n                                  \"price\": 100,\n                                  \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                  \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS\",\n                                  \"firstPiece\": 1,\n                                  \"lastPiece\": 1\n                              }\n                          }\n                      }\n                  }\n              ],\n              \"duration\": 1185\n          },\n          {\n              \"tripId\": 2,\n              \"segments\": [\n                  {\n                      \"segmentId\": 1,\n                      \"departureTime\": \"2019-12-25 09:15\",\n                      \"arrivalTime\": \"2019-12-25 10:35\",\n                      \"stop\": 0,\n                      \"stops\": null,\n                      \"flightNumber\": \"6681\",\n                      \"bookingClass\": \"T\",\n                      \"duration\": 140,\n                      \"departureAirportCode\": \"IST\",\n                      \"departureAirportTerminal\": \"\",\n                      \"arrivalAirportCode\": \"GVA\",\n                      \"arrivalAirportTerminal\": \"\",\n                      \"operatingAirline\": \"AC\",\n                      \"airEquipType\": null,\n                      \"marketingAirline\": \"AC\",\n                      \"cabin\": \"Y\",\n                      \"ticket_id\": 1,\n                      \"baggage\": {\n                          \"\": {\n                              \"allowPieces\": 1,\n                              \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡\",\n                              \"allowMaxWeight\": \"UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS\",\n                              \"charge\": {\n                                  \"price\": 100,\n                                  \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                  \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND\",\n                                  \"firstPiece\": 1,\n                                  \"lastPiece\": 1\n                              }\n                          }\n                      }\n                  },\n                  {\n                      \"segmentId\": 2,\n                      \"departureTime\": \"2019-12-25 12:00\",\n                      \"arrivalTime\": \"2019-12-25 17:34\",\n                      \"stop\": 0,\n                      \"stops\": null,\n                      \"flightNumber\": \"835\",\n                      \"bookingClass\": \"T\",\n                      \"duration\": 694,\n                      \"departureAirportCode\": \"GVA\",\n                      \"departureAirportTerminal\": \"\",\n                      \"arrivalAirportCode\": \"YYZ\",\n                      \"arrivalAirportTerminal\": \"\",\n                      \"operatingAirline\": \"AC\",\n                      \"airEquipType\": null,\n                      \"marketingAirline\": \"AC\",\n                      \"cabin\": \"Y\",\n                      \"ticket_id\": 2,\n                      \"baggage\": {\n                          \"\": {\n                              \"allowPieces\": 1,\n                              \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡\",\n                              \"allowMaxWeight\": \"UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS\",\n                              \"charge\": {\n                                  \"price\": 100,\n                                  \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                  \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND\",\n                                  \"firstPiece\": 1,\n                                  \"lastPiece\": 1\n                              }\n                          }\n                      }\n                  },\n                  {\n                      \"segmentId\": 3,\n                      \"departureTime\": \"2019-12-25 20:55\",\n                      \"arrivalTime\": \"2019-12-25 22:37\",\n                      \"stop\": 0,\n                      \"stops\": null,\n                      \"flightNumber\": \"7313\",\n                      \"bookingClass\": \"T\",\n                      \"duration\": 102,\n                      \"departureAirportCode\": \"YYZ\",\n                      \"departureAirportTerminal\": \"\",\n                      \"arrivalAirportCode\": \"IND\",\n                      \"arrivalAirportTerminal\": \"\",\n                      \"operatingAirline\": \"AC\",\n                      \"airEquipType\": null,\n                      \"marketingAirline\": \"AC\",\n                      \"cabin\": \"Y\",\n                      \"ticket_id\": 2,\n                      \"baggage\": {\n                          \"\": {\n                              \"allowPieces\": 1,\n                              \"allowMaxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡\",\n                              \"allowMaxWeight\": \"UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS\",\n                              \"charge\": {\n                                  \"price\": 100,\n                                  \"maxWeight\": \"UP TO 50 POUNDS/23 KILOG RAMS\",\n                                  \"maxSize\": \"UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND\",\n                                  \"firstPiece\": 1,\n                                  \"lastPiece\": 1\n                              }\n                          }\n                      }\n                  }\n              ],\n              \"duration\": 1222\n          }\n      ],\n      \"validatingCarrier\": \"AC\",\n      \"fareType\": \"PUB\",\n      \"tripType\": \"RT\",\n      \"currency\": \"USD\",\n      \"currencyRate\": 1\n  },\n  \"errors\": [],\n  \"uid\": \"5cb97d1c78486\",\n  \"lead_id\": 92322,\n  \"lead_uid\": \"5cb8735a502f5\",\n  \"lead_expiration_dt\": \"2021-02-23 20:12:12\",\n  \"lead_delayed_charge\": 0,\n  \"lead_status\": null,\n  \"booked_quote_uid\": null,\n  \"source_code\": \"38T556\",\n  \"agentName\": \"admin\",\n  \"agentEmail\": \"admin@wowfare.com\",\n  \"agentDirectLine\": \"\",\n  \"generalEmail\": \"info@wowfare.com\",\n  \"generalDirectLine\": \"+37379731662\",\n  \"typeId\": 2,\n  \"typeName\": \"Alternative\",\n  \"client\": {\n      \"uuid\": \"35009a79-1a05-49d7-b876-2b884d0f825b\"\n      \"client_id\": 331968,\n      \"first_name\": \"Johann\",\n      \"middle_name\": \"Sebastian\",\n      \"last_name\": \"Bach\",\n      \"phones\": [\n          \"+13152572166\"\n      ],\n      \"emails\": [\n          \"example@test.com\",\n          \"bah@gmail.com\"\n      ]\n  },\n  \"quote\": {\n      \"id\": 382366,\n      \"uid\": \"5d43e1ec36372\",\n      \"lead_id\": 178363,\n      \"employee_id\": 167,\n      \"record_locator\": \"\",\n      \"pcc\": \"DFWG32100\",\n      \"cabin\": \"E\",\n      \"gds\": \"A\",\n      \"trip_type\": \"OW\",\n      \"main_airline_code\": \"SU\",\n      \"reservation_dump\": \"1  SU1845T  22AUG  KIVSVO    255A    555A  TH\",\n      \"status\": 5,\n      \"check_payment\": 1,\n      \"fare_type\": \"PUB\",\n      \"created\": \"2019-08-02 07:10:36\",\n      \"updated\": \"2019-08-05 08:58:18\",\n      \"created_by_seller\": 1,\n      \"employee_name\": \"alex.connor2\",\n      \"last_ticket_date\": \"2019-08-09 00:00:00\",\n      \"service_fee_percent\": null,\n      \"pricing_info\": null,\n      \"alternative\": 1,\n      \"tickets\": \"[{\\\"key\\\":\\\"02_QVdBUlFBKlkxMDAwL05ZQ01BRDIwMTktMDgtMjYvTUFETllDMjAxOS0wOS0wNipVQX4jVUE1MSNVQTUw\\\",\\\"routingId\\\":0,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-08-11\\\",\\\"totalPrice\\\":392.73,\\\"totalTax\\\":272.73,\\\"markup\\\":50,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"JWZ\\\",\\\"cnt\\\":1,\\\"price\\\":392.73,\\\"tax\\\":272.73,\\\"baseFare\\\":120,\\\"pubBaseFare\\\":120,\\\"baseTax\\\":222.73,\\\"markup\\\":50,\\\"refundPenalty\\\":\\\"\\\",\\\"changePenalty\\\":\\\"Percentage: 100.00%\\\",\\\"endorsementPenalty\\\":\\\"\\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":120,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":222.73,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}}},\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"UA\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"fareType\\\":\\\"SR\\\",\\\"tripType\\\":\\\"RT\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segmentIds\\\":[1]},{\\\"tripId\\\":2,\\\"segmentIds\\\":[3]}]},{\\\"key\\\":\\\"02_QVdBUlFBKlkxMDAwL01BRFZJRTIwMTktMDgtMjcvVklFTUFEMjAxOS0wOS0wNSpMWH4jTFgyMDI3I0xYMzU2OCNMWDM1NjMjTFgyMDQ4\\\",\\\"routingId\\\":0,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-08-09\\\",\\\"totalPrice\\\":305.3,\\\"totalTax\\\":184.3,\\\"markup\\\":50,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"ADT\\\",\\\"cnt\\\":1,\\\"price\\\":305.3,\\\"tax\\\":184.3,\\\"baseFare\\\":121,\\\"pubBaseFare\\\":121,\\\"baseTax\\\":134.3,\\\"markup\\\":50,\\\"refundPenalty\\\":\\\"Percentage: 100.00%\\\",\\\"changePenalty\\\":\\\"Percentage: 100.00%\\\",\\\"endorsementPenalty\\\":\\\"\\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":121,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":134.3,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}}},\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"LX\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"fareType\\\":\\\"PUB\\\",\\\"tripType\\\":\\\"RT\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segmentIds\\\":[2,3]},{\\\"tripId\\\":2,\\\"segmentIds\\\":[1,2]}]}]\",\n      \"origin_search_data\": \"{\\\"key\\\":\\\"01_U0FMMTAxKlkxMDAwL0pGS0ZSQTIwMTktMTEtMjEqUk9+I1FSNzA0I1FSMjI3I1JPMjk4I1JPMzAxOjNkMjBiYzI5LWIzMmItNGJhOC05OTljLTQ4ZTFlYWI1NGU1Ng==\\\",\\\"routingId\\\":306,\\\"gdsOfferId\\\":\\\"3d20bc29-b32b-4ba8-999c-48e1eab54e56\\\",\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-11-23\\\",\\\"totalPrice\\\":670.35,\\\"totalTax\\\":367.35,\\\"markup\\\":100,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":100,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"JWZ\\\",\\\"cnt\\\":1,\\\"price\\\":670.35,\\\"tax\\\":367.35,\\\"baseFare\\\":303,\\\"pubBaseFare\\\":303,\\\"baseTax\\\":267.35,\\\"markup\\\":100,\\\"refundPenalty\\\":\\\"Amount: USD375.00 Percentage: 100.00%\\\",\\\"changePenalty\\\":\\\"Amount: USD260.00 Percentage: 100.00%\\\",\\\"endorsementPenalty\\\":\\\" \\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":303,\\\"currency\\\":\\\"USD\\\"},\\\"oPubBaseFare\\\":{\\\"amount\\\":303,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":267.35,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":100,\\\"currency\\\":\\\"USD\\\"}}},\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segments\\\":[{\\\"segmentId\\\":1,\\\"departureTime\\\":\\\"2019-11-21 09:45\\\",\\\"arrivalTime\\\":\\\"2019-11-22 06:00\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"704\\\",\\\"bookingClass\\\":\\\"N\\\",\\\"duration\\\":735,\\\"departureAirportCode\\\":\\\"JFK\\\",\\\"departureAirportTerminal\\\":\\\"7\\\",\\\"arrivalAirportCode\\\":\\\"DOH\\\",\\\"arrivalAirportTerminal\\\":\\\"\\\",\\\"operatingAirline\\\":\\\"QR\\\",\\\"airEquipType\\\":\\\"351\\\",\\\"marketingAirline\\\":\\\"QR\\\",\\\"marriageGroup\\\":\\\"I\\\",\\\"mileage\\\":6689,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"NLUSN1RO\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"allowPieces\\\":2}},\\\"recheckBaggage\\\":false},{\\\"segmentId\\\":2,\\\"departureTime\\\":\\\"2019-11-22 07:10\\\",\\\"arrivalTime\\\":\\\"2019-11-22 11:25\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"227\\\",\\\"bookingClass\\\":\\\"N\\\",\\\"duration\\\":315,\\\"departureAirportCode\\\":\\\"DOH\\\",\\\"departureAirportTerminal\\\":\\\"\\\",\\\"arrivalAirportCode\\\":\\\"SOF\\\",\\\"arrivalAirportTerminal\\\":\\\"2\\\",\\\"operatingAirline\\\":\\\"QR\\\",\\\"airEquipType\\\":\\\"320\\\",\\\"marketingAirline\\\":\\\"QR\\\",\\\"marriageGroup\\\":\\\"O\\\",\\\"mileage\\\":1999,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"NLUSN1RO\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"allowPieces\\\":2}},\\\"recheckBaggage\\\":false},{\\\"segmentId\\\":3,\\\"departureTime\\\":\\\"2019-11-22 19:45\\\",\\\"arrivalTime\\\":\\\"2019-11-22 20:50\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"298\\\",\\\"bookingClass\\\":\\\"T\\\",\\\"duration\\\":65,\\\"departureAirportCode\\\":\\\"SOF\\\",\\\"departureAirportTerminal\\\":\\\"2\\\",\\\"arrivalAirportCode\\\":\\\"OTP\\\",\\\"arrivalAirportTerminal\\\":\\\"\\\",\\\"operatingAirline\\\":\\\"RO\\\",\\\"airEquipType\\\":\\\"AT7\\\",\\\"marketingAirline\\\":\\\"RO\\\",\\\"marriageGroup\\\":\\\"I\\\",\\\"mileage\\\":185,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"TOWSVR\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"allowPieces\\\":1}},\\\"recheckBaggage\\\":true},{\\\"segmentId\\\":4,\\\"departureTime\\\":\\\"2019-11-23 08:35\\\",\\\"arrivalTime\\\":\\\"2019-11-23 10:15\\\",\\\"stop\\\":0,\\\"stops\\\":[],\\\"flightNumber\\\":\\\"301\\\",\\\"bookingClass\\\":\\\"T\\\",\\\"duration\\\":160,\\\"departureAirportCode\\\":\\\"OTP\\\",\\\"departureAirportTerminal\\\":\\\"\\\",\\\"arrivalAirportCode\\\":\\\"FRA\\\",\\\"arrivalAirportTerminal\\\":\\\"2\\\",\\\"operatingAirline\\\":\\\"RO\\\",\\\"airEquipType\\\":\\\"73W\\\",\\\"marketingAirline\\\":\\\"RO\\\",\\\"marriageGroup\\\":\\\"O\\\",\\\"mileage\\\":903,\\\"cabin\\\":\\\"Y\\\",\\\"meal\\\":\\\"\\\",\\\"fareCode\\\":\\\"TOWSVR\\\",\\\"baggage\\\":{\\\"ADT\\\":{\\\"allowPieces\\\":1}},\\\"recheckBaggage\\\":false}],\\\"duration\\\":2550}],\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"RO\\\",\\\"gds\\\":\\\"G\\\",\\\"pcc\\\":\\\"NA\\\",\\\"cons\\\":\\\"GIS\\\",\\\"fareType\\\":\\\"NA\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"currencies\\\":[\\\"USD\\\"],\\\"currencyRates\\\":{\\\"USDUSD\\\":{\\\"from\\\":\\\"USD\\\",\\\"to\\\":\\\"USD\\\",\\\"rate\\\":1}},\\\"tickets\\\":[{\\\"key\\\":\\\"02_QVdBUlFBKlkxMDAwL0pGS1NPRjIwMTktMTEtMjEqUVJ+I1FSNzA0I1FSMjI3\\\",\\\"routingId\\\":0,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-11-21\\\",\\\"totalPrice\\\":388.8,\\\"totalTax\\\":267.8,\\\"markup\\\":50,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"JWZ\\\",\\\"cnt\\\":1,\\\"price\\\":388.8,\\\"tax\\\":267.8,\\\"baseFare\\\":121,\\\"pubBaseFare\\\":121,\\\"baseTax\\\":217.8,\\\"markup\\\":50,\\\"refundPenalty\\\":\\\"Amount: USD375.00 \\\",\\\"changePenalty\\\":\\\"Amount: USD260.00\\\",\\\"endorsementPenalty\\\":\\\"\\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":121,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":217.8,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}}},\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"QR\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"fareType\\\":\\\"SR\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segmentIds\\\":[1,2]}]},{\\\"key\\\":\\\"01_QVdBUlFBKlkxMDAwL1NPRkZSQTIwMTktMTEtMjIqUk9+I1JPMjk4I1JPMzAx\\\",\\\"routingId\\\":0,\\\"prices\\\":{\\\"lastTicketDate\\\":\\\"2019-10-19\\\",\\\"totalPrice\\\":265.6,\\\"totalTax\\\":83.6,\\\"markup\\\":50,\\\"markupId\\\":0,\\\"isCk\\\":false,\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}},\\\"passengers\\\":{\\\"ADT\\\":{\\\"codeAs\\\":\\\"ADT\\\",\\\"cnt\\\":1,\\\"price\\\":265.6,\\\"tax\\\":83.6,\\\"baseFare\\\":182,\\\"pubBaseFare\\\":182,\\\"baseTax\\\":33.6,\\\"markup\\\":50,\\\"refundPenalty\\\":\\\"Percentage: 100.00%\\\",\\\"changePenalty\\\":\\\"Percentage: 100.00%\\\",\\\"endorsementPenalty\\\":\\\"\\\",\\\"publishFare\\\":false,\\\"fareDescription\\\":\\\"\\\",\\\"oBaseFare\\\":{\\\"amount\\\":182,\\\"currency\\\":\\\"USD\\\"},\\\"oBaseTax\\\":{\\\"amount\\\":33.6,\\\"currency\\\":\\\"USD\\\"},\\\"oMarkup\\\":{\\\"amount\\\":50,\\\"currency\\\":\\\"USD\\\"}}},\\\"maxSeats\\\":0,\\\"validatingCarrier\\\":\\\"RO\\\",\\\"gds\\\":\\\"T\\\",\\\"pcc\\\":\\\"E9V\\\",\\\"fareType\\\":\\\"PUB\\\",\\\"tripType\\\":\\\"OW\\\",\\\"cabin\\\":\\\"Y\\\",\\\"currency\\\":\\\"USD\\\",\\\"trips\\\":[{\\\"tripId\\\":1,\\\"segmentIds\\\":[3,4]}]}]}\",\n      \"typeId\": 2,\n      \"typeName\": \"Alternative\"\n  },\n  \"itineraryOrigin\": {\n     \"uid\": \"5f207ec202212\",\n     \"typeId\": 1,\n     \"typeName\": \"Original\"\n  },\n  \"visitor_log\": {\n      \"vl_source_cid\": \"string_abc\",\n      \"vl_ga_client_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n      \"vl_ga_user_id\": \"35009a79-1a05-49d7-b876-2b884d0f825b\",\n      \"vl_customer_id\": \"3\",\n      \"vl_gclid\": \"gclid=TeSter-123#bookmark\",\n      \"vl_dclid\": \"CJKu8LrQxd4CFQ1qwQodmJIElw\",\n      \"vl_utm_source\": \"newsletter4\",\n      \"vl_utm_medium\": \"string_abc\",\n      \"vl_utm_campaign\": \"string_abc\",\n      \"vl_utm_term\": \"string_abc\",\n      \"vl_utm_content\": \"string_abc\",\n      \"vl_referral_url\": \"string_abc\",\n      \"vl_location_url\": \"string_abc\",\n      \"vl_user_agent\": \"string_abc\",\n      \"vl_ip_address\": \"127.0.0.1\",\n      \"vl_visit_dt\": \"2020-02-14 12:00:00\",\n      \"vl_created_dt\": \"2020-02-28 17:17:33\"\n  },\n  \"lead\": {\n      \"additionalInformation\": [\n          {\n             \"pnr\": \"example_pnr\",\n              \"bo_sale_id\": \"example_sale_id\",\n             \"vtf_processed\": null,\n             \"tkt_processed\": null,\n             \"exp_processed\": null,\n             \"passengers\": [],\n             \"paxInfo\": []\n         }\n     ]\n  },\n  \"action\": \"v2/quote/get-info\",\n  \"response_id\": 298939,\n  \"request_dt\": \"2019-04-25 13:12:44\",\n  \"response_dt\": \"2019-04-25 13:12:44\"\n}",
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
