<?php

namespace sales\yii\grid;

use yii\grid\DataColumn;

/**
 * Renders several attributes in one grid column
 */
class CombinedDataColumn extends DataColumn
{
    /* @var $labelTemplate string */
    public ?string $labelTemplate = null;

    /* @var $valueTemplate string */
    public ?string $valueTemplate = null;

    /* @var $attributes string[] | null */
    public ?array $attributes = null;

    /* @var $formats string[] | null */
    public ?array $formats = null;

    /* @var $values string[] | null */
    public ?array $values = null;

    /* @var $labels string[] | null */
    public ?array $labels = null;

    /* @var $sortLinksOptions string[] | null */
    public ?array $sortLinksOptions = null;


    /**
     * Sets parent object parameters for current attribute
     * @param $key string Key of current attribute
     * @param $attribute string Current attribute
     */
    protected function setParameters($key, $attribute): void
    {
        [$attribute, $format] = array_pad(explode(':', $attribute), 2, null);

        $this->attribute = $attribute;

        if (isset($format)) {
            $this->format = $format;
        } elseif (isset($this->formats[$key])) {
            $this->format = $this->formats[$key];
        } else {
            $this->format = null;
        }

        $this->label = $this->labels[$key] ?? null;

        $this->sortLinkOptions = $this->sortLinksOptions[$key] ?? [];

        $this->value = $this->values[$key] ?? null;
    }

    /**
     * Sets parent object parameters and calls parent method for each attribute, then renders combined cell content
     * @inheritdoc
     */
    protected function renderHeaderCellContent()
    {
        if (!is_array($this->attributes)) {
            return parent::renderHeaderCellContent();
        }

        $labels = [];
        foreach ($this->attributes as $i => $attribute) {
            $this->setParameters($i, $attribute);
            $labels['{'.$i.'}'] = parent::renderHeaderCellContent();
        }

        if ($this->labelTemplate === null) {
            return implode('<br>', $labels);
        }

        return strtr($this->labelTemplate, $labels);
    }

    /**
     * Sets parent object parameters and calls parent method for each attribute, then renders combined cell content
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if (!is_array($this->attributes)) {
            return parent::renderDataCellContent($model, $key, $index);
        }

        $values = [];
        foreach ($this->attributes as $i => $attribute) {
            $this->setParameters($i, $attribute);
            $values['{'.$i.'}'] = parent::renderDataCellContent($model, $key, $index);
        }

        if ($this->valueTemplate === null) {
            return implode('<br>', $values);
        }

        return strtr($this->valueTemplate, $values);
    }
}