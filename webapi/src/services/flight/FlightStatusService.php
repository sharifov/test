<?php

namespace webapi\src\services\flight;

use common\components\jobs\CreateSaleFromBOJob;
use src\entities\cases\CaseCategory;
use src\entities\cases\CaseCategoryKeyDictionary;
use src\entities\cases\Cases;
use src\helpers\app\AppHelper;
use src\interfaces\BoWebhookService;
use src\model\cases\useCases\cases\api\create\CreateForm;
use src\model\cases\useCases\cases\api\create\Handler;
use src\repositories\cases\CasesRepository;
use webapi\src\forms\boWebhook\FlightStatusChangeForm;
use Yii;
use yii\base\Model;

class FlightStatusService implements BoWebhookService
{
    private CasesRepository $caseRepository;
    private Handler $createCaseHandler;

    public function __construct(CasesRepository $caseRepository, Handler $createHandler)
    {
        $this->caseRepository = $caseRepository;
        $this->createCaseHandler = $createHandler;
    }

    /**
     * @param FlightStatusChangeForm $form
     */
    public function processRequest(Model $form): void
    {
        /** @fflag FFlag::FF_KEY_CROSS_SALE_QUEUE_ENABLE, Cross Sale Queue enable */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_CROSS_SALE_QUEUE_ENABLE) === false) {
            return;
        }

        if ($form->isStatusClose() && $form->hasLead() === false) {
            $caseExists = Cases::find()
                ->where([
                    'cs_order_uid' => $form->order_uid,
                    'cs_category_id' => CaseCategory::getIdByKey(CaseCategoryKeyDictionary::CROSS_SALE)
                ])
                ->exists();

            if ($caseExists) {
                return;
            }

            if ($form->order_uid || $form->client_email || $form->client_phone) {
                $createCaseForm = new CreateForm(null);
                $createCaseForm->project_key = $form->project_key;
                $createCaseForm->contact_email = $form->client_email;
                $createCaseForm->contact_phone = $form->client_phone;
                $createCaseForm->order_uid = $form->order_uid;
                $createCaseForm->category_key = CaseCategoryKeyDictionary::CROSS_SALE;
                $createCaseForm->order_info = [];

                try {
                    $caseCategory = $createCaseForm->getCaseCategory();
                    $result = $this->createCaseHandler->handle($createCaseForm->getDto(), $caseCategory);

                    $job = new CreateSaleFromBOJob();
                    $job->case_id = $result->csId;
                    $job->order_uid = $createCaseForm->order_uid;
                    $job->email = $createCaseForm->contact_email;
                    $job->phone = $createCaseForm->contact_phone;
                    $job->project_key = $createCaseForm->project_key ?? null;

                    Yii::$app->queue_job->priority(100)->push($job);
                } catch (\Throwable $throwable) {
                    Yii::error(
                        AppHelper::throwableLog($throwable),
                        'FlightStatusService:processRequest'
                    );
                }
            }
        }
    }
}
