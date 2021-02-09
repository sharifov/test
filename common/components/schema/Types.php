<?php

namespace common\components\schema;

use GraphQL\Type\Definition\ObjectType;

/**
 * Class Types
 * @package common\components\schema
 */
class Types extends ObjectType
{
    private static $query;
    // private static $mutation;

    private static $user;
    private static $call;
    private static $project;
    private static $department;
    private static $array;
    private static $userOnline;
    private static $userStatus;
    private static $userParams;

    public static function query()
    {
        return self::$query ?: (self::$query = new QueryType());
    }


    public static function user(): UserType
    {
        return self::$user ?: (self::$user = new UserType());
    }

    public static function call()
    {
        return self::$call ?: (self::$call = new CallType());
    }

    public static function project()
    {
        return self::$project ?: (self::$project = new ProjectType());
    }

    public static function department()
    {
        return self::$department ?: (self::$department = new DepartmentType());
    }

    public static function array()
    {
        return self::$array ?: (self::$array = new ArrayType());
    }

    public static function userOnline()
    {
        return self::$userOnline ?: (self::$userOnline = new UserOnlineType());
    }

    public static function userStatus()
    {
        return self::$userStatus ?: (self::$userStatus = new UserStatusType());
    }

    public static function userParams()
    {
        return self::$userParams ?: (self::$userParams = new UserParamsType());
    }
}
