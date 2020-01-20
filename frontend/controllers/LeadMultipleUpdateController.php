<?php

namespace frontend\controllers;

use frontend\widgets\multipleUpdate\lead\MultipleUpdateForm;
use frontend\widgets\multipleUpdate\lead\MultipleUpdateService;
use sales\auth\Auth;
use Yii;
use yii\bootstrap4\ActiveForm;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class LeadMultipleUpdateController
 *
 * @property MultipleUpdateService $service
 */
class LeadMultipleUpdateController extends Controller
{
    private $service;

    public function __construct($id, $module, MultipleUpdateService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionShow(): string
    {
        try {
            return $this->renderAjax('_show', [
                'validationUrl' => ['/lead-multiple-update/validation'],
                'action' => ['/lead-multiple-update/update'],
                'modalId' => 'modal-df',
                'ids' => Yii::$app->request->post('ids'),
                'pjaxId' => 'lead-pjax-list',
                'user' => Auth::user()
            ]);
        } catch (\DomainException $e) {
            return $this->renderAjax('_error', [
                'error' => $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            Yii::error($e, 'LeadMultipleUpdateController:actionShow');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionValidation(): array
    {
        $user = Auth::user();

        $form = new MultipleUpdateForm($user);
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionUpdate(): Response
    {
        $user = Auth::user();

        $form = new MultipleUpdateForm($user);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $report = $this->service->update($form);
            return $this->asJson([
                'success' => true,
                'message' => count($report) . ' rows affected.',
                'text' => $this->service->formatReport($report),
            ]);
        }
        throw new BadRequestHttpException();
    }
}
