<?php

namespace console\controllers;

use common\components\BackOffice;
use common\models\Airline;
use common\models\Airport;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\EmployeeAcl;
use common\models\EmployeeContactInfo;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadPreferences;
use common\models\Note;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\Quote;
use common\models\QuotePrice;
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
        $this->addSeller($result, $data);
    }

    private function addSeller($result, $data)
    {
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

    public function actionLeads($status = '', $projects = '')
    {
        $attr = [];
        if (!empty($status)) {
            $attr['status'] = $status;
        }

        if (!empty($projects)) {
            $attr['projects'] = $projects;
        }

        $query = '';
        if (!empty($attr)) {
            $query = '?' . http_build_query($attr);
        }

        echo $query . PHP_EOL;

        $result = BackOffice::sendRequest('old/leads' . $query);
        if (isset($result['data'])) {
            foreach ($result['data'] as $leadId => $objects) {
                try {
                    $leadId = intval($leadId);
                    //check if exist employee
                    if (empty($objects['Lead']['employee_id'])) {
                        continue;
                    }
                    $employee = Employee::findOne(['id' => $objects['Lead']['employee_id']]);
                    if ($employee === null) {
                        echo 'Need sync employee id: ' . $objects['Lead']['employee_id'] . PHP_EOL;
                        $data = [
                            'projects' => [6],
                            'employeeID' => $objects['Lead']['employee_id']
                        ];
                        $result = BackOffice::sendRequest('old/sellers', 'POST', json_encode($data));
                        $this->addSeller($result, $data);
                    }

                    //add-edit client object
                    $client = Client::findOne(['id' => $objects['Client']['id']]);
                    if ($client === null) {
                        $client = new Client();
                    }
                    $client->attributes = $objects['Client'];
                    if (!$client->save()) {
                        var_dump($client->getErrors());
                        //exit;
                    }

                    //clear and add client email object
                    ClientEmail::deleteAll([
                        'client_id' => $client->id
                    ]);
                    foreach ($objects['Emails'] as $item) {
                        $email = new ClientEmail();
                        $email->attributes = $item;
                        $email->client_id = $client->id;
                        $email->save();
                    }

                    //clear and add client phone object
                    ClientPhone::deleteAll([
                        'client_id' => $client->id
                    ]);
                    foreach ($objects['Phones'] as $item) {
                        $phone = new ClientPhone();
                        $phone->attributes = $item;
                        $phone->client_id = $client->id;
                        $phone->save();
                    }

                    //add-edit lead object
                    $lead = Lead::findOne(['id' => $leadId]);
                    if ($lead === null) {
                        $lead = new Lead();
                    }
                    $lead->attributes = $objects['Lead'];
                    $lead->client_id = $client->id;
                    $lead->id = $leadId;
                    if (!$lead->save()) {
                        var_dump($lead->getErrors());
                        //exit;
                    }

                    $lead->created = $objects['Lead']['created'];
                    $lead->updated = $objects['Lead']['updated'];
                    $lead->updated(false, ['created', 'updated']);

                    //edit-add preference object
                    $preference = LeadPreferences::findOne(['id' => $leadId]);
                    if ($preference === null) {
                        $preference = new LeadPreferences();
                    }
                    $preference->attributes = $objects['Lead'];
                    $preference->lead_id = $lead->id;
                    if (!$preference->save()) {
                        var_dump($preference->getErrors());
                        //exit;
                    }

                    //clear and add leadFlightSegments object
                    LeadFlightSegment::deleteAll([
                        'lead_id' => $lead->id
                    ]);
                    foreach ($objects['LeadFlightSegments'] as $item) {
                        $segment = new LeadFlightSegment();
                        $segment->attributes = $item;
                        $segment->lead_id = $lead->id;
                        $segment->save();
                        if (!$segment->save()) {
                            var_dump($segment->getErrors());
                            // exit;
                        }
                    }

                    //clear and add notes object
                    Note::deleteAll([
                        'lead_id' => $lead->id
                    ]);
                    foreach ($objects['Notes'] as $item) {
                        $note = new Note();
                        $note->attributes = $item;

                        $employeeNote = Employee::findOne(['id' => $note->employee_id]);
                        if ($employeeNote == null) {
                            continue;
                        }

                        $note->lead_id = $lead->id;
                        $note->save();
                        if (!$note->save()) {
                            var_dump($note->getErrors());
                            //exit;
                        }
                        $note->created = $item['created'];
                        $note->updated(false, ['created']);
                    }


                    //clear and add quotes object
                    $deleted = Quote::findAll([
                        'lead_id' => $lead->id
                    ]);
                    foreach ($deleted as $d) {
                        $d->delete();
                    }
                    foreach ($objects['Quotes'] as $item) {
                        $quote = new Quote();
                        $quote->attributes = $item;
                        $quote->lead_id = $lead->id;
                        $quote->save();
                        if (!$quote->save(false)) {
                            echo 'LEAD: ' . $quote->lead_id . PHP_EOL;
                            var_dump($quote->getErrors());
                            //exit;
                        } else {
                            $quote->created = $item['created'];
                            $quote->updated = $item['updated'];
                            $quote->updated(false, ['created', 'updated']);
                            foreach ($item['QuotePrices'] as $priceItem) {
                                $quotePrice = new QuotePrice();
                                $quotePrice->attributes = $priceItem;
                                $quotePrice->quote_id = $quote->id;
                                $quotePrice->save();
                                $quotePrice->created = $priceItem['created'];
                                $quotePrice->updated = $priceItem['updated'];
                                $quotePrice->updated(false, ['created', 'updated']);
                            }
                        }
                    }

                    echo 'Sync success Lead id: ' . $lead->id . PHP_EOL;
                } catch (\Throwable $throwable) {
                    var_dump($throwable->getMessage());
                    var_dump($throwable->getTraceAsString());
                }
            }
        }
    }
}