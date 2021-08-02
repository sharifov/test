<?php

namespace common\components\grid;

use common\models\Employee;
use sales\widgets\UserSelect2Widget;
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
    [
        'class' => \common\components\grid\UserSelect2Column::class,
        'attribute' => 'ugs_updated_user_id',
        'relation' => 'updatedUser',
        'url' => 'employee/list-ajax',
    ],
 *
 */
class OwnerSelect2Column extends DataColumn
{
    public $format = 'userName';
    public $url;
    public $relation;
    public $minimumInputLength = 1;
    public $delay = 300;
    public $placeholder = '';
    public $data = [];


    public function init(): void
    {
        parent::init();

        $model = $this->grid->filterModel;

        $this->attribute = 'objectOwner';
        $this->format = 'raw';
        $this->filter = \sales\widgets\UserSelect2Widget::widget([
            'model' => $model,
            'attribute' => 'objectOwner',
            'placeholder' => 'Select User',
        ]);
    }

    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return string|null
     */
    public function getDataCellValue($model, $key, $index)
    {
        return \Yii::$app->formatter->asUserName($model->getOwner());
    }
}
