<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220817_131504_add_abac_permission_to_view_fare_rules
 */
class m220817_131504_add_abac_permission_to_view_fare_rules extends Migration
{
    public string $dump = 'eyJhcF9pZCI6NDQ2LCJhcF9ydWxlX3R5cGUiOiJwIiwiYXBfc3ViamVjdCI6IihyLnN1Yi5lbnYuYXZhaWxhYmxlID09IHRydWUpIiwiYXBfc3ViamVjdF9qc29uIjoie1wiY29uZGl0aW9uXCI6XCJBTkRcIixcInJ1bGVzXCI6W3tcImlkXCI6XCJlbnZfYXZhaWxhYmxlXCIsXCJmaWVsZFwiOlwiZW52LmF2YWlsYWJsZVwiLFwidHlwZVwiOlwiYm9vbGVhblwiLFwiaW5wdXRcIjpcInJhZGlvXCIsXCJvcGVyYXRvclwiOlwiPT1cIixcInZhbHVlXCI6dHJ1ZX1dLFwibm90XCI6ZmFsc2UsXCJ2YWxpZFwiOnRydWV9IiwiYXBfb2JqZWN0IjoiY2FzZS9jYXNlL3VpL2Jsb2NrL3NhbGUtbGlzdCIsImFwX2FjdGlvbiI6Iih2aWV3RmFyZVJ1bGVzKSIsImFwX2FjdGlvbl9qc29uIjoiW1widmlld0ZhcmVSdWxlc1wiXSIsImFwX2VmZmVjdCI6MSwiYXBfdGl0bGUiOiJWaWV3IEZhcmUgUnVsZXMiLCJhcF9zb3J0X29yZGVyIjo1MCwiYXBfY3JlYXRlZF9kdCI6IjIwMjItMDgtMTcgMTA6MjI6MDEiLCJhcF91cGRhdGVkX2R0IjoiMjAyMi0wOC0xNyAxMDoyMzowOCIsImFwX2NyZWF0ZWRfdXNlcl9pZCI6bnVsbCwiYXBfdXBkYXRlZF91c2VyX2lkIjoxLCJhcF9lbmFibGVkIjoxLCJhcF9oYXNoX2NvZGUiOiI4ZGUwYzQ2ZjNiIn0=';

    /**
     * @return void
     */
    public function safeUp()
    {
        echo AbacService::importPolicyFromDump($this->dump, true) ? '- OK: added' : '- Error: not added';
        echo PHP_EOL;
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        echo AbacService::removePolicyFromDump($this->dump) ? '- OK: removed' : '- Error: not removed';
        echo PHP_EOL;
    }
}
