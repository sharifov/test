<?php
/**
 * @author alex.connor@techork.com
 */

namespace sales\helpers\communication;

use common\models\Call;
use Yii;

class CommunicationHelper
{

    /**
     * @param Call[]|null $callList
     */
    public static function renderChildCallsRecursive(?array $callList): void
    {
        echo Yii::$app->controller->renderPartial('/lead/communication/_list_call_recursive', ['callList' => $callList]);
    }

}
