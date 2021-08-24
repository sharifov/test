<?php

namespace frontend\widgets;

use sales\helpers\setting\SettingHelper;
use Yii;

/**
 * YandexMetrikaWidget widget
 *
 * @author Alexandr <alex.connor@techork.com>
 */
class YandexMetrikaWidget extends \yii\bootstrap\Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {

        $ym = SettingHelper::getYandexMetrika();

        if ($ym && !empty($ym['counterId']) && !empty($ym['enabled'])) {
            $params = [];
            if (isset($ym['clickmap'])) {
                $params['clickmap'] = (bool) $ym['clickmap'];
            }

            if (isset($ym['trackLinks'])) {
                $params['trackLinks'] = (bool) $ym['trackLinks'];
            }

            if (isset($ym['accurateTrackBounce'])) {
                $params['accurateTrackBounce'] = (bool) $ym['accurateTrackBounce'];
            }

            if (isset($ym['webvisor'])) {
                $params['webvisor'] = (bool) $ym['webvisor'];
            }

            if (isset($ym['trackHash'])) {
                $params['trackHash'] = (bool) $ym['trackHash'];
            }

            $counterId = (int) $ym['counterId'];
            return $this->render('yandex_metrika', ['counterId' => $counterId, 'params' => $params]);
        }
    }
}
