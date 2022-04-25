<?php

namespace frontend\widgets;

use yii\base\Widget;

class ShowMoreFieldWidget extends Widget
{
    public string $title = '';

    public function run(): string
    {
        return $this->render('show_more', [
            'title' => $this->title,
            'view' => $this->view
        ]);
    }

    public static function addLinkToShowMore(&$truncateMessage, $originMessage, $elementId)
    {
        if (substr($truncateMessage, -3) == '...') {
            $truncateMessage .= '<div class="detail_' . $elementId . '" style="display: none;">' . $originMessage . '</div>';
            $truncateMessage .= ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $elementId . '"></i>';
        }
        return $truncateMessage;
    }
}
