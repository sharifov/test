<?php

namespace webapi\src\services\flight;

use common\components\jobs\CreateSaleFromBOJob;
use src\entities\cases\CaseCategory;
use src\entities\cases\CaseCategoryKeyDictionary;
use src\entities\cases\Cases;
use src\helpers\app\AppHelper;
use src\helpers\DateHelper;
use src\interfaces\BoWebhookService;
use src\model\cases\useCases\cases\api\create\CreateForm;
use src\model\cases\useCases\cases\api\create\Handler;
use src\repositories\cases\CasesRepository;
use src\services\cases\CasesSaleService;
use webapi\src\forms\boWebhook\FlightStatusChangeForm;
use Yii;
use yii\base\Model;

class FlightStatusService implements BoWebhookService
{
    private CasesRepository $caseRepository;
    private Handler $createCaseHandler;
    private CasesSaleService $casesSaleService;

    public function __construct(CasesRepository $caseRepository, Handler $createHandler, CasesSaleService $casesSaleService)
    {
        $this->caseRepository = $caseRepository;
        $this->createCaseHandler = $createHandler;
        $this->casesSaleService = $casesSaleService;
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

        $setting = Yii::$app->params['settings']['case_cross_sale_queue'];

        /** @fflag FFlag::FF_KEY_CROSS_SALE_NEW_PARAMETERS_ENABLE, New parameters for cross sale enable */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_CROSS_SALE_NEW_PARAMETERS_ENABLE)) {
            if ($this->parameterIsValid($form->project_key, 'projects', 'excludeProjects', $setting) === false) {
                return;
            }

            if ($this->parameterIsValid($form->flight_cabin, 'cabins', 'excludeCabin', $setting) === false) {
                return;
            }
        } else {
            if (isset($setting['excludeProjects']) && is_array($setting['excludeProjects'])) {
                if (in_array($form->project_key, $setting['excludeProjects'])) {
                    return;
                }
            }

            if (isset($setting['excludeCabin']) && is_array($setting['excludeCabin'])) {
                if (in_array($form->flight_cabin, $setting['excludeCabin'])) {
                    return;
                }
            }
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

            /** @fflag FFlag::FF_KEY_CROSS_SALE_NEW_PARAMETERS_ENABLE, New parameters for cross sale enable */
            if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_CROSS_SALE_NEW_PARAMETERS_ENABLE)) {
                $saleDetail = $this->casesSaleService->detailRequestToBackOffice(
                    $form->flight_id
                );

                if (isset($setting['products']) && is_array($setting['products']) && !empty($setting['products'])) {
                    $keyFound = false;
                    foreach ($setting['products'] as $product) {
                        if (array_key_exists($product, $saleDetail) && !empty($saleDetail[$product])) {
                            $keyFound = true;
                        }
                    }

                    if ($keyFound === false) {
                        return;
                    }
                } elseif (isset($setting['excludeProducts']) && is_array($setting['excludeProducts'])) {
                    foreach ($setting['excludeProducts'] as $excludeProduct) {
                        if (array_key_exists($excludeProduct, $saleDetail) && !empty($saleDetail[$excludeProduct])) {
                            return;
                        }
                    }
                }

                if (isset($setting['rushFlightsHours'])) {
                    $rushFlightHours = (int) $setting['rushFlightsHours'];

                    if (isset($saleDetail['itinerary']) && !empty($saleDetail['itinerary'])) {
                        $departureDate = $saleDetail['itinerary'][0]['segments'][0]['departureTime'] ?? '';

                        if (!empty($departureDate)) {
                            $hours = DateHelper::getDifferentInHoursByDatesUTC(
                                date('Y-m-d H:i:s'),
                                $departureDate
                            );

                            if ($hours < $rushFlightHours) {
                                return;
                            }
                        }
                    }
                }
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

                    /** @fflag FFlag::FF_KEY_CROSS_SALE_NEW_PARAMETERS_ENABLE, New parameters for cross sale enable */
                    if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_CROSS_SALE_NEW_PARAMETERS_ENABLE) && isset($saleDetail)) {
                        $this->casesSaleService->createSaleByData(
                            $result->csId,
                            $saleDetail
                        );
                    } else {
                        $job = new CreateSaleFromBOJob();
                        $job->case_id = $result->csId;
                        $job->order_uid = $createCaseForm->order_uid;
                        $job->email = $createCaseForm->contact_email;
                        $job->phone = $createCaseForm->contact_phone;
                        $job->project_key = $createCaseForm->project_key ?? null;

                        Yii::$app->queue_job->priority(100)->push($job);
                    }
                } catch (\Throwable $throwable) {
                    Yii::error(
                        AppHelper::throwableLog($throwable),
                        'FlightStatusService:processRequest'
                    );
                }
            }
        }
    }

    private function parameterIsValid(string $value, string $include, string $exclude, array $setting): bool
    {
        if (isset($setting[$include]) && is_array($setting[$include]) && !empty($setting[$include])) {
            if (in_array($value, $setting[$include]) === false) {
                return false;
            }
        } elseif (isset($setting[$exclude]) && is_array($setting[$exclude])) {
            if (in_array($value, $setting[$exclude])) {
                return false;
            }
        }

        return true;
    }
}
