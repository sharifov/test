<?php

namespace frontend\controllers;

use common\models\Department;
use common\models\Employee;
use common\models\UserGroup;
use sales\auth\Auth;
use sales\model\user\entity\userStats\UserStatsSearch;
use sales\model\user\reports\stats\UserStatsReport;
use sales\model\userModelSetting\service\UserModelSettingService;
use Yii;
use yii\helpers\VarDumper;
use yii\rbac\Role;
use yii\web\Controller;

/**
 * Class UserStatsController
 */
class UserStatsController extends Controller
{
    public function init(): void
    {
        parent::init();
//        $this->layoutCrud();
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
        $searchModel = new UserStatsReport(
            \Yii::$app->user->identity->timezone,
            date('Y-m') . '-01 00:00 - ' . date('Y-m-d') . ' 23:59',
            Department::getList(),
            array_map(fn (Role $item) => $item->description, Yii::$app->authManager->getRoles()),
            UserGroup::getList(),
            Employee::getActiveUsersList()
        );
        $dataProvider = $searchModel->search(array_merge(Yii::$app->request->queryParams, Yii::$app->request->post()));

        return $this->render('report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
