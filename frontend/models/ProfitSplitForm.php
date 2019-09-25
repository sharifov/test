<?php

namespace frontend\models;

use sales\forms\NotificationsFormHelper;
use yii\base\Model;
use common\models\Lead;
use common\models\ProfitSplit;
use yii\web\BadRequestHttpException;

/**
 * ProfitSplitForm form
 */
class ProfitSplitForm extends Model
{
    const
    VIEW_MODE = 'view',
    EDIT_MODE = 'edit';
    /**
     * @var $mode string
     */
    public $mode;
    /**
     * @var $_lead Lead
     */
    private $_lead;

    /**
     * @var $_profitSplit ProfitSplit[]
     */
    private $_profitSplit;

    public const SCENARIO_CHECK_PERCENTAGE = 'checkPercentageOfSplit';


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Lead'], 'required'],
            [['ProfitSplit'], 'checkSumPercentage'],
            [['ProfitSplit'], 'checkMainAgent'],
            [['ProfitSplit'], 'safe'],
			[['ProfitSplit'], 'checkSumPercent', 'on' => self::SCENARIO_CHECK_PERCENTAGE]
        ];
    }

    public function checkSumPercentage($attribute, $params)
    {
        $sum = $this->getSumProfitSplit();
        if($sum > 100){
            $this->addError('sumPercent', \Yii::t('user', 'Sum of percent more than 100'));
            return false;
        }
        return true;
    }

	/**
	 * @param $attribute
	 * @param $params
	 * @return bool
	 */
	public function checkSumPercent($attribute, $params): bool
	{
		$sum = $this->getSumProfitSplit();
		if ($sum === 100){
			NotificationsFormHelper::addNotification('sumPercent', \Yii::t('user', 'Sum of percent is 100. The main agent was left without profit.'));
			return false;
		}
		return true;
	}

    public function checkMainAgent($attribute, $params)
    {
        //var_dump($attribute);die;
        $employee = $this->getLead()->employee;
        $profitSplit = $this->getProfitSplit();
        if(!empty($profitSplit)){
            foreach ($profitSplit as $entry){
                if(!empty($entry->ps_user_id) && $employee->id == $entry->ps_user_id){
                    $this->addError('mainAgent', \Yii::t('user', $employee->username.' already is main agent'));
                    return false;
                }
            }
        }
        return true;
    }

    public function __construct(Lead $lead, array $config = [])
    {
        $this->setLead($lead);
       // $this->setProfitSplit([(new ProfitSplit())]);
        if(!empty($lead->profitSplits)){
            $this->setProfitSplit($lead->profitSplits);
        }

        $this->mode = self::EDIT_MODE;

        parent::__construct($config);
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

        if (!$this->hasErrors('ProfitSplit') && $this->hasErrors('ProfitSplit')) {
            $this->clearErrors('ProfitSplit');
        }

		if (NotificationsFormHelper::hasNotifications()) {
			$this->addError('warnings', NotificationsFormHelper::getAllAlertsNotifications('alert-warning'));
		}

        parent::afterValidate();
    }

    private function getAllModels()
    {
        $models = [
            'Lead' => $this->getLead(),
        ];
        $profitSplit = $this->getProfitSplit();
        if(!empty($profitSplit)){
            foreach ($profitSplit as $id => $split) {
                $models['ProfitSplit'][$id] = $profitSplit[$id];
            }
        }else{
            $models['ProfitSplit'][] = new ProfitSplit();
        }
        return $models;
    }
    /**
     * @return Lead $_lead
     */
    public function getLead()
    {
        return $this->_lead;
    }

    /**
     * @return ProfitSplit[]
     */
    public function getProfitSplit()
    {
        return $this->_profitSplit;
    }

    /**
     * @param \common\models\Lead $_lead
     */
    public function setLead($_lead)
    {
        $this->_lead = $_lead;
    }

    /**
     * @param ProfitSplit[]  $_profitSplit
     */
    public function setProfitSplit($_profitSplit)
    {
        $this->_profitSplit = [];

        foreach ($_profitSplit as $key => $prSplit) {
            if (!$prSplit->isNewRecord) {
                $this->_profitSplit[$prSplit->ps_id] = $prSplit;
            } else {
                $prSplit->ps_lead_id = $this->_lead->id;
                $this->_profitSplit[$key] = $prSplit;
            }
        }
    }


    public function save(&$errors = [])
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $hasErrors = false;
            $lead = $this->getLead();

            $keep = [];
            if(!empty($this->getProfitSplit())){
                foreach ($this->getProfitSplit() as $key => $split) {
                    $split->ps_lead_id = $lead->id;
                    if (!$split->save()) {
                        $hasErrors = true;
                        $errors['ProfitSplit'][$key] = $split->getErrors();
                    }
                    $keep[] = $split->ps_id;
                }
            }

            $query = ProfitSplit::find()->andWhere(['ps_lead_id' => $lead->id]);
            if ($keep) {
                $query->andWhere(['NOT IN', 'ps_id', $keep]);
            }
            foreach ($query->all() as $split) {
                $split->delete();
            }

            if (!$hasErrors) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
            }
        } catch (\Exception $ex) {
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
                if (in_array($modelName, ['ProfitSplit'])) {
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
                    $this->modelsPopulate($modelsPopulate, $modelName);

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
            'Lead' => 'common\models\Lead',
            'ProfitSplit' => 'common\models\ProfitSplit',
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
        if ($model == 'ProfitSplit') {
            $this->setProfitSplit($modelsPopulate);
        }
    }

	/**
	 * @return int
	 */
	private function getSumProfitSplit(): int
	{
		$profitSplit = $this->getProfitSplit();
		$sum = 0;
		if(!empty($profitSplit)){
			foreach ($profitSplit as $entry){
				if(!empty($entry->ps_percent)){
					$sum += $entry->ps_percent;
				}
			}
		}
		return $sum;
	}
}
