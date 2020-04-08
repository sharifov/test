<?php

namespace common\components\grid;

use sales\model\emailList\entity\EmailList;
use sales\widgets\EmailSelect2Widget;
use yii\base\Model;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class EmailSelect2Column
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
        'class' => \common\components\grid\EmailSelect2Column::class,
        'attribute' => 'email_list_id',
        'relation' => 'emailList',
    ],
 *
 */
class EmailSelect2Column extends DataColumn
{
    public $url;
    public $relation;
    public $minimumInputLength = 1;
    public $delay = 300;
    public $placeholder = '';
    public $data = [];
    public $format = 'emailList';

    public function init(): void
    {
        parent::init();

        if (empty($this->relation)) {
            throw new \InvalidArgumentException('relation must be set.');
        }

        if (empty($this->url)) {
            $this->url = Url::to(['/email-list/list-ajax']);
        }

        $model = $this->grid->filterModel;

        if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
            if ($emailId = (int) $model->getAttribute($this->attribute)) {
                if ($email = EmailList::find()->select(['el_id', 'el_email'])->where(['el_id' => $emailId])->cache(3600)->one()) {
                    $this->data[$email->el_id] = $email->el_email . ' (' . $email->el_id . ')';
                }
            }
        }

        $this->options = ArrayHelper::merge(['style' => 'width:200px'], $this->options);
    }

    public function getDataCellValue($model, $key, $index)
    {
        if ($model->{$this->attribute} && ($email = $model->{$this->relation})) {
            return $email;
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

        return EmailSelect2Widget::widget($widgetOptions);
    }
}
