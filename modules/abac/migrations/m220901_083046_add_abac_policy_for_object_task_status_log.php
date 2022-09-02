<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220901_083046_add_abac_policy_for_object_task_status_log
 */
class m220901_083046_add_abac_policy_for_object_task_status_log extends Migration
{
    /**
     * objectTask/act/object_task_status_log - (access)|(create)|(update)|(delete)
     */
    private const DUMP = 'eyJhcF9pZCI6NDMwLCJhcF9ydWxlX3R5cGUiOiJwIiwiYXBfc3ViamVjdCI6IihcImFkbWluXCIgaW4gci5zdWIuZW52LnVzZXIucm9sZXMpIHx8IChcInNhbGVzX21hbmFnZXJcIiBpbiByLnN1Yi5lbnYudXNlci5yb2xlcykiLCJhcF9zdWJqZWN0X2pzb24iOiJ7XCJjb25kaXRpb25cIjpcIk9SXCIsXCJydWxlc1wiOlt7XCJpZFwiOlwiZW52X3VzZXJfcm9sZXNcIixcImZpZWxkXCI6XCJlbnYudXNlci5yb2xlc1wiLFwidHlwZVwiOlwic3RyaW5nXCIsXCJpbnB1dFwiOlwic2VsZWN0XCIsXCJvcGVyYXRvclwiOlwiaW5fYXJyYXlcIixcInZhbHVlXCI6XCJhZG1pblwifSx7XCJpZFwiOlwiZW52X3VzZXJfcm9sZXNcIixcImZpZWxkXCI6XCJlbnYudXNlci5yb2xlc1wiLFwidHlwZVwiOlwic3RyaW5nXCIsXCJpbnB1dFwiOlwic2VsZWN0XCIsXCJvcGVyYXRvclwiOlwiaW5fYXJyYXlcIixcInZhbHVlXCI6XCJzYWxlc19tYW5hZ2VyXCJ9XSxcIm5vdFwiOmZhbHNlLFwidmFsaWRcIjp0cnVlfSIsImFwX29iamVjdCI6Im9iamVjdFRhc2svYWN0L29iamVjdF90YXNrX3N0YXR1c19sb2ciLCJhcF9hY3Rpb24iOiIoYWNjZXNzKXwoY3JlYXRlKXwodXBkYXRlKXwoZGVsZXRlKSIsImFwX2FjdGlvbl9qc29uIjoiW1wiYWNjZXNzXCIsXCJjcmVhdGVcIixcInVwZGF0ZVwiLFwiZGVsZXRlXCJdIiwiYXBfZWZmZWN0IjoxLCJhcF90aXRsZSI6IkFjY2VzcyB0byBvYmplY3QgdGFzayBzdGF0dXMgbG9nIiwiYXBfc29ydF9vcmRlciI6NTAsImFwX2NyZWF0ZWRfZHQiOiIyMDIyLTA5LTAxIDA4OjMzOjM5IiwiYXBfdXBkYXRlZF9kdCI6IjIwMjItMDktMDEgMDg6MzM6MzkiLCJhcF9jcmVhdGVkX3VzZXJfaWQiOjEsImFwX3VwZGF0ZWRfdXNlcl9pZCI6MSwiYXBfZW5hYmxlZCI6MSwiYXBfaGFzaF9jb2RlIjoiMmExMjRhM2FjNCJ9';

    public function safeUp()
    {
        echo AbacService::importPolicyFromDump(self::DUMP, true) ? '- OK: added' : '- Error: not added';
        echo PHP_EOL;
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        echo AbacService::removePolicyFromDump(self::DUMP) ? '- OK: removed' : '- Error: not removed';
        echo PHP_EOL;
    }
}
