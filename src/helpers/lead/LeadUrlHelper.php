<?php

namespace src\helpers\lead;

use common\models\Lead;
use yii\helpers\Html;
use yii\validators\UrlValidator;

class LeadUrlHelper
{
    public static function getUrl(?int $id, ?string $gid): ?string
    {
        if (!$id && !$gid) {
            return null;
        }

        if ($id && $gid) {
            return Html::a($id, \Yii::$app->params['url'] . '/lead/view/' . $gid);
        }

        if (!$id) {
            if ($lead = Lead::findOne(['gid' => $gid])) {
                return Html::a($lead->id, \Yii::$app->params['url'] . '/lead/view/' . $lead->gid);
            }
            return null;
        }

        if (!$gid) {
            if ($lead = Lead::findOne($id)) {
                return Html::a($lead->id, \Yii::$app->params['url'] . '/lead/view/' . $lead->gid);
            }
            return null;
        }

        return null;
    }

    public static function checkDeepLink(string $url): array
    {
        $attributes = [
            'tt',
            'cabin',
            'adt',
            'chd',
            'inf',
            'leadId',
            //'CID',
            //'redirectUrl'
        ];
        $dlQueryData = [];

        $summary = [
            'error' => false,
            'message' => ''
        ];

       /* $validator = new UrlValidator();

        if (!$validator->validate($url, $error)) {
            $summary['error'] = true;
            $summary['message'] = $error;
            return $summary;
        }*/

        parse_str(parse_url($url, PHP_URL_QUERY), $dlQueryData);

        if (empty($dlQueryData)) {
            $summary['error'] = true;
            $summary['message'] = 'Flight Request params not set to dl';
            return $summary;
        } else {
            foreach ($attributes as $attr) {
                if (array_key_exists($attr, $dlQueryData)) {
                    if (!strlen($dlQueryData[$attr])) {
                        $summary['error'] = true;
                        $summary['message'] = 'Flight Request param {' . $attr . '} is empty';
                        return $summary;
                    }
                } else {
                    $summary['error'] = true;
                    $summary['message'] = 'Flight Request param {' . $attr . '} missing';
                    return $summary;
                }
            }
        }

        return $summary;
    }

    public static function formatFlexOptions(int $flex, ?string $flexType): ?int
    {
        $flexExact = 0;
        $flexPlusDayAfter = 1;
        $flexPlusDayBefore = 2;
        $flexPlusMinusDay = 3;
        $flexPlusMinusTwoDays = 4;

        if ($flex == 0) {
            return $flexExact;
        }
        if (($flex == 1 || $flex == 2) && $flexType == "+") {
            return $flexPlusDayAfter;
        }
        if (($flex == 1 || $flex == 2) && $flexType == "-") {
            return $flexPlusDayBefore;
        }
        if ($flex == 1 && $flexType == "+/-") {
            return $flexPlusMinusDay;
        }
        if ($flex == 2 && $flexType == "+/-") {
            return $flexPlusMinusTwoDays;
        }

        return null;
    }
}
