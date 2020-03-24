<?php

namespace sales\yii\grid;

use sales\model\phoneList\entity\PhoneList;
use sales\widgets\PhoneSelect2Widget;
use yii\base\Model;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class PhoneSelect2Column
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
        'class' => \sales\yii\grid\PhoneSelect2Column::class,
        'attribute' => 'phone_list_id',
        'relation' => 'phoneList',
    ],
 *
 */
class PhoneSelect2Column extends DataColumn
{
    public $url;
    public $relation;
    public $minimumInputLength = 1;
    public $delay = 300;
    public $placeholder = '';
    public $data = [];

    public function init(): void
    {
        parent::init();

        if (empty($this->relation)) {
            throw new \InvalidArgumentException('relation must be set.');
        }

        if (empty($this->url)) {
            $this->url = Url::to(['/phone-list/list-ajax']);
        }

        $model = $this->grid->filterModel;

        if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
            if ($phoneId = (int) $model->getAttribute($this->attribute)) {
                if ($phone = PhoneList::find()->select(['pl_id', 'pl_phone_number'])->where(['pl_id' => $phoneId])->cache(3600)->one()) {
                    $this->data[$phone->pl_id] = $phone->pl_phone_number . ' (' . $phone->pl_id . ')';
                }
            }
        }

        $this->options = ArrayHelper::merge(['style' => 'width:200px'], $this->options);
    }

    public function getDataCellValue($model, $key, $index): ?string
    {
        if ($model->{$this->attribute} && ($phone = $model->{$this->relation})) {
            /** @var PhoneList $phone */
            return $phone->pl_phone_number;
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

        return PhoneSelect2Widget::widget($widgetOptions);
    }
}
