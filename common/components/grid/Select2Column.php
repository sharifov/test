<?php

namespace common\components\grid;

use kartik\select2\Select2;
use yii\grid\DataColumn;

class Select2Column extends DataColumn
{
    public array $data = [];

    public string $placeholder = '';

    public string $size = Select2::SIZE_SMALL;

    public string $id = '';

    public array $pluginOptions = [];

    public function init(): void
    {
        parent::init();
    }

    protected function renderFilterCellContent()
    {
        if (is_string($this->filter)) {
            return $this->filter;
        }

        if ($this->filter === false) {
            return parent::renderFilterCellContent();
        }

        $widgetOptions = [
            'model' => $this->grid->filterModel,
            'attribute' => $this->attribute,
            'data' => $this->data,
            'options' => ['placeholder' => $this->placeholder, 'id' => $this->id],
            'size' => $this->size,
            'pluginOptions' => $this->pluginOptions
        ];

        return Select2::widget($widgetOptions);
    }
}
