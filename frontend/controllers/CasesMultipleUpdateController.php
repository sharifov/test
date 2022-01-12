<?php

namespace frontend\controllers;

use frontend\widgets\multipleUpdate\cases\MultipleUpdateService;
use src\auth\Auth;
use Yii;
use common\models\Employee;
use frontend\widgets\multipleUpdate\cases\MultipleUpdateForm;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class CasesMultipleUpdateController
 *
 * @property MultipleUpdateService $service
 */
class CasesMultipleUpdateController extends FController
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
                'validationUrl' => ['/cases-multiple-update/validation'],
                'action' => ['/cases-multiple-update/update'],
                'modalId' => 'modal-df',
                'ids' => Yii::$app->request->post('ids'),
                'pjaxId' => 'cases-pjax-list',
                'user' => Auth::user(),
            ]);
        } catch (\DomainException $e) {
            return $this->renderAjax('_error', [
                'error' => $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            Yii::error($e, 'CasesMultipleUpdateController:actionShow');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionValidation(): array
    {
        $form = new MultipleUpdateForm(Auth::user());
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
        $form = new MultipleUpdateForm(Auth::user());
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $messages = $this->service->update($form);
            return $this->asJson([
                'success' => true,
                'message' => count($messages) . ' rows affected.',
                'text' => $this->service->formatMessages(...$messages),
            ]);
        }
        throw new BadRequestHttpException();
    }
}
