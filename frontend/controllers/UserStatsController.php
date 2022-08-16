<?php

namespace frontend\controllers;

use src\auth\Auth;
use src\model\user\entity\sales\SalesSearch;
use src\model\user\entity\userStats\UserStatsSearch;
use src\model\user\reports\stats\Access;
use src\model\user\reports\stats\SessionFilterStorage;
use src\model\user\reports\stats\UserStatsReport;
use src\model\userModelSetting\service\UserModelSettingService;
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
        $timeZone = \Yii::$app->user->identity->timezone;
        /** @fflag FFlag::FF_KEY_CONVERSION_BY_TIMEZONE, Conversion Filter by Timezone */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_CONVERSION_BY_TIMEZONE)) {
            $timeZone = SalesSearch::DEFAULT_TIMEZONE;
        }

        $searchModel = new UserStatsReport(
            $timeZone,
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
        $summaryStats = $searchModel->getSummaryStats();

        if ($searchModel->isValid) {
            $filterStorage->add($user->id, UserStatsReport::class, $searchModel->getFilters());
        }

        return $this->render('report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'showReport' => $searchModel->isValid && !$needResetFilters && Yii::$app->request->queryParams,
            'summaryStats' => $summaryStats
        ]);
    }
}
