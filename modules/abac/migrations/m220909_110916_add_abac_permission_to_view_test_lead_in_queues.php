<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220909_110916_add_abac_permission_to_view_test_lead_in_queues
 */
class m220909_110916_add_abac_permission_to_view_test_lead_in_queues extends Migration
{
    /**
     * lead/lead/query/show_is_test - (access)
     */
    private const DUMP = 'eyJhcF9pZCI6NDMzLCJhcF9ydWxlX3R5cGUiOiJwIiwiYXBfc3ViamVjdCI6IihcImFkbWluXCIgaW4gci5zdWIuZW52LnVzZXIucm9sZXMpIiwiYXBfc3ViamVjdF9qc29uIjoie1wiY29uZGl0aW9uXCI6XCJBTkRcIixcInJ1bGVzXCI6W3tcImlkXCI6XCJlbnZfdXNlcl9yb2xlc1wiLFwiZmllbGRcIjpcImVudi51c2VyLnJvbGVzXCIsXCJ0eXBlXCI6XCJzdHJpbmdcIixcImlucHV0XCI6XCJzZWxlY3RcIixcIm9wZXJhdG9yXCI6XCJpbl9hcnJheVwiLFwidmFsdWVcIjpcImFkbWluXCJ9XSxcIm5vdFwiOmZhbHNlLFwidmFsaWRcIjp0cnVlfSIsImFwX29iamVjdCI6ImxlYWQvbGVhZC9xdWVyeS9zaG93X2lzX3Rlc3QiLCJhcF9hY3Rpb24iOiIoYWNjZXNzKSIsImFwX2FjdGlvbl9qc29uIjoiW1wiYWNjZXNzXCJdIiwiYXBfZWZmZWN0IjoxLCJhcF90aXRsZSI6IlNob3cgaXMgdGVzdCBsZWFkIGluIHF1ZXVlcyIsImFwX3NvcnRfb3JkZXIiOjUwLCJhcF9jcmVhdGVkX2R0IjoiMjAyMi0wOS0wOSAxMDoxMjoyNiIsImFwX3VwZGF0ZWRfZHQiOiIyMDIyLTA5LTA5IDExOjEwOjMwIiwiYXBfY3JlYXRlZF91c2VyX2lkIjoxLCJhcF91cGRhdGVkX3VzZXJfaWQiOjEsImFwX2VuYWJsZWQiOjEsImFwX2hhc2hfY29kZSI6IjA1YTUyOGFlYzMifQ==';

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
