<?php

namespace console\controllers;

use common\components\BackOffice;
use common\models\Airline;
use common\models\Airports;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\EmployeeAcl;
use common\models\EmployeeContactInfo;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadFlow;
use common\models\LeadPreferences;
use common\models\Note;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\Quote;
use common\models\QuotePrice;
use common\models\Sources;
use modules\flight\src\useCases\sale\FlightFromSaleService;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\payment\PaymentRepository;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleService;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\airportLang\service\AirportLangService;
use sales\services\cases\CasesSaleService;
use yii\console\Controller;
use Yii;
use yii\console\ExitCode;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;

/**
 * Class SyncController
 *
 * @property OrderRepository $orderRepository
 * @property PaymentRepository $paymentRepository
 * @property OrderCreateFromSaleService $orderCreateFromSaleService
 * @property FlightFromSaleService $flightFromSaleService,
 */
class SyncController extends Controller
{
    private OrderRepository $orderRepository;
    private PaymentRepository $paymentRepository;
    private OrderCreateFromSaleService $orderCreateFromSaleService;
    private FlightFromSaleService $flightFromSaleService;

    public function __construct(
        $id,
        $module,
        OrderRepository $orderRepository,
        PaymentRepository $paymentRepository,
        OrderCreateFromSaleService $orderCreateFromSaleService,
        FlightFromSaleService $flightFromSaleService,
        $config = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentRepository = $paymentRepository;
        $this->orderCreateFromSaleService = $orderCreateFromSaleService;
        $this->flightFromSaleService = $flightFromSaleService;

        parent::__construct($id, $module, $config);
    }

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
                    $source = Sources::findOne(['id' => $sourceId]);
                    if ($source === null) {
                        $source = new Sources();
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
                $airport = Airports::findOne($airportAttr['iata']);
                if ($airport === null) {
                    $airport = new Airports();
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

    /**
     * @param int $limit
     * @throws \yii\httpclient\Exception
     */
    public function actionAirports2($limit = 12000)
    {
        printf("\n --- [" . date('Y-m-d H:i:s') . "] Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $result = Airports::synchronization($limit);

        if ($result) {
            if ($result['error']) {
                printf(" - Error: %s\n", $this->ansiFormat($result['error'], Console::FG_RED));
            } else {
                if ($result['created']) {
                    $message = 'Created Airports (' . count($result['created']) . '): "' . implode(', ', $result['created']);
                    printf(" - Created: %s\n", $this->ansiFormat($message, Console::FG_GREEN));
                }
                if ($result['updated']) {
                    $message = 'Updated Airports (' . count($result['updated']) . '): "' . implode(', ', $result['updated']);
                    printf(" - Updated: %s\n", $this->ansiFormat($message, Console::FG_YELLOW));
                }
                if ($result['errored']) {
                    $message = 'Errored Airports (' . count($result['errored']) . '): "' . implode(', ', $result['errored']);
                    printf(" - Error: %s\n", $this->ansiFormat($message, Console::FG_RED));
                }
            }
        }

        printf("\n --- [" . date('Y-m-d H:i:s') . "] End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
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
                    $empoloyee->roles[] = $empoloyeeeAttr['role'];
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

                    echo 'Sync success Employee id: ' . $empoloyeeId . '. Roles: ' . implode(', ', $empoloyee->getRoles()) . PHP_EOL;
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

    public function actionLeads($status = '', $projects = '', $limit = 1000, $offset = 0)
    {
        $attr = [
            'limit' => $limit,
            'offset' => $offset,
        ];
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
        var_dump($result);

        var_dump($result['sql']);
        if (isset($result['errors'])) {
            var_dump($result['errors']);
        }

        if (isset($result['data'])) {
            foreach ($result['data'] as $leadId => $objects) {
                try {
                    $leadId = intval($leadId);
                    //check if exist employee
                    if (empty($objects['Lead']['employee_id']) && !in_array($status, [5, 1])) {
                        continue;
                    }
                    if (!in_array($status, [5, 1])) {
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
                    echo 'Sync success Client id: ' . $client->id . PHP_EOL;

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
                    echo 'Sync success ClientEmail id: ' . $client->id . PHP_EOL;

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
                    echo 'Sync success ClientPhone id: ' . $client->id . PHP_EOL;

                    //add-edit lead object
                    $lead = Lead::findOne(['id' => $leadId]);
                    if ($lead === null) {
                        $lead = new Lead();
                    }
                    $lead->attributes = $objects['Lead'];
                    $lead->client_id = $client->id;
                    $lead->id = $leadId;
                    $lead->additional_information = json_encode($lead->additional_information);
                    echo 'Sync before Lead id: ' . $lead->id . PHP_EOL;
                    if (!$lead->save(false)) {
                        var_dump($lead->getErrors());
                        //exit;
                    }
                    echo 'Sync success Lead id: ' . $lead->id . PHP_EOL;

                    //edit-add preference object
                    $preference = LeadPreferences::findOne(['id' => $leadId]);
                    if ($preference === null) {
                        $preference = new LeadPreferences();
                    }
                    $preference->attributes = $objects['Lead'];
                    $preference->lead_id = $lead->id;
                    if (!$preference->save(false)) {
                        var_dump($preference->getErrors());
                        //exit;
                    }
                    echo 'Sync success LeadPreferences id: ' . $lead->id . PHP_EOL;

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
                    echo 'Sync success LeadFlightSegment id: ' . $lead->id . PHP_EOL;

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
                        $note->update(false, ['created']);

                        echo 'Sync success Note id: ' . $lead->id . PHP_EOL;
                    }


                    //clear and add quotes object
                    $deleted = Quote::findAll([
                        'lead_id' => $lead->id
                    ]);
                    foreach ($deleted as $d) {
                        $d->delete();
                    }
                    echo 'Deleted success Quote id: ' . $lead->id . PHP_EOL;
                    foreach ($objects['Quotes'] as $item) {
                        $quote = new Quote();
                        $quote->attributes = $item;
                        $quote->lead_id = $lead->id;
                        $quote->save();
                        $quote->createQuoteTrips();

                        if (!$quote->save(false)) {
                            echo 'LEAD: ' . $quote->lead_id . PHP_EOL;
                            var_dump($quote->getErrors());
                        //exit;
                        } else {
                            $quote->created = $item['created'];
                            $quote->update(false, ['created']);

                            Yii::$app->db->createCommand('UPDATE ' . Quote::tableName() . ' SET updated = :updated WHERE id = :id', [
                                ':updated' => $item['updated'],
                                ':id' => $quote->id
                            ])->execute();

                            foreach ($item['QuotePrices'] as $priceItem) {
                                $quotePrice = new QuotePrice();
                                $quotePrice->attributes = $priceItem;
                                $quotePrice->quote_id = $quote->id;
                                $quotePrice->save();

                                $quotePrice->created = $priceItem['created'];
                                $quotePrice->update(false, ['created']);

                                Yii::$app->db->createCommand('UPDATE ' . QuotePrice::tableName() . ' SET updated = :updated WHERE id = :id', [
                                    ':updated' => $priceItem['updated'],
                                    ':id' => $quotePrice->id
                                ])->execute();
                            }
                        }
                        echo 'Sync success Quote id: ' . $quote->id . PHP_EOL;
                    }

                    LeadFlow::deleteAll([
                        'lead_id' => $lead->id
                    ]);
                    if (!empty($objects['Flows'])) {
                        foreach ($objects['Flows'] as $item) {
                            $flow = new LeadFlow();
                            $flow->attributes = $item;
                            $flow->lead_id = $lead->id;
                            $flow->save();
                        }
                    }

//                    if (!empty($objects['Reason'])) {
//                        $reason = new Reason();
//                        $reason->attributes = $objects['Reason'];
//                        $reason->lead_id = $lead->id;
//                        $reason->save();
//                        echo 'Sync success Reason id: ' . $lead->id . PHP_EOL;
//                    }

                    $lead->created = $objects['Lead']['created'];
                    $lead->update(false, ['created']);

                    Yii::$app->db->createCommand('UPDATE ' . Lead::tableName() . ' SET updated = :updated WHERE id = :id', [
                        ':updated' => $objects['Lead']['updated'],
                        ':id' => $lead->id
                    ])->execute();

                    echo 'Sync FINAL success Lead id: ' . $lead->id . PHP_EOL;
                    //sleep(1);
                } catch (\Throwable $throwable) {
                    var_dump($throwable->getMessage());
                    var_dump($throwable->getTraceAsString());
                }
            }
        }
    }

    public function actionAirportsLang($limit = 99999)
    {
        echo Console::renderColoredString('%y --- Start %w[' . date('Y-m-d H:i:s') . '] %y' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);

        $result = AirportLangService::synchronization(0);

        if ($result) {
            if ($result['error']) {
                echo Console::renderColoredString('%R --- Error: %r' . VarDumper::dumpAsString($result['error']) . ' %n'), PHP_EOL;
            } else {
                if ($result['info']) {
                    $message = implode(', ', $result['info']);
                    echo Console::renderColoredString('%y --- Info: %w' . $message . ' %n'), PHP_EOL;
                }

                if ($result['created']) {
                    $message = '(' . count($result['created']) . ') ';
                    echo Console::renderColoredString('%y --- Created AirportLand: %w' . $message . ' %n'), PHP_EOL;
                }
                if ($result['updated']) {
                    $message = 'Updated AirportLand (' . count($result['updated']) . ') ';
                    echo Console::renderColoredString('%y --- Updated AirportLand: %w' . $message . ' %n'), PHP_EOL;
                }
                if ($result['errored']) {
                    $message = '(' . count($result['errored']) . '): "' . implode(', ', $result['errored']);
                    echo Console::renderColoredString('%r --- Errored AirportLand: %R' . $message . ' %n'), PHP_EOL;
                }
            }
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . ']%n'), PHP_EOL;
    }

    public function actionSales($limit = 20, $from_id = 1)
    {
        if (!SettingHelper::isEnableOrderFromSale()) {
            echo Console::renderColoredString('%y --- %YWarning:%n%y Service is disabled.%n%w setting - enable_order_from_sale = false%n'), PHP_EOL;
            return ExitCode::CONFIG;
        }
        echo Console::renderColoredString('%y --- Start %w[' . date('Y-m-d H:i:s') . '] %y' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);
        $idLimit = $from_id + $limit;
        $processed = 0;
        Console::startProgress(0, $limit);

        $casesSaleService = Yii::createObject(CasesSaleService::class);

        for ($saleId = $from_id; $saleId < $idLimit; $saleId++) {
            if ($order = Order::findOne(['or_sale_id' => $saleId])) {
                echo Console::renderColoredString('%g --- SaleId: %w[' . $saleId . ']%g already processed in OrderId %w[' .
                    $order->getId() . '] %n'), PHP_EOL;
                continue;
            }

            try {
                $saleData = $casesSaleService->detailRequestToBackOffice($saleId, 0, 120, 1);
            } catch (\Throwable $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['saleId'] = $saleId;
                Yii::warning(AppHelper::throwableLog($throwable), 'SyncController:actionSales:RequestToBO');
                self::showMessage($throwable->getMessage());
                continue;
            }

            $transactionOrder = new Transaction(['db' => Yii::$app->db]);
            try {
                if (!$order = Order::findOne(['or_sale_id' => $saleId])) {
                    $orderCreateFromSaleForm = new OrderCreateFromSaleForm();
                    if (!$orderCreateFromSaleForm->load($saleData)) {
                        throw new \RuntimeException('OrderCreateFromSaleForm not loaded');
                    }
                    if (!$orderCreateFromSaleForm->validate()) {
                        throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($orderCreateFromSaleForm));
                    }
                    $order = $this->orderCreateFromSaleService->orderCreate($orderCreateFromSaleForm);

                    $transactionOrder->begin();
                    $orderId = $this->orderRepository->save($order);
                    $this->orderCreateFromSaleService->orderContactCreate($order, OrderContactForm::fillForm($saleData));

                    $currency = $orderCreateFromSaleForm->currency;
                    $this->flightFromSaleService->createHandler($order, $orderCreateFromSaleForm, $saleData);

                    if ($authList = ArrayHelper::getValue($saleData, 'authList')) {
                        $this->orderCreateFromSaleService->paymentCreate($authList, $orderId, $currency);
                    }
                    $transactionOrder->commit();
                }
            } catch (\Throwable $throwable) {
                $transactionOrder->rollBack();
                $message = AppHelper::throwableLog($throwable, true);
                $message['saleData'] = $saleData;
                Yii::warning($message, 'SyncController:actionSales::CreateFromSale');
                self::showMessage($throwable->getMessage());
            }
            $processed++;
            Console::updateProgress($processed, $limit);
        }

        Console::endProgress(false);

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Processed: %w[' . $processed . '] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . ']%n'), PHP_EOL;
        return ExitCode::OK;
    }

    public static function showMessage($message, $lenght = 60)
    {
        $preparedMessage = StringHelper::truncate(VarDumper::dumpAsString($message), $lenght, '... %n%wFull text in the logs.');
        echo Console::renderColoredString('%r --- %RError: %n%p' . $preparedMessage . ' %n'), PHP_EOL;
    }
}
