<?php

namespace modules\quoteAward\src\dictionary;

class AwardProgramDictionary
{
    public const REVENUE = 'revenue';
    public const AWARD_MILE = 'award_mile';

    //  public const UPGRADE = 'upgrade';

    public static function geList(): array
    {
        return [
            self::REVENUE => 'Revenue',
            self::AWARD_MILE => 'Awards miles',
            //     self::UPGRADE => 'Upgrade',
        ];
    }
}
