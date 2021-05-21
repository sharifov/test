<?php

namespace frontend\controllers;

use sales\auth\Auth;
use sales\model\user\entity\userStats\UserStatsSearch;
use sales\model\userModelSetting\service\UserModelSettingService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

/**
 * Class UserStatsController
 */
class UserStatsController extends FController
{
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function actionIndex()
    {
        $defaultFields = UserModelSettingService::getFields(Auth::id(), UserStatsSearch::class);
        $searchModel = new UserStatsSearch(Auth::user(), $defaultFields);
        $dataProvider = $searchModel->searchByUser(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
