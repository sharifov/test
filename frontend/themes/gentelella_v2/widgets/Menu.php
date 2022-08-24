<?php

/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace frontend\themes\gentelella_v2\widgets;

use rmrevin\yii\fontawesome\component\Icon;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Menu extends \yii\widgets\Menu
{
    /**
     * @inheritdoc
     */
    public $labelTemplate = '{label}';

    /**
     * @inheritdoc
     */
    public $linkTemplate = '<a href="{url}" {attributes}>{icon}<span>{label}</span>{badge}</a>';

    /**
     * @inheritdoc
     */
    public $submenuTemplate = "\n<ul class=\"nav child_menu\">\n{items}\n</ul>\n";

    /**
     * @inheritdoc
     */
    public $activateParents = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        Html::addCssClass($this->options, 'nav side-menu');
        parent::init();
        $this->ensureVisibility($this->items);
    }

    /**
     * @param array $items
     *
     * @return bool
     */
    protected function ensureVisibility(&$items)
    {
        $allVisible = false;

        foreach ($items as &$item) {
            if (isset($item['url']) and !isset($item['visible']) and !in_array($item['url'], ['', '#', 'javascript:'])) {
                $item['visible'] = \Yii::$app->user->can($item['url']);
            }

            if (isset($item['items'])) {
                // If not children are visible - make invisible this node
                if (!$this->ensureVisibility($item['items']) and !isset($item['visible'])) {
                    $item['visible'] = false;
                }
            }

            if (isset($item['label']) and ( !isset($item['visible']) or $item['visible'] === true )) {
                $allVisible = true;
            }
        }

        return $allVisible;
    }

    /**
     * @inheritdoc
     */
    protected function renderItem($item)
    {
        $renderedItem = parent::renderItem($item);
        if (isset($item['badge'])) {
            $badgeOptions = ArrayHelper::getValue($item, 'badgeOptions', []);
            Html::addCssClass($badgeOptions, 'label pull-right');
        } else {
            $badgeOptions = null;
        }

        if (isset($item['attributes']) && is_array($item['attributes'])) {
            $attributes = ' ';
            foreach ($item['attributes'] as $attribute => $value) {
                $attributes .= "{$attribute}='{$value}' ";
            }
        }
        return strtr(
            $renderedItem,
            [
                '{icon}' => isset($item['icon'])
                    ? Html::tag('i', '', ['class' => ($item['iconPrefix'] ?? 'fa') . ' fa-' . $item['icon']])

                    //new Icon($item['iconPrefix'] ?? 'fa', $item['icon'])
            //                    ? new Icon($item['icon'], ArrayHelper::getValue($item, 'iconOptions', []))
                    : '',
                '{badge}' => (
                    isset($item['badge'])
                        ? Html::tag('small', $item['badge'], $badgeOptions)
                        : ''
                    ) . (
                    isset($item['items']) && count($item['items']) > 0
                        ?
                        Html::tag('span', '', ['class' => 'fa fa-chevron-down'])
                        //(new Icon('fa','chevron-down', ['tag' => 'span']))
            //                      ? (new Icon('chevron-down'))->tag('span')
                        : ''
                    ),
                '{attributes}' => $attributes ?? ''
            ]
        );
    }
}
