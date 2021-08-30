<?php

namespace webapi\src\forms\cases;

use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use borales\extensions\phoneInput\PhoneInputValidator;
use sales\services\client\InternalPhoneValidator;
use webapi\src\request\BoWebhook;
use yii\base\Model;
use common\components\validators\IsArrayValidator;

/**
 * Class CaseApiResponse
 * @package sales\entities\cases\Cases
 *
 * @property string status_name
 */
class CaseApiResponse extends Cases
{
    public string $status_name = '';

    public function rules(): array
    {
        return [['status_name'], 'string'];
    }

//    public function setStatusName($status_name): string
//    {
//        $this->status_name = 'as'; #CasesStatus::getName($this->cs_status);
//    }
//
//    public function getStatusName(): string
//    {
//        return $this->status_name;
//    }
}
