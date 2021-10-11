<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\service;

use common\models\CaseSale;
use common\models\Client;
use DomainException;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\app\AppHelper;
use sales\services\client\ClientCreateForm;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class VoluntaryExchangeCreateService
 *
 * @property VoluntaryExchangeCreateForm $voluntaryExchangeCreateForm
 * @property VoluntaryExchangeObjectCollection $objectCollection
 */
class VoluntaryExchangeCreateService
{
    private VoluntaryExchangeCreateForm $voluntaryExchangeCreateForm;
    private VoluntaryExchangeObjectCollection $objectCollection;

    /**
     * @param VoluntaryExchangeCreateForm $voluntaryExchangeCreateForm
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     */
    public function __construct(
        VoluntaryExchangeCreateForm $voluntaryExchangeCreateForm,
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
    ) {
        $this->voluntaryExchangeCreateForm = $voluntaryExchangeCreateForm;
        $this->objectCollection = $voluntaryExchangeObjectCollection;
    }

    public function createCaseSale(array $saleData, Cases $case): ?CaseSale
    {
        $caseSale = $this->casesSaleService->createSaleByData($case->cs_id, $saleData);
        $case->addEventLog(
            CaseEventLog::RE_PROTECTION_CREATE,
            'Case Sale created by Data',
            ['case_id' => $case->cs_id]
        );
        return $caseSale;
    }

    public function getOrCreateClient(int $projectId, OrderContactForm $orderContactForm): Client
    {
        $clientForm = new ClientCreateForm();
        $clientForm->projectId = $projectId;
        $clientForm->typeCreate = Client::TYPE_CREATE_CASE;
        $clientForm->firstName = $orderContactForm->first_name;
        $clientForm->lastName = $orderContactForm->last_name;

        return $this->objectCollection->getClientManageService()->getOrCreate(
            [new PhoneCreateForm(['phone' => $orderContactForm->phone_number])],
            [new EmailCreateForm(['email' => $orderContactForm->email])],
            $clientForm
        );
    }

    public static function writeLog(Throwable $throwable, array $data = [], string $category = 'VoluntaryExchangeCreateJob:throwable'): void
    {
        $message = AppHelper::throwableLog($throwable);
        if ($data) {
            $message = ArrayHelper::merge($message, $data);
        }
        if ($throwable instanceof DomainException) {
            Yii::warning($message, $category);
        } else {
            Yii::error($message, $category);
        }
    }
}
