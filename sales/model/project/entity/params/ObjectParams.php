<?php

namespace sales\model\project\entity\params;

/**
 * Class ObjectParams
 *
 * @property CaseParams $case
 */
class ObjectParams
{
    public CaseParams $case;

    public function __construct(array $params)
    {
        $this->case = new CaseParams($params['case'] ?? self::default()['case']);
    }

    public static function default(): array
    {
        return [
            'case' => CaseParams::default(),
        ];
    }
}
