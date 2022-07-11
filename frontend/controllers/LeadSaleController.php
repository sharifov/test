<?php

namespace frontend\controllers;

use modules\featureFlag\FFlag;
use modules\lead\src\abac\sale\LeadSaleAbacDto;
use modules\lead\src\abac\sale\LeadSaleAbacObject;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\repositories\lead\LeadRepository;
use src\services\cases\LeadSaleService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class LeadSaleController extends FController
{
    private LeadSaleService $leadSaleService;
    private LeadRepository $leadRepository;

    public function __construct(
        $id,
        $module,
        LeadSaleService $leadSaleService,
        LeadRepository $leadRepository,
        $config = []
    ) {
        $this->leadSaleService = $leadSaleService;
        $this->leadRepository = $leadRepository;
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'ajax-sale-detail',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @param $leadId
     * @return string
     */
    public function actionAjaxSaleDetail($leadId): string
    {
        $viewData = [];
        try {
            /** @fflag FFlag::FF_KEY_SALE_VIEW_IN_LEAD_ENABLE, LeadSale view enable\disable */
            if (!Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_SALE_VIEW_IN_LEAD_ENABLE)) {
                throw new BadRequestHttpException('Feature Flag ' . FFlag::FF_KEY_SALE_VIEW_IN_LEAD_ENABLE . ' disable');
            }

            $lead = $this->leadRepository->findByIdAndNotEmptyBoFlightId($leadId);

            $leadSaleAbacDto = new LeadSaleAbacDto($lead, Auth::id());
            /** @abac new $leadSaleAbacDto, LeadSaleAbacObject::LOGIC_CLIENT_DATA, LeadSaleAbacObject::ACTION_VIEW, Access to view sale in lead view */
            if (!Yii::$app->abac->can($leadSaleAbacDto, LeadSaleAbacObject::NS, LeadSaleAbacObject::ACTION_VIEW)) {
                throw new ForbiddenHttpException('Access denied.');
            }

            $viewData['sale'] = $this->leadSaleService->getSaleByBoFlightId($lead->bo_flight_id);
        } catch (\DomainException | \RuntimeException $e) {
            $viewData['errorMessage'] = $e->getMessage();
            $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['lead_id' => $leadId]);
            Yii::warning($message, 'LeadSaleController::actionAjaxView::Exception');
        } catch (\Throwable $throwable) {
            $viewData['errorMessage'] = $throwable->getMessage();
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['lead_id' => $leadId]);
            Yii::error($message, 'LeadSaleController::actionAjaxView::Throwable');
        }

        return $this->renderAjax('partial/_sale_detail', $viewData);
    }
}
