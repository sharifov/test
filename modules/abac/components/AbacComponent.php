<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 20/11/2019
 * Time: 09:50 AM
 */

namespace modules\abac\components;

use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLog;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLogStatus;
use modules\hotel\src\helpers\HotelApiDataHelper;
use modules\hotel\src\helpers\HotelApiMessageHelper;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class AbacComponent
 * @package modules\abac\components
 *
 * @property array $modules
 * @property Request $request
 */

class AbacComponent extends Component
{
    public array $modules = [];

    public function init(): void
    {
        parent::init();
        //$this->initRequest();
    }

    public function getObjectList(): array
    {
        $objectList = [];
        if ($this->modules) {
            /** @var \modules\abac\src\entities\AbacInterface $module */
            foreach ($this->modules as $module) {
                $objects = $module::getObjectList();
                if ($objects) {
                    $objectList = array_merge($objectList, $objects);
                }
            }
        }
        return $objectList;
    }


}
