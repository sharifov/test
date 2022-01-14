<?php

namespace frontend\controllers;

use src\forms\CompositeFormHelper;
use src\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use src\model\clientChat\componentEvent\entity\search\ClientChatComponentEventSearch;
use src\model\clientChat\componentEvent\form\ComponentEventCreateForm;
use src\model\clientChat\componentEvent\service\ClientChatComponentEventManageService;
use src\repositories\NotFoundException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class ClientChatComponentEventController
 * @package frontend\controllers
 * @property-read ClientChatComponentEventManageService $manageService
 */
class ClientChatComponentEventController extends \frontend\controllers\FController
{
    /**
     * @var ClientChatComponentEventManageService
     */
    private ClientChatComponentEventManageService $manageService;

    public function __construct($id, $module, ClientChatComponentEventManageService $manageService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->manageService = $manageService;
    }

    public function actionIndex()
    {
        $searchModel = new ClientChatComponentEventSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'ComponentEventCreateForm',
            ['componentRules' => 'ClientChatComponentRule']
        );
        $form = new ComponentEventCreateForm(count($data['post']['ClientChatComponentRule']));

        $form->load($data['post']);
        if (Yii::$app->request->isPost && !$form->pjaxReload && $form->validate()) {
            $componentEventId = $this->manageService->createWithRules($form);
            return $this->redirect(['view', 'id' => $componentEventId]);
        }

        if ($form->pjaxReload) {
            $form->componentEventSetDefaultConfig();
            $form->componentRulesSetDefaultConfig();
        }

        $form->pjaxReload = 0;

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    public function actionView()
    {
        $id = Yii::$app->request->get('id');

        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model
        ]);
    }

    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');

        $model = $this->findModel($id);

        if (Yii::$app->request->isGet) {
            $data[$model->formName()] = $model->attributes;

            $componentRules = $model->componentRules;
            foreach ($componentRules as $componentRule) {
                $data[$componentRule->formName()][] = $componentRule->attributes;
            }

            $form = new ComponentEventCreateForm(count($data['ClientChatComponentRule'] ?? []));
            $form->load($data);

            return $this->render('update', [
                'model' => $form
            ]);
        }

        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'ComponentEventCreateForm',
            ['componentRules' => 'ClientChatComponentRule']
        );
        $form = new ComponentEventCreateForm(count($data['post']['ClientChatComponentRule']), $model);

        if (Yii::$app->request->isPost && $form->load($data['post']) && !$form->pjaxReload && $form->validate()) {
            try {
                $this->manageService->updateWithRules($model, $form);
                return $this->redirect(['view', 'id' => $model->ccce_id]);
            } catch (\RuntimeException $e) {
                $form->addError('general', $e->getMessage());
            }
        }

        if ($form->pjaxReload) {
            $form->componentEventSetDefaultConfig();
            $form->componentRulesSetDefaultConfig();
        }

        $form->pjaxReload = 0;

        return $this->render('update', [
            'model' => $form
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    private function findModel($id): ClientChatComponentEvent
    {
        if ($model = ClientChatComponentEvent::findOne($id)) {
            return $model;
        }
        throw new NotFoundException();
    }
}
