<?php

namespace sales\model\leadRedial\assign;

/**
 * Class SortUsers
 *
 * @property array $value
 */
class SortUsers
{
    private const DEFAULT_ORDER = [
        'up_call_user_level' => SORT_DESC,
        'gross_profit' => SORT_DESC,
        'us_phone_ready_time' => SORT_ASC
    ];

    private const MAP = [
        'priority_level' => 'up_call_user_level',
        'gross_profit' => 'gross_profit',
        'phone_ready_time' => 'us_phone_ready_time'
    ];

    private const SORT_ORDER = [
        'ASC' => SORT_ASC,
        'DESC' => SORT_DESC,
    ];

    private array $value = [];

    public function __construct()
    {
        $sort = \Yii::$app->params['settings']['lead_redial_sort_users'] ?? null;

        if (!$sort) {
            \Yii::error([
                'message' => 'Lead redial sort settings is empty',
                'settings' => 'lead_redial_sort_users',
            ], 'leadRedial:assign:SortUsers');
            $this->value = self::DEFAULT_ORDER;
            return;
        }

        if (!is_array($sort)) {
            \Yii::error([
                'message' => 'Lead redial sort settings error. Setting is not array',
                'settings' => 'lead_redial_sort_users',
            ], 'leadRedial:assign:SortUsers');
            $this->value = self::DEFAULT_ORDER;
            return;
        }

        foreach ($sort as $key => $value) {
            if (array_key_exists($key, self::MAP) && array_key_exists($value, self::SORT_ORDER)) {
                $this->value[self::MAP[$key]] = self::SORT_ORDER[$value];
            } else {
                \Yii::error([
                    'message' => 'Lead redial sort settings error. Not allowed key or value',
                    'settings' => 'lead_redial_sort_users',
                ], 'leadRedial:assign:SortUsers');
            }
        }
    }

    public function getValue()
    {
        return $this->value;
    }
}
