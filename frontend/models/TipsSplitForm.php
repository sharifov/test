<?php

namespace frontend\models;

use yii\base\Model;
use common\models\Lead;
use common\models\TipsSplit;
use yii\web\BadRequestHttpException;

/**
 * TipsSplitForm form
 */
class TipsSplitForm extends Model
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
     * @var $_tipsSplit TipsSplit[]
     */
    private $_tipsSplit;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Lead'], 'required'],
            [['TipsSplit'], 'checkSumPercentage'],
            [['TipsSplit'], 'checkMainAgent'],
            [['TipsSplit'], 'safe'],
        ];
    }

    public function checkSumPercentage($attribute, $params)
    {
        $profitSplit = $this->getTipsSplit();
        if(!empty($profitSplit)){
            $sum = 0;
            foreach ($profitSplit as $entry){
                if(!empty($entry->ts_percent)){
                    $sum += $entry->ts_percent;
                }
            }
        }
        if($sum > 100){
            $this->addError('sumPercent', \Yii::t('user', 'Sum of percent more than 100'));
            return false;
        }elseif($sum == 100){
            $this->addError('sumPercent', \Yii::t('user', 'Sum of percent is 100. The main agent was left without profit.'));
            return false;
        }
        return true;
    }

    public function checkMainAgent($attribute, $params)
    {
        //var_dump($attribute);die;
        $employee = $this->getLead()->employee;
        $profitSplit = $this->getTipsSplit();
        if(!empty($profitSplit)){
            foreach ($profitSplit as $entry){
                if(!empty($entry->ts_user_id) && $employee->id == $entry->ts_user_id){
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
       // $this->setTipsSplit([(new TipsSplit())]);
        if(!empty($lead->tipsSplits)){
            $this->setTipsSplit($lead->tipsSplits);
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

        if (!$this->hasErrors('TipsSplit') && $this->hasErrors('TipsSplit')) {
            $this->clearErrors('TipsSplit');
        }

        parent::afterValidate();
    }

    private function getAllModels()
    {
        $models = [
            'Lead' => $this->getLead(),
        ];
        $profitSplit = $this->getTipsSplit();
        if(!empty($profitSplit)){
            foreach ($profitSplit as $id => $split) {
                $models['TipsSplit'][$id] = $profitSplit[$id];
            }
        }else{
            $models['TipsSplit'][] = new TipsSplit();
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
     * @return TipsSplit[]
     */
    public function getTipsSplit()
    {
        return $this->_tipsSplit;
    }

    /**
     * @param \common\models\Lead $_lead
     */
    public function setLead($_lead)
    {
        $this->_lead = $_lead;
    }

    /**
     * @param TipsSplit[]  $_tipsSplit
     */
    public function setTipsSplit($_tipsSplit)
    {
        $this->_tipsSplit = [];

        foreach ($_tipsSplit as $key => $tSplit) {
            if (!$tSplit->isNewRecord) {
                $this->_tipsSplit[$tSplit->ts_id] = $tSplit;
            } else {
                $tSplit->ts_lead_id = $this->_lead->id;
                $this->_tipsSplit[$key] = $tSplit;
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
            if(!empty($this->getTipsSplit())){
                foreach ($this->getTipsSplit() as $key => $split) {
                    $split->ts_lead_id = $lead->id;
                    if (!$split->save()) {
                        $hasErrors = true;
                        $errors['TipsSplit'][$key] = $split->getErrors();
                    }
                    $keep[] = $split->ts_id;
                }
            }

            $query = TipsSplit::find()->andWhere(['ts_lead_id' => $lead->id]);
            if ($keep) {
                $query->andWhere(['NOT IN', 'ts_id', $keep]);
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
                if (in_array($modelName, ['TipsSplit'])) {
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
            'TipsSplit' => 'common\models\TipsSplit',
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
        if ($model == 'TipsSplit') {
            $this->setTipsSplit($modelsPopulate);
        }
    }

}
