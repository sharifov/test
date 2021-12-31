<?php

namespace frontend\controllers;

use sales\auth\Auth;
use sales\model\user\entity\userStats\UserStatsSearch;
use sales\model\user\reports\stats\Access;
use sales\model\user\reports\stats\SessionFilterStorage;
use sales\model\user\reports\stats\UserStatsReport;
use sales\model\userModelSetting\service\UserModelSettingService;
use Yii;

/**
 * Class UserStatsController
 */
class UserStatsController extends FController
{
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

    public function actionReport()
    {
        $user = Auth::user();

        $searchModel = new UserStatsReport(
            \Yii::$app->user->identity->timezone,
            date('Y-m') . '-01 00:00 - ' . date('Y-m-d') . ' 23:59',
            (new Access($user))
        );

        $savedFilters = [];
        $filterStorage = new SessionFilterStorage();

        $needResetFilters = (bool)Yii::$app->request->get('reset');

        if ($needResetFilters) {
            $filterStorage->remove($user->id, UserStatsReport::class);
        } else {
            $savedFilters = $filterStorage->find($user->id, UserStatsReport::class);
        }

        $dataProvider = $searchModel->search(array_merge($savedFilters, $needResetFilters ? [] : Yii::$app->request->queryParams));

        if ($searchModel->isValid) {
            $filterStorage->add($user->id, UserStatsReport::class, $searchModel->getFilters());
        }

        return $this->render('report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'showReport' => $searchModel->isValid && !$needResetFilters && Yii::$app->request->queryParams,
        ]);
    }
}
