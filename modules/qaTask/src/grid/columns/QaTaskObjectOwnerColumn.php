<?php

namespace modules\qaTask\src\grid\columns;

use yii\grid\DataColumn;

/**
 * Class QaTaskObjectOwnerColumn
 *
 * @property int $minimumInputLength
 * @property int $delay
 * @property string $placeholder
 * @property array $data
 *
 * Ex.
    [
        'class' => \modules\qaTask\src\grid\columns\QaTaskObjectOwnerColumn::class,
    ],
 */
class QaTaskObjectOwnerColumn extends DataColumn
{
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
