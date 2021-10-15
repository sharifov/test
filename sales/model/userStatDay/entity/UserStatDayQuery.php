<?php

namespace sales\model\userStatDay\entity;

class UserStatDayQuery
{
    public static function getGrossProfitQuery(): Scopes
    {
        return UserStatDay::find()
        ->alias('usd')
        ->addSelect('sum(usd_value)')
        ->where('usd_user_id = employees.id')
        ->grossProfit();
    }
}
