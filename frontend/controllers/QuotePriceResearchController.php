<?php

namespace frontend\controllers;

use common\models\Lead;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use src\forms\lead\ItineraryEditForm;
use src\forms\siteSetting\PriceResearchLinkForm;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\priceResearchLink\service\PriceResearchLinkService;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class QuotePriceResearchController extends FController
{
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'allowActions' => [
                    'open-price-research-link',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionOpenPriceResearchLink(int $leadId, string $researchLinkKey)
    {
        try {
            $lead = Lead::findOne(['id' => $leadId]);
            if (empty($lead)) {
                throw new NotFoundHttpException('Lead not found');
            }
            $leadAbacDto = new LeadAbacDto($lead, Auth::id());
            /** @abac $leadAbacDto, LeadAbacObject::ACT_PRICE_LINK_RESEARCH, LeadAbacObject::ACTION_ACCESS, Access to edit price research links  */
            $canAccessPriceResearchLinks = \Yii::$app->abac->can(
                $leadAbacDto,
                LeadAbacObject::ACT_PRICE_LINK_RESEARCH,
                LeadAbacObject::ACTION_ACCESS
            );
            if (!$canAccessPriceResearchLinks) {
                throw new BadRequestHttpException('Access denied');
            }
            $linkData =  SettingHelper::getPriceResearchLinkByKey($researchLinkKey);
            $linkForm = new PriceResearchLinkForm();
            $linkForm->load($linkData);
            if (!$linkForm->validate()) {
                throw new BadRequestHttpException(implode(', ', $linkForm->getErrorSummary(true)));
            }
            if ($linkForm->enabled == false) {
                throw new BadRequestHttpException('Price research link disabled');
            }
            $itineraryForm = new ItineraryEditForm($lead);
            $priceResearchLinkService = new PriceResearchLinkService($linkForm, $itineraryForm);
            $url = $priceResearchLinkService->generateUrl();
            header("Referrer-Policy: no-referrer");
            return $this->redirect($url);
        } catch (BadRequestHttpException | NotFoundHttpException $e) {
            throw $e;
        } catch (\RuntimeException | \DomainException $e) {
            \Yii::warning(AppHelper::throwableFormatter($e), 'QuoteController::actionOpenPriceResearchLink:exception');
            return $this->render('_error', [
                'error' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e), 'QuoteController:actionOpenPriceResearchLink:Throwable');
            return $this->render('_error', [
                'error' => 'Server Error',
            ]);
        }
    }
}
