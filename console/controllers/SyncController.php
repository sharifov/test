<?php

namespace console\controllers;

use common\components\BackOffice;
use common\models\Airline;
use common\models\Airport;
use common\models\Employee;
use common\models\EmployeeAcl;
use common\models\EmployeeContactInfo;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\Source;
use yii\console\Controller;
use Yii;

class SyncController extends Controller
{
    public function actionProjects()
    {
        $result = BackOffice::sendRequest('default/projects');
        if (isset($result['data'])) {
            foreach ($result['data'] as $projectId => $projectAttr) {
                $project = Project::findOne(['id' => $projectId]);
                if ($project === null) {
                    $project = new Project();
                }
                $project->attributes = $projectAttr;
                if (!$project->save()) {
                    var_dump($project->getErrors());
                    exit;
                }
                foreach ($projectAttr['sources'] as $sourceId => $sourceAttr) {
                    $source = Source::findOne(['id' => $sourceId]);
                    if ($source === null) {
                        $source = new Source();
                    }
                    $source->attributes = $sourceAttr;
                    if (!$source->save()) {
                        var_dump($source->getErrors());
                        exit;
                    }
                }
                echo 'Sync success project id: ' . $projectId . PHP_EOL;
            }
        }
    }

    public function actionAirports()
    {
        $result = BackOffice::sendRequest('default/airports');
        if (isset($result['data'])) {
            foreach ($result['data'] as $airportId => $airportAttr) {
                $airport = Airport::findOne(['id' => $airportId]);
                if ($airport === null) {
                    $airport = new Airport();
                }
                $airport->attributes = $airportAttr;
                if (!$airport->save()) {
                    var_dump($airport->getErrors());
                    exit;
                }
                echo 'Sync success airport id: ' . $airportId . PHP_EOL;
            }
        }
    }

    public function actionAirlines()
    {
        $result = BackOffice::sendRequest('default/airlines');
        if (isset($result['data'])) {
            foreach ($result['data'] as $airlineId => $airlineAttr) {
                $airline = Airline::findOne(['id' => $airlineId]);
                if ($airline === null) {
                    $airline = new Airline();
                }
                $airline->attributes = $airlineAttr;
                if (!$airline->save()) {
                    var_dump($airline->getErrors());
                    exit;
                }
                echo 'Sync success airport id: ' . $airlineId . PHP_EOL;
            }
        }
    }

    public function actionSellers()
    {
        $data = ['projects' => [6]];
        $result = BackOffice::sendRequest('old/sellers', 'POST', json_encode($data));
        if (isset($result['data'])) {
            foreach ($result['data'] as $empoloyeeId => $empoloyeeeAttr) {
                $empoloyee = Employee::findOne(['id' => $empoloyeeId]);
                if ($empoloyee === null) {
                    $empoloyee = new Employee();
                    $empoloyee->id = intval($empoloyeeId);
                }
                $empoloyee->attributes = $empoloyeeeAttr;
                if (!$empoloyee->save()) {
                    var_dump($empoloyee->getErrors());
                    exit;
                } else {

                    $empoloyee->role = $empoloyeeeAttr['role'];
                    $empoloyee->addRole(false);

                    ProjectEmployeeAccess::deleteAll([
                        'employee_id' => $empoloyee->id
                    ]);

                    foreach ($data['projects'] as $id) {
                        $access = new ProjectEmployeeAccess();
                        $access->employee_id = $empoloyee->id;
                        $access->project_id = intval($id);
                        $access->save();
                    }

                    echo 'Sync success Employee id: ' . $empoloyeeId . '. Role: ' . $empoloyee->role . PHP_EOL;
                    if (!empty($empoloyeeeAttr['contactInfo'])) {
                        foreach ($empoloyeeeAttr['contactInfo'] as $projectId => $attr) {
                            $contactInfo = EmployeeContactInfo::findOne([
                                'employee_id' => $empoloyeeId,
                                'project_id' => $attr['project_id']
                            ]);
                            if ($contactInfo == null) {
                                $contactInfo = new  EmployeeContactInfo();
                            }
                            $contactInfo->attributes = $attr;
                            if (!$contactInfo->save()) {
                                var_dump($contactInfo->getErrors());
                                exit;
                            }
                            echo 'Sync success ContactInfo id: ' . $empoloyeeId . PHP_EOL;
                        }
                    }

                    if (!empty($empoloyeeeAttr['aclRules'])) {
                        foreach ($empoloyeeeAttr['aclRules'] as $key => $attr) {
                            $acl = EmployeeAcl::findOne([
                                'employee_id' => $empoloyee->id,
                                'mask' => $attr['mask']
                            ]);
                            if ($acl == null) {
                                $acl = new  EmployeeAcl();
                            }
                            $acl->attributes = $attr;
                            if (!$acl->save()) {
                                var_dump($acl->getErrors());
                                exit;
                            }
                            echo 'Sync success Acl id: ' . $empoloyeeId . PHP_EOL;
                        }
                    }
                }
                echo 'Sync success: ' . $empoloyeeId . PHP_EOL;
            }
        }
    }
}