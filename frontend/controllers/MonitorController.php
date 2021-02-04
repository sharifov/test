<?php

namespace frontend\controllers;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\ConferenceParticipant;
use common\models\Department;
use common\models\Employee;
use common\models\Project;
use common\models\search\CallSearch;
use common\models\search\UserConnectionSearch;
use common\models\search\UserOnlineSearch;
use common\models\UserOnline;
use sales\auth\Auth;
use sales\guards\call\CallDisplayGuard;
use sales\helpers\app\AppParamsHelper;
use sales\helpers\call\CallHelper;
use sales\model\user\entity\userStatus\UserStatus;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class MonitorController extends FController
{
    public function actionCallIncoming(): string
    {
        $centrifugoEnabled = AppParamsHelper::isCentrifugoEnabled();
        $centrifugoWsConnectionUrl = AppParamsHelper::getCentrifugoWsConnectionUrl();

        if (!$centrifugoEnabled) {
            throw new InvalidConfigException('The "centrifugo" is not enabled.');
        }

        if (empty($centrifugoWsConnectionUrl)) {
            throw new InvalidConfigException('The "wsConnectionUrl" property must be set in config params.');
        }

        return $this->render('call-incoming', [
            'cfChannelName' => Call::CHANNEL_REALTIME_MAP,
            'cfUserOnlineChannel' => Call::CHANNEL_USER_ONLINE,
            'cfConnectionUrl' => $centrifugoWsConnectionUrl,
            'cfUserStatusChannel' => UserStatus::CHANNEL_NAME,
            'cfToken' => \Yii::$app->centrifugo->generateConnectionToken(Auth::id())
        ]);
    }

    /**
     * @return array
     */
    public function actionStaticDataApi(): array
    {
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Auth::user();
        $isAdmin = $user->isSuperAdmin() || $user->isOnlyAdmin();

        $userOnlineSearch = new UserOnlineSearch();
        $params = \Yii::$app->request->queryParams;

        $withOutDepartments = 0;
        if ($isAdmin) {
            $accessDepartments = [];
        } elseif ($departments = $user->udDeps) {
            $accessDepartments = ArrayHelper::getColumn($departments, 'dep_id');
        } else {
            $accessDepartments = [$withOutDepartments];
        }

        $withOutProjects = 0;
        if ($isAdmin) {
            $accessProjects = [];
        } elseif ($projects = $user->projects) {
            $accessProjects = ArrayHelper::getColumn($projects, 'id');
        } else {
            $accessProjects = [$withOutProjects];
        }

        $accessGroups = [];

        $params['UserOnlineSearch']['ug_ids'] = $accessGroups;
        $params['UserOnlineSearch']['project_ids'] = $accessProjects;
        $params['UserOnlineSearch']['user_id'] = Auth::id();

        if ($isAdmin || in_array(Department::DEPARTMENT_SALES, $accessDepartments, true)) {
            $params['UserOnlineSearch']['dep_ids'][] = Department::DEPARTMENT_SALES;
        }

        if ($isAdmin || in_array(Department::DEPARTMENT_EXCHANGE, $accessDepartments, true)) {
            $params['UserOnlineSearch']['dep_ids'][] = Department::DEPARTMENT_EXCHANGE;
        }

        if ($isAdmin || in_array(Department::DEPARTMENT_SUPPORT, $accessDepartments, true)) {
            $params['UserOnlineSearch']['dep_ids'][] = Department::DEPARTMENT_SUPPORT;
        }

        if ($isAdmin || in_array(Department::DEPARTMENT_SCHEDULE_CHANGE, $accessDepartments, true)) {
            $params['UserOnlineSearch']['dep_ids'][] = Department::DEPARTMENT_SCHEDULE_CHANGE;
        }

        if ($isAdmin || in_array($withOutDepartments, $accessDepartments, true)) {
            $params['UserOnlineSearch']['dep_ids'][] = $withOutDepartments;
        }

        $response['projectList'] = Project::getList();
        $response['depList'] = Department::DEPARTMENT_LIST;
        $response['userList'] = Employee::getList();

        $response['callStatusList'] = Call::STATUS_LIST;
        $response['callSourceList'] = Call::SHORT_SOURCE_LIST;
        $response['callTypeList'] = Call::TYPE_LIST;
        $response['callUserAccessStatusTypeList'] = CallUserAccess::STATUS_TYPE_LIST;
        $response['onlineUserList'] = $userOnlineSearch->searchUserByIncomingCall($params);
        $response['userStatusList'] = UserStatus::find()->all();

        $response['userTimeZone'] = Auth::user()->timezone ?: 'UTC';

        return $response;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function actionListApi(): array
    {
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Auth::user();
        $isAdmin = $user->isSuperAdmin() || $user->isOnlyAdmin();

        $callSearch = new CallSearch();
        $params = \Yii::$app->request->queryParams;

        $withOutDepartments = 0;
        if ($isAdmin) {
            $accessDepartments = [];
        } elseif ($departments = $user->udDeps) {
            $accessDepartments = ArrayHelper::getColumn($departments, 'dep_id');
        } else {
            $accessDepartments = [$withOutDepartments];
        }

        $withOutProjects = 0;
        if ($isAdmin) {
            $accessProjects = [];
        } elseif ($projects = $user->projects) {
            $accessProjects = ArrayHelper::getColumn($projects, 'id');
        } else {
            $accessProjects = [$withOutProjects];
        }

        $accessGroups = [];

        $params['CallSearch']['dep_ids'] = $accessDepartments;
        $params['CallSearch']['project_ids'] = $accessProjects;
        $params['CallSearch']['ug_ids'] = $accessGroups;

        $params['CallSearch']['status_ids'] = [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS, Call::STATUS_HOLD, Call::STATUS_QUEUE, Call::STATUS_IVR, Call::STATUS_DELAY];
        $activeCalls = $callSearch->searchMonitorIncomingCalls($params);

        $callList = [];
        if ($activeCalls) {
            foreach ($activeCalls as $call) {
                $callList[] = $call->getApiData();
            }
        }
        $response['callList'] = $callList;
        return $response;
    }
}
