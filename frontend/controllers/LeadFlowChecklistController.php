<?php

namespace frontend\controllers;

use common\models\search\LeadFlowChecklistSearch;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class LeadFlowChecklistController
 */
class LeadFlowChecklistController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['viewLeadFlowChecklist']
                    ]
                ]
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new LeadFlowChecklistSearch();

        $params = Yii::$app->request->queryParams;

        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['LeadFlowSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
