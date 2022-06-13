<?php

namespace common\components\grid;

use common\models\Employee;
use src\widgets\UserSelect2Widget;
use yii\base\Model;
use yii\grid\DataColumn;
use yii\helpers\Url;

/**
 * Class UserSelect2Column
 *
 * @property int $userId
 * @property string $url
 * @property string $relation
 * @property int $minimumInputLength
 * @property int $delay
 * @property string $placeholder
 * @property array $data
 *
 * Ex.
 * [
 * 'class' => \common\components\grid\UserSelect2Column::class,
 * 'attribute' => 'ugs_updated_user_id',
 * 'relation' => 'updatedUser',
 * 'url' => 'employee/list-ajax',
 * ],
 *
 */
class UserSelect2Column extends DataColumn
{
    public $format = 'userName';
    public $url;
    public $relation;
    public $minimumInputLength = 1;
    public $delay = 300;
    public $placeholder = '';
    public $id = '';
    public $data = [];


    public function init(): void
    {
        parent::init();

        if (empty($this->relation)) {
            throw new \InvalidArgumentException('relation must be set.');
        }

        if (empty($this->url)) {
            $this->url = Url::to(['/employee/list-ajax']);
        }
        $model = $this->grid->filterModel;

        //VarDumper::dump($attr, 10 , true); exit;

        if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
            $userId = (int)$model->getAttribute($this->attribute);
            if ($userId) {
                $user = Employee::find()->select(['id', 'username'])->where(['id' => $userId])->cache(3600)->one();
                if ($user) {
                    $this->data[$user->id] = $user->username . ' (' . $user->id . ')';
                }
            }
        }
    }

    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return string|null
     */
    public function getDataCellValue($model, $key, $index)
    {
        if ($model->{$this->attribute} && ($user = $model->{$this->relation})) {
            /** @var Employee $user */
            return $user->username;
        }

        return null;
    }

    /**
     * @return array|false|string|null
     * @throws \Exception
     */
    protected function renderFilterCellContent()
    {
        if (is_string($this->filter)) {
            return $this->filter;
        }

        $widgetOptions = [
            'model' => $this->grid->filterModel,
            'attribute' => $this->attribute,
            'data' => $this->data,
            'pluginOptions' => [
                'minimumInputLength' => $this->minimumInputLength,
                'ajax' => [
                    'url' => $this->url,
                    'delay' => $this->delay
                ],
            ],
            'options' => ['placeholder' => $this->placeholder],
        ];

        if (!empty($this->id)) {
            $widgetOptions['options']['id'] = $this->id;
        }

        return UserSelect2Widget::widget($widgetOptions);
    }
}
