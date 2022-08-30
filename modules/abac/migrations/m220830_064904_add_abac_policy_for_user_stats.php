<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220830_064904_add_abac_policy_for_user_stats
 */
class m220830_064904_add_abac_policy_for_user_stats extends Migration
{
    public string $dump = 'eyJhcF9pZCI6Mzk5LCJhcF9ydWxlX3R5cGUiOiJwIiwiYXBfc3ViamVjdCI6IihcImFkbWluXCIgaW4gci5zdWIuZW52LnVzZXIucm9sZXMpIiwiYXBfc3ViamVjdF9qc29uIjoie1wiY29uZGl0aW9uXCI6XCJBTkRcIixcInJ1bGVzXCI6W3tcImlkXCI6XCJlbnZfdXNlcl9yb2xlc1wiLFwiZmllbGRcIjpcImVudi51c2VyLnJvbGVzXCIsXCJ0eXBlXCI6XCJzdHJpbmdcIixcImlucHV0XCI6XCJzZWxlY3RcIixcIm9wZXJhdG9yXCI6XCJpbl9hcnJheVwiLFwidmFsdWVcIjpcImFkbWluXCJ9XSxcIm5vdFwiOmZhbHNlLFwidmFsaWRcIjp0cnVlfSIsImFwX29iamVjdCI6InVzZXItc3RhdHMvdXNlci1zdGF0cy9vYmovdXNlci1zdGF0cyIsImFwX2FjdGlvbiI6IihhY2Nlc3MpIiwiYXBfYWN0aW9uX2pzb24iOiJbXCJhY2Nlc3NcIl0iLCJhcF9lZmZlY3QiOjEsImFwX3RpdGxlIjoiQWNjZXNzIHRvIFVzZXIgU3RhdHMgYnkgTGVhZCBmcm9tIHJlcG9ydCIsImFwX3NvcnRfb3JkZXIiOjUwLCJhcF9jcmVhdGVkX2R0IjoiMjAyMi0wOC0yOSAxMDowODo0MyIsImFwX3VwZGF0ZWRfZHQiOiIyMDIyLTA4LTMwIDA2OjQ5OjU5IiwiYXBfY3JlYXRlZF91c2VyX2lkIjpudWxsLCJhcF91cGRhdGVkX3VzZXJfaWQiOjMwMiwiYXBfZW5hYmxlZCI6MSwiYXBfaGFzaF9jb2RlIjoiOWYwY2JiZjI5MSJ9';

    /**
     * @return void
     */
    public function safeUp()
    {
        if (AbacPolicy::deleteAll(['ap_object' => 'user-stats/user-stats/obj/user-stats', 'ap_subject' => '("admin" in r.sub.env.user.roles)'])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }

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
