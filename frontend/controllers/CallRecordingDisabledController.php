<?php

namespace frontend\controllers;

use common\models\search\ClientSearch;
use common\models\search\DepartmentPhoneProjectSearch;
use common\models\search\DepartmentSearch;
use common\models\search\ProjectSearch;
use common\models\search\SettingSearch;
use common\models\search\UserProfileSearch;

class CallRecordingDisabledController extends FController
{
    public function actionList()
    {
        $queryParams = \Yii::$app->request->queryParams;

        $systemSettingsDataProvider = (new SettingSearch())->searchByCallRecording();

        $userProfileSearchModel = new UserProfileSearch();
        $userProfileDataProvider = $userProfileSearchModel->search($queryParams);

        $clientSearchModel = new ClientSearch();
        $clientDataProvider = $clientSearchModel->searchByCallRecording($queryParams);

        $projectSearchModel = new ProjectSearch();
        $projectDataProvider = $projectSearchModel->searchByCallRecording($queryParams);

        $departmentSearchModel = new DepartmentSearch();
        $departmentDataProvider = $departmentSearchModel->searchByCallRecording($queryParams);

        $departmentPhoneProjectSearchModel = new DepartmentPhoneProjectSearch();
        $departmentPhoneProjectDataProvider = $departmentPhoneProjectSearchModel->searchByCallRecording($queryParams);

        return $this->render('list', [
            'systemSettingsDataProvider' => $systemSettingsDataProvider,

            'userProfileDataProvider' => $userProfileDataProvider,
            'userProfileSearchModel' => $userProfileSearchModel,

            'clientDataProvider' => $clientDataProvider,
            'clientSearchModel' => $clientSearchModel,

            'projectDataProvider' => $projectDataProvider,
            'projectSearchModel' => $projectSearchModel,

            'departmentSearchModel' => $departmentSearchModel,
            'departmentDataProvider' => $departmentDataProvider,

            'departmentPhoneProjectSearchModel' => $departmentPhoneProjectSearchModel,
            'departmentPhoneProjectDataProvider' => $departmentPhoneProjectDataProvider,
        ]);
    }
}
