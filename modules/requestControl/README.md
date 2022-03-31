# REQUEST CONTROL

## Description

Module should give an access to view user activities in system and provide logic for configure them.

## DATABASE & MIGRATION

Tables that need for module works

### Rules registry

Table name: `request_control_rules`

Fields:
* `id` - primary key
* `type` - string field with type of rule (size: 50)
* `subject` - string value that indicates checking subject (size: 255). This field check to whom the rule applies. If type is `ROLE` (as example) a checking value will be users role, if `IP` - his IP and etc.
* `local` - integer value that indicates the available request count to current resource per period.
* `global` - integer value that indicates the available request count to system per period.

> Actually period locates in `settings` table, so this module will not try to connect another tables or modules. Period should be received as argument or property.

### CRUD

The CRUD logic uses Yii2 agreements and practices likewise [GII](https://www.yiiframework.com/doc/guide/2.0/en/start-gii)

### Checking logic

1. Find all rules from `request_control_rules`.

The result should looks like:

| id  | type      | subject  | local  | global  |
|-----|-----------|----------|--------|---------|
| 1   | ROLE      | agent    | 10     | 30      |
| 2   | USERNAME  | johndoe  | 20     | 15      |

2. Get lower values from rules. In this case we have: 10 local request (max) & 15 global request (max);

3. Check the list of exist user requests.
- If rule not exist - allow access;
- If rule is exist - check both value. Check have to work as successively, firstly we have to check local value. If local request count is not exceed available count - check global request count.