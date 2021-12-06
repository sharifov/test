<?php

namespace frontend\controllers;

use sales\auth\Auth;
use sales\model\emailReviewQueue\entity\EmailReviewQueueSearch;
use yii\helpers\ArrayHelper;

class EmailReviewQueueController extends FController
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'index',
                ],
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionIndex(): string
    {
        $search = new EmailReviewQueueSearch();
        $dataProvider = $search->reviewQueue($this->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $search,
            'dataProvider' => $dataProvider
        ]);
    }
}
