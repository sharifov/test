# REQUEST CONTROL

## Description

Module should give an access to view user activities in system and provide logic for configure them.

## INSTALL

1. Fork module catalog into `/modules/` folder.
2. Include module configuration into your config file. `/config/main.php` as example.
```
...
'product' => [
    'class' => ProductModule::class
],
...
```
3. Exec migration `console/migrations/archive/2019/m190801_060710_create_tbl_user_site_activity.php` (this migration is legacy).
4. Run `yii migrate --migrationPath=@modules/requestController/migrations`.

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

## Usage

Main classes that involved in access checking is:
1. Main module class `modules\requestControl\RequestControlerModule`. This class implements api for access checking (`can/1`).
2. Class with admission pass `modules\requestControl\accessCheck\AdmissionPass`. 
* Constructor expects period size (seconds) as argument. If you wanna check access bearing in mind last 1 hour - you should pass 3600; 
* This class contain required specifications for access checking. Instance of this class is expected argument for `can/1` method of `modules\requestControl\RequestControlerModule`;

Example:

```
...
$checkAccess = new AdmissionPass(600); // Creating the checking entity for 600sec limit
$checkAccess
    ->addConditionByType(UsernameCondition::TYPE, "johndoe")        // Add checking condition by username 
    ->addConditionByType(RoleCondition::TYPE, ["admin", "agent"])   // Add checking condition by role
    
if (\Yii::$app->getModule('requestControl')->can($checkAccess) === true) {
    // ...code, if access allow
} else {
    // ...code, if access deny
}
...
```

Also, you can include prepared behavior class for request logging and checking the access;

In config:

```
...
return [
    ...,
    'as beforeRequest' => [
        'class' => modules\requestControl\components\Watcher::class,
    ]
    ...
...
```

## Logic extension

At this time module checks access by `USERNAME` and `ROLE` only, but you can extend the checking logic. 

### First way. Adding external checking condition

If you wanna add external checking condition you should create class, that extends `modules\requestControl\accessCheck\conditions\AbstractCondition` class.

As example:

```
<?php
namespace modules\myModule;

use modules\requestControl\accessCheck\conditions\AbstractCondition
use yii\db\Query;

class IPCondition extends AbstractCondition {
    const TYPE = 'IP'; // Make sure that you set unique type name
    
    /**
    * @param Query $query
    * @return Query
    */
    public function modifyQuery(Query $query): Query
    {
        return $query->orWhere(["type" => self::TYPE, "subject" => $this->value]);
    }
}
```

The `modifyQuery` function carried out the logic of result query modifying.
 
What does it mean? When you call `addConditionByType/2` or `addCondition/1` of `modules\requestControl\accessCheck\AdmissionPass` you passing the comparing value too. For `ROLE` it's role name, for `USERNAME` and etc, but some time in future you will need to add some rule that have complex checking logic (using another tables, modules, regular expressions etc), you can implement required logic here.

If you add external condition module - you should use `addCondition/1` only. The `addConditionByType/2` works only with internal conditions.

Usage:

```
use modules\myModule\IPCondition;
...
class MySpecificClass {
    ...
    $checkAccess = new AdmissionPass(600); // Creating the checking entity for 600sec limit
    $checkAccess 
        ->addCondition(new IPCondition(Yii::app()->request->getUserHostAddress()))   // Add checking condition by user's current IP
    ...
}
```

### Second way. Adding internal checking condition.

For adding internal checking condition you should:

Create your condition file in `/modules/requestControl/accessCheck/conditions`. As example:

```
<?php
namespace modules\requestControl\accessCheck\conditions;

use modules\requestControl\accessCheck\conditions\AbstractCondition
use yii\db\Query;

class IPCondition extends AbstractCondition {
    const TYPE = 'IP'; // Make sure that you set unique type name
    
    /**
    * @param Query $query
    * @return Query
    */
    public function modifyQuery(Query $query): Query
    {
        return $query->orWhere(["type" => self::TYPE, "subject" => $this->value]);
    }
}
```

Add new type into `addConditionByType/2` like:

```
...
/**
 * @param string $type
 * @param string|array $value
 * @return AdmissionPass
 */
public function addConditionByType(string $type, $value): self
{
    switch ($type) {
        case UsernameCondition::TYPE:
            return $this->addCondition(new UsernameCondition($value));
        case RoleCondition::TYPE:
            return $this->addCondition(new RoleCondition($value));
        case RoleCondition::TYPE:
            return $this->addCondition(new RoleCondition($value));
        case IPCondition::TYPE:
            return $this->addCondition(new IPCondition($value));
    }
    return $this;
}
...
```

*TODO:* This function should work dynamically, using config file.


In this case usage can be like:

```
use modules\myModule\IPCondition;
...
class MySpecificClass {
    ...
    $checkAccess = new AdmissionPass(600); // Creating the checking entity for 600sec limit
    $checkAccess 
        ->addCondition(IPCondition::TYPE, Yii::app()->request->getUserHostAddress())   // Add checking condition by user's current IP
    ...
}
```

or 

```
use modules\myModule\IPCondition;
...
class MySpecificClass {
    ...
    $checkAccess = new AdmissionPass(600); // Creating the checking entity for 600sec limit
    $checkAccess 
        ->addCondition(new IPCondition(Yii::app()->request->getUserHostAddress()))   // Add checking condition by user's current IP
    ...
}
```