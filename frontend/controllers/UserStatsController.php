<?php

namespace frontend\controllers;

use modules\userStats\src\abac\UserStatsAbacObject;
use src\auth\Auth;
use src\model\user\entity\sales\SalesSearch;
use src\model\user\entity\userStats\UserStatsSearch;
use src\model\user\reports\stats\Access;
use src\model\user\reports\stats\SessionFilterStorage;
use src\model\user\reports\stats\UserStatsReport;
use src\model\userModelSetting\service\UserModelSettingService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

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

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'ajax-show-user-leads',
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
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

    /**
     * @throws ForbiddenHttpException
     */
    public function actionAjaxShowUserLeads(): string
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            throw new \RuntimeException('Method is not allowed');
        }
        /** @abac null, ProductQuoteChangeAbacObject::OBJ_USER_STATS, ProductQuoteChangeAbacObject::UserStatsAbacObject, Act Flight Create Voluntary quote*/
        if (!Yii::$app->abac->can(null, UserStatsAbacObject::OBJ_USER_STATS, UserStatsAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access Denied');
        }

        $user = Auth::user();
        $timeZone = \Yii::$app->user->identity->timezone;

        $filterStorage = new SessionFilterStorage();
        $savedFilters = $filterStorage->find($user->id, UserStatsReport::class);

        $searchModel = new UserStatsReport(
            $timeZone,
            date('Y-m') . '-01 00:00 - ' . date('Y-m-d') . ' 23:59',
            (new Access($user))
        );

        $dataProvider = $searchModel->searchLeadsByUser(
            $savedFilters,
            Yii::$app->request->get('user'),
            Yii::$app->request->get('type')
        );

        return $this->renderAjax('_user_leads_list_modal', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'type' => Yii::$app->request->get('type')
        ]);
    }
}
