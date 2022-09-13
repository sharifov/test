<?php

namespace modules\quoteAward\src\entities;

use yii\helpers\ArrayHelper;

class QuoteFlightProgramQuery
{
    public static function getFirstProgramPpm()
    {
        return QuoteFlightProgram::find()->select('gfp_ppm')->orderBy('gfp_id ASC')->limit(1)->scalar();
    }

    public static function getListWithPpm(): array
    {
        return ArrayHelper::map(QuoteFlightProgram::find()->orderBy('gfp_id ASC')->asArray()->all(), 'gfp_id', function ($item) {
            return ['data-ppm' => $item['gfp_ppm']];
        });
    }
}
