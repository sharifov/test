<?php

namespace src\model\coupon\useCase\send;

use common\components\CommunicationService;
use common\models\DepartmentEmailProject;
use common\models\Employee;
use common\models\UserProjectParams;
use src\entities\cases\Cases;
use src\model\coupon\entity\coupon\Coupon;
use src\services\cases\CasesCommunicationService;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class SendCouponsService
 * @package src\model\coupon\useCase\send
 *
 * @property CasesCommunicationService $casesCommunicationService
 */
class SendCouponsService
{
    /**
     * @var CasesCommunicationService
     */
    private $casesCommunicationService;

    private $removableIndexes = [
        'c_id',
        'c_created_dt',
        'c_updated_dt',
        'c_created_user_id',
        'c_updated_user_id',
    ];

    public function __construct(CasesCommunicationService $casesCommunicationService)
    {
        $this->casesCommunicationService = $casesCommunicationService;
    }

    public function preview(SendCouponsForm $form, Cases $case, Employee $user): array
    {
        $emailData = $this->casesCommunicationService->getEmailData($case, $user);

        $emailData['coupons'] = $this->getEmailData($form->couponIds);

        /** @var CommunicationService $communication */
        $communication = Yii::$app->comms;

        $emailFrom = $this->getEmailFrom($case, $user);

        return $communication->mailPreview($case->cs_project_id, $form->emailTemplateType, $emailFrom, $form->emailTo, $emailData);
    }

    private function getEmailData(array $couponsIds): array
    {
        $coupons = Coupon::find()->byCouponIds($couponsIds)->asArray()->all();

        $result = [];
        foreach ($coupons as $coupon) {
            $result[] = $this->removeIndexes($coupon);
        }
        return $result;
    }

    private function removeIndexes(array $data): array
    {
        foreach ($data as $key => $item) {
            if (in_array($key, $this->removableIndexes, false)) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    private function getEmailFrom(Cases $case, Employee $user)
    {
        $emailFrom = $user->email;

        if ($case->cs_project_id) {
            $upp = UserProjectParams::find()->where(['upp_project_id' => $case->cs_project_id, 'upp_user_id' => $user->id])->withEmailList()->one();
            if ($upp) {
                $emailFrom = $upp->getEmail() ?: $emailFrom;
            }
        }

        if (!$emailFrom) {
            throw new \RuntimeException('Agent not has assigned email');
        }

        return $emailFrom;
    }
}
