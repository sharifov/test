<?php

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220606_081735_add_abac_policy_test
 */
class m220606_081735_add_abac_policy_test extends Migration
{
    public $dump = 'eyJhcF9pZCI6MzQzLCJhcF9ydWxlX3R5cGUiOiJwIiwiYXBfc3ViamVjdCI6IihyLnN1Yi5lbnYuZHQueWVhciA9PSAyMDIxKSAmJiAoci5zdWIuZW52LmR0Lm1vbnRoID09IDEpICYmIChcImFkbWluXCIgaW4gci5zdWIuZW52LnVzZXIucm9sZXMpIiwiYXBfc3ViamVjdF9qc29uIjoie1wiY29uZGl0aW9uXCI6XCJBTkRcIixcInJ1bGVzXCI6W3tcImlkXCI6XCJlbnZfZHRfeWVhclwiLFwiZmllbGRcIjpcImVudi5kdC55ZWFyXCIsXCJ0eXBlXCI6XCJpbnRlZ2VyXCIsXCJpbnB1dFwiOlwibnVtYmVyXCIsXCJvcGVyYXRvclwiOlwiPT1cIixcInZhbHVlXCI6MjAyMX0se1wiaWRcIjpcImVudl9kdF9tb250aFwiLFwiZmllbGRcIjpcImVudi5kdC5tb250aFwiLFwidHlwZVwiOlwiaW50ZWdlclwiLFwiaW5wdXRcIjpcInNlbGVjdFwiLFwib3BlcmF0b3JcIjpcIj09XCIsXCJ2YWx1ZVwiOjF9LHtcImlkXCI6XCJlbnZfdXNlcl9yb2xlc1wiLFwiZmllbGRcIjpcImVudi51c2VyLnJvbGVzXCIsXCJ0eXBlXCI6XCJzdHJpbmdcIixcImlucHV0XCI6XCJzZWxlY3RcIixcIm9wZXJhdG9yXCI6XCJpbl9hcnJheVwiLFwidmFsdWVcIjpcImFkbWluXCJ9XSxcInZhbGlkXCI6dHJ1ZX0iLCJhcF9vYmplY3QiOiJvcmRlci9vcmRlci9hY3Qvc3RhdHVzX2xvZyIsImFwX2FjdGlvbiI6IihhY2Nlc3MpIiwiYXBfYWN0aW9uX2pzb24iOiJbXCJhY2Nlc3NcIl0iLCJhcF9lZmZlY3QiOjAsImFwX3RpdGxlIjoiVGVzdCBBYmFjIFBvbGljeSAoRXhhbXBsZSBmb3IgZXhwb3J0L2ltcG9ydCkiLCJhcF9zb3J0X29yZGVyIjoyMCwiYXBfY3JlYXRlZF9kdCI6IjIwMjItMDYtMDYgMDk6NDc6MDEiLCJhcF91cGRhdGVkX2R0IjoiMjAyMi0wNi0wNiAxMDowMDoxNCIsImFwX2NyZWF0ZWRfdXNlcl9pZCI6MTY3LCJhcF91cGRhdGVkX3VzZXJfaWQiOjE2NywiYXBfZW5hYmxlZCI6MCwiYXBfaGFzaF9jb2RlIjoiNGFmZGYyNTdlMyJ9';

    /**
     * @return false|mixed|void
     */
    public function safeUp()
    {
        echo AbacService::importPolicyFromDump($this->dump, false) ? '- OK: added' : '- Error: not added';
        echo PHP_EOL;
    }


    /**
     * @return false|mixed|void
     */
    public function safeDown()
    {
        echo AbacService::removePolicyFromDump($this->dump) ? '- OK: removed' : '- Error: not removed';
        echo PHP_EOL;
    }
}
