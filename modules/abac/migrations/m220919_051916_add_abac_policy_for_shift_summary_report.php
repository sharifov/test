<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220919_051916_add_abac_policy_for_shift_summary_report
 */
class m220919_051916_add_abac_policy_for_shift_summary_report extends Migration
{
    /**
     * shift/shift/act/summary_report - (access)
     */
    private const DUMP = 'eyJhcF9pZCI6NDM2LCJhcF9ydWxlX3R5cGUiOiJwIiwiYXBfc3ViamVjdCI6IihcImFkbWluXCIgaW4gci5zdWIuZW52LnVzZXIucm9sZXMpIiwiYXBfc3ViamVjdF9qc29uIjoie1wiY29uZGl0aW9uXCI6XCJBTkRcIixcInJ1bGVzXCI6W3tcImlkXCI6XCJlbnZfdXNlcl9yb2xlc1wiLFwiZmllbGRcIjpcImVudi51c2VyLnJvbGVzXCIsXCJ0eXBlXCI6XCJzdHJpbmdcIixcImlucHV0XCI6XCJzZWxlY3RcIixcIm9wZXJhdG9yXCI6XCJpbl9hcnJheVwiLFwidmFsdWVcIjpcImFkbWluXCJ9XSxcIm5vdFwiOmZhbHNlLFwidmFsaWRcIjp0cnVlfSIsImFwX29iamVjdCI6InNoaWZ0L3NoaWZ0L2FjdC9zdW1tYXJ5X3JlcG9ydCIsImFwX2FjdGlvbiI6IihhY2Nlc3MpIiwiYXBfYWN0aW9uX2pzb24iOiJbXCJhY2Nlc3NcIl0iLCJhcF9lZmZlY3QiOjEsImFwX3RpdGxlIjoiQWNjZXNzIHRvIHNoaWZ0LXNjaGVkdWxlL3N1bW1hcnktcmVwb3J0IiwiYXBfc29ydF9vcmRlciI6NTAsImFwX2NyZWF0ZWRfZHQiOiIyMDIyLTA5LTE5IDA2OjAwOjI3IiwiYXBfdXBkYXRlZF9kdCI6IjIwMjItMDktMTkgMDY6MDA6MjciLCJhcF9jcmVhdGVkX3VzZXJfaWQiOjEsImFwX3VwZGF0ZWRfdXNlcl9pZCI6MSwiYXBfZW5hYmxlZCI6MSwiYXBfaGFzaF9jb2RlIjoiMjJmYTA4YWM4YiJ9';

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
