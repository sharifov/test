<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220908_083502_add_abac_permission_for_delete_note
 */
class m220908_083502_add_abac_permission_for_delete_note extends Migration
{
    /**
     * task-list/task-list/obj/user_task - (removeNote)
     */
    private const DUMP = 'eyJhcF9pZCI6MzMyLCJhcF9ydWxlX3R5cGUiOiJwIiwiYXBfc3ViamVjdCI6IihyLnN1Yi5pc1VzZXJUYXNrT3duZXIgPT0gdHJ1ZSkgfHwgKFwiYWRtaW5cIiBpbiByLnN1Yi5lbnYudXNlci5yb2xlcykiLCJhcF9zdWJqZWN0X2pzb24iOiJ7XCJjb25kaXRpb25cIjpcIk9SXCIsXCJydWxlc1wiOlt7XCJpZFwiOlwidGFzay1saXN0L3Rhc2stbGlzdC9pc1VzZXJUYXNrT3duZXJcIixcImZpZWxkXCI6XCJpc1VzZXJUYXNrT3duZXJcIixcInR5cGVcIjpcImJvb2xlYW5cIixcImlucHV0XCI6XCJyYWRpb1wiLFwib3BlcmF0b3JcIjpcIj09XCIsXCJ2YWx1ZVwiOnRydWV9LHtcImlkXCI6XCJlbnZfdXNlcl9yb2xlc1wiLFwiZmllbGRcIjpcImVudi51c2VyLnJvbGVzXCIsXCJ0eXBlXCI6XCJzdHJpbmdcIixcImlucHV0XCI6XCJzZWxlY3RcIixcIm9wZXJhdG9yXCI6XCJpbl9hcnJheVwiLFwidmFsdWVcIjpcImFkbWluXCJ9XSxcIm5vdFwiOmZhbHNlLFwidmFsaWRcIjp0cnVlfSIsImFwX29iamVjdCI6InRhc2stbGlzdC90YXNrLWxpc3Qvb2JqL3VzZXJfdGFzayIsImFwX2FjdGlvbiI6IihyZW1vdmVOb3RlKSIsImFwX2FjdGlvbl9qc29uIjoiW1wicmVtb3ZlTm90ZVwiXSIsImFwX2VmZmVjdCI6MSwiYXBfdGl0bGUiOiJBY2Nlc3MgdG8gZGVsZXRlIFVzZXJUYXNrIE5vdGUiLCJhcF9zb3J0X29yZGVyIjo1MCwiYXBfY3JlYXRlZF9kdCI6IjIwMjItMDktMDcgMDc6Mzc6MzEiLCJhcF91cGRhdGVkX2R0IjoiMjAyMi0wOS0wNyAwODo0ODo0OCIsImFwX2NyZWF0ZWRfdXNlcl9pZCI6bnVsbCwiYXBfdXBkYXRlZF91c2VyX2lkIjozMDIsImFwX2VuYWJsZWQiOjEsImFwX2hhc2hfY29kZSI6IjEwNDUwYzEzODEifQ==';

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
