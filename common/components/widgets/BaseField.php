<?php

namespace common\components\widgets;

use common\components\helpers\ArrayHelper;
use yii\widgets\ActiveField;

class BaseField extends ActiveField
{
    public function withoutContainer()
    {
        return $this->wrap(false);
    }

    public function simpleTemplate()
    {
        return $this->setTemplate('{input}');
    }
    public function readonly($readonly = true)
    {
        $this->inputOptions['readonly'] = $readonly;
        $this->inputOptions['disabled'] = $readonly;
        return $this;
    }

    public function simpleHidden($options = [])
    {
        return $this
        ->withoutContainer()
        ->simpleTemplate()
        ->hiddenInput($options)
        ;
    }

    public function simpleInput($options = [])
    {
        return $this
        ->withoutContainer()
        ->simpleTemplate()
        ->textInput($options)
        ;
    }

    public function simpleDropDown($items, $options = [])
    {
        return $this
        ->simpleTemplate()
        ->dropDownList($items, $options)
        ;
    }

    public function wrap($tag)
    {
        $this->options['tag'] = $tag;
        return $this;
    }

    public function wrapperClass($class)
    {
        $this->options['class'] = $class;
        return $this;
    }

    public function setTemplate($template = '')
    {
        $this->template = $template;
        return $this;
    }

    public function setOptions($options = [])
    {
        $this->options = ArrayHelper::merge($this->options, $options);
        return $this;
    }

    public function setFieldOptions($options = [])
    {
        $this->inputOptions = ArrayHelper::merge($this->inputOptions, $options);
        return $this;
    }
}
