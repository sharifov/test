<?php
namespace frontend\models;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadPreferences;
use yii\base\Exception;
use yii\base\Model;
use yii\web\BadRequestHttpException;

/**
 * Lead form
 */
class LeadForm extends Model
{
    const
        VIEW_MODE = 'view',
        EDIT_MODE = 'edit';
    /**
     * @var $mode string
     */
    public $mode;
    /**
     * @var $viewPermission boolean
     */
    public $viewPermission = true;
    /**
     * @var $_lead Lead
     */
    private $_lead;
    /**
     * @var $_client Client
     */
    private $_client;
    /**
     * @var $_clientPhone ClientPhone[]
     */
    private $_clientPhone;
    /**
     * @var $_clientEmail ClientEmail[]
     */
    private $_clientEmail;
    /**
     * @var $_leadFlightSegment LeadFlightSegment[]
     */
//    private $_leadFlightSegment;
    /**
     * @var $_leadPreferences LeadPreferences
     */
    private $_leadPreferences;

    public function __construct(Lead $lead = null, array $config = [])
    {
        if ($lead === null) {
            $this->setLead((new Lead()));
            $this->setClient((new Client()));
            $this->setClientEmail([(new ClientEmail())]);
            $this->setClientPhone([(new ClientPhone())]);
//            $this->setLeadFlightSegment([
//                (new LeadFlightSegment()),
//                (new LeadFlightSegment())
//            ]);
            $this->setLeadPreferences((new LeadPreferences()));
        } else {
            $this->setLead($lead);
            $this->setClient($lead->client);

            if (empty($lead->client->clientEmails)) {
                $this->setClientEmail([(new ClientEmail())]);
            } else {
                $this->setClientEmail($lead->client->clientEmails);
            }

            if (empty($lead->client->clientPhones)) {
                $this->setClientPhone([(new ClientPhone())]);
            } else {
                $this->setClientPhone($lead->client->clientPhones);
            }

//            $this->setLeadFlightSegment($lead->leadFlightSegments);

            if(!$this->getLeadFlightSegment()) {
                $this->setLeadFlightSegment([
                    (new LeadFlightSegment()),
                    (new LeadFlightSegment())
                ]);
            }

            if ($lead->leadPreferences === null) {
                $this->setLeadPreferences(new LeadPreferences());
            } else {
                $this->setLeadPreferences($lead->leadPreferences);
            }
        }
        $this->mode = self::EDIT_MODE;

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Client', 'Lead'], 'required'],
//            ['LeadFlightSegment', 'required'],
            [['ClientPhone', 'ClientEmail', 'LeadPreferences'], 'safe'],
        ];
    }

    public function afterValidate()
    {
        /**
         * @var $model Model
         * @var $item Model
         */
        foreach ($this->getAllModels() as $id => $model) {
            if (!is_array($model)) {
                if (!$model->validate()) {
                    $this->addError($id, $model->getErrors());
                }
            } else {
                $errors = [];
                foreach ($model as $key => $item) {
                    if (!$item->validate()) {
                        $errors[$key] = $item->getErrors();
                    }
                }
                if (!empty($errors)) {
                    $this->addError($id, $errors);
                }
            }
        }

        if (!$this->hasErrors('ClientPhone') && $this->hasErrors('ClientEmail')) {
            $this->clearErrors('ClientEmail');
        }
        if ($this->hasErrors('ClientPhone') && !$this->hasErrors('ClientEmail')) {
            $this->clearErrors('ClientPhone');
        }

        parent::afterValidate();
    }

    private function getAllModels()
    {
        $models = [
            'Client' => $this->getClient(),
            'Lead' => $this->getLead(),
            'LeadPreferences' => $this->getLeadPreferences(),
        ];
//        foreach ($this->getLeadFlightSegment() as $id => $segment) {
//            $models['LeadFlightSegment'][$id] = $this->getLeadFlightSegment()[$id];
//        }
        foreach ($this->getClientEmail() as $id => $email) {
            $models['ClientEmail'][$id] = $this->getClientEmail()[$id];
        }
        foreach ($this->getClientPhone() as $id => $phone) {
            $models['ClientPhone'][$id] = $this->getClientPhone()[$id];
        }
        return $models;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * @param Client $client
     */
    public function setClient($client)
    {
        $this->_client = $client;
    }

    /**
     * @return Lead
     */
    public function getLead()
    {
        return $this->_lead;
    }

    /**
     * @param Lead $leadModel
     */
    public function setLead($leadModel)
    {
        if ($leadModel->isNewRecord) {
            $leadModel->trip_type = $leadModel::TRIP_TYPE_ROUND_TRIP;
            $leadModel->cabin = $leadModel::CABIN_ECONOMY;
            $leadModel->adults = 1;
            $leadModel->children = 0;
            $leadModel->infants = 0;
            $leadModel->uid = uniqid();
            $leadModel->id = 0;
            $leadModel->status = Lead::STATUS_PENDING;
        }
        $this->_lead = $leadModel;
    }

    /**
     * @return LeadPreferences
     */
    public function getLeadPreferences()
    {
        return $this->_leadPreferences;
    }

    /**
     * @param LeadPreferences $leadPreferences
     */
    public function setLeadPreferences($leadPreferences)
    {
        $this->_leadPreferences = $leadPreferences;
    }

    /**
     * @return LeadFlightSegment[]
     */
//    public function getLeadFlightSegment()
//    {
//        return $this->_leadFlightSegment;
//    }

    /**
     * @param LeadFlightSegment[] $leadFlightSegment
     */
//    public function setLeadFlightSegment($leadFlightSegment)
//    {
//        $this->_leadFlightSegment = [];
//        foreach ($leadFlightSegment as $key => $segment) {
//            if (!$segment->isNewRecord) {
//                $this->_leadFlightSegment[$segment->id] = $segment;
//            } else {
//                $this->_leadFlightSegment[$key] = $segment;
//            }
//        }
//    }

    /**
     * @return ClientEmail[]
     */
    public function getClientEmail()
    {
        return $this->_clientEmail;
    }

    /**
     * @param ClientEmail[] $clientEmail
     */
    public function setClientEmail($clientEmail)
    {
        $this->_clientEmail = [];
        foreach ($clientEmail as $key => $email) {
            if (!$email->isNewRecord) {
                $this->_clientEmail[$email->id] = $email;
            } else {
                $this->_clientEmail[$key] = $email;
            }
        }
    }

    /**
     * @return ClientPhone[]
     */
    public function getClientPhone()
    {
        return $this->_clientPhone;
    }

    /**
     * @param ClientPhone[] $clientPhone
     */
    public function setClientPhone($clientPhone)
    {
        $this->_clientPhone = [];
        foreach ($clientPhone as $key => $phone) {
            if (!$phone->isNewRecord) {
                $this->_clientPhone[$phone->id] = $phone;
            } else {
                $this->_clientPhone[$key] = $phone;
            }
        }
    }

    public function save(&$errors = [])
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $hasErrors = false;

            $client = $this->getClient();
            if (!$client->save()) {
                $hasErrors = true;
                $errors['Client'] = $client->getErrors();
            } else {
                $lead = $this->getLead();
                $lead->client_id = $client->id;
//                if (count($this->getLeadFlightSegment()) == 1) {
//                    $lead->trip_type = Lead::TRIP_TYPE_ONE_WAY;
//                }
                if (!$lead->save()) {
                    $hasErrors = true;
                    $errors['Lead'] = $lead->getErrors();
                } else {
//                    $keep = [];
//                    foreach ($this->getLeadFlightSegment() as $key => $flightSegment) {
//                        $flightSegment->lead_id = $lead->id;
//                        if (!$flightSegment->save()) {
//                            $hasErrors = true;
//                            $errors['LeadFlightSegment'][$key] = $flightSegment->getErrors();
//                        }
//                        $keep[] = $flightSegment->id;
//                    }
//
//                    $query = LeadFlightSegment::find()->andWhere(['lead_id' => $lead->id]);
//                    if ($keep) {
//                        $query->andWhere(['NOT IN', 'id', $keep]);
//                    }
//                    foreach ($query->all() as $segment) {
//                        $segment->delete();
//                    }

                    $preference = $this->getLeadPreferences();
                    $preference->lead_id = $lead->id;
                    $preference->save();
                }

                $savedClientEmail = false;
                foreach ($this->getClientEmail() as $key => $clientEmail) {
                    $clientEmail->client_id = $client->id;
                    if ($clientEmail->save()) {
                        $savedClientEmail = true;
                    } else {
                        $errors['ClientEmail'][$key] = $clientEmail->getErrors();
                    }
                }

                $savedClientPhone = false;
                foreach ($this->getClientPhone() as $key => $clientPhone) {
                    $clientPhone->client_id = $client->id;
                    if ($clientPhone->save()) {
                        $savedClientPhone = true;
                    } else {
                        $errors['ClientPhone'][$key] = $clientPhone->getErrors();
                    }
                }

                if (!$savedClientEmail && !$savedClientPhone) {
                    $hasErrors = true;
                }
            }

            if (!$hasErrors) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
            }
        } catch (Exception $ex) {
            $transaction->rollBack();
            $errors[] = $ex->getMessage();
            $errors[] = $ex->getTraceAsString();
        }
        return false;
    }

    public function loadModels($data)
    {
        /* @var $models Model[] */
        $success = false;
        $models = $this->getAllModels();
        foreach ($data as $modelName => $attributes) {
            if (isset($models[$modelName])) {
//                if (in_array($modelName, ['LeadFlightSegment', 'ClientEmail', 'ClientPhone'])) {
                if (in_array($modelName, ['ClientEmail', 'ClientPhone'])) {
                    $modelsPopulate = [];
                    foreach ($attributes as $key => $item) {
                        if ($key == '__id__') {
                            continue;
                        }
                        $m = $this->getModelClass($modelName, $key);
                        if (!empty($item) && $m->load($item, '')) {
                            $success = true;
                        }
                        $modelsPopulate[$key] = $m;
                    }
                    if (!empty($modelsPopulate)) {
                        $this->modelsPopulate($modelsPopulate, $modelName);
                    }
                } else {
                    if (!empty($attributes) && $models[$modelName]->load($attributes, '')) {
                        $success = true;
                    }
                }
            }
        }

        return $success;
    }

    /**
     * @param $model
     * @param $key
     * @return Model
     * @throws BadRequestHttpException
     */
    private function getModelClass($model, $key)
    {
        $mapping = [
            'Lead' => 'common/models/Lead',
            'LeadPreferences' => 'common\models\LeadPreferences',
//            'LeadFlightSegment' => 'common\models\LeadFlightSegment',
            'ClientEmail' => 'common\models\ClientEmail',
            'ClientPhone' => 'common\models\ClientPhone',
            'Client' => 'common\models\Client',
        ];

        if (isset($mapping[$model])) {
            if (is_int($key)) {
                return $this->getAllModels()[$model][$key];
            } else {
                return new $mapping[$model];
            }
        }

        throw new BadRequestHttpException(sprintf('Unknown model % getModelClass()', self::class));
    }

    /**
     * @param $modelsPopulate
     * @param $model
     */
    private function modelsPopulate($modelsPopulate, $model)
    {
//        if ($model == 'LeadFlightSegment') {
//            $this->setLeadFlightSegment($modelsPopulate);
        if ($model == 'ClientEmail') {
            $this->setClientEmail($modelsPopulate);
        } else if ($model == 'ClientPhone') {
            $this->setClientPhone($modelsPopulate);
        }
    }
}
