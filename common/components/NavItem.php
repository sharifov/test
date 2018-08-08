<?php
namespace common\components;

use common\models\ExpertSale;
use common\models\Lead;
use common\models\SaleSale;
use common\models\SourcePermission;
use common\models\SupportSale;
use common\models\Team;
use common\models\TicketSale;
use common\models\VerificationSale;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

class NavItem
{
    public static function items(&$menuItems)
    {
        if (!Yii::$app->user->isGuest) {

            if (!in_array(Yii::$app->user->identity->role, ['admin', 'supervision'])) {
                $items = [
                    [
                        'label' => 'Dashboard',
                        'url' => ['site/index']
                    ],
                ];
            } else {
                $items = [
                    [
                        'label' => 'Dashboard',
                        'url' => (!strpos(Yii::$app->request->baseUrl, 'admin'))
                            ? ['admin/site/index']
                            : ['site/index']
                    ],
                    [
                        'label' => 'Employees',
                        'url' => (!strpos(Yii::$app->request->baseUrl, 'admin'))
                            ? ['admin/employee/list']
                            : ['employee/list']
                    ],
                    [
                        'label' => 'Settings',
                        'url' => ['#']
                    ],
                ];
            }
            $items[] = ['label' => 'Search Order', 'url' => ['search/index']];
            $menuItems[] = [
                'label' => '<i class="fa fa-bars"></i> Menu',
                'items' => $items,
            ];
            if (Yii::$app->user->identity->role != 'coach') {
                $badges = Lead::getBadges();
                $menuItems[] = '<li class="' . self::isActive('inbox') . '">'
                    . Html::a('Inbox<span id="inbox-queue" class="badge badge-info">' . $badges['inbox'] . '</span > ', self::getQueueUri('inbox'))
                    . ' </li > ';
                $menuItems[] = '<li class="' . self::isActive('follow-up') . '">'
                    . Html::a('Follow Up<span class="badge badge-success">' . $badges['follow-up'] . '</span > ', self::getQueueUri('follow-up'))
                    . ' </li > ';
                $menuItems[] = '<li class="' . self::isActive('processing') . '">'
                    . Html::a('Processing(Me)<span class="badge badge-warning">' . $badges['processing'] . '</span > ', self::getQueueUri('processing'))
                    . ' </li > ';

                if (Yii::$app->user->identity->role != 'agent') {
                    $menuItems[] = '<li class="' . self::isActive('processing-all') . '">'
                        . Html::a('Processing(All)<span class="badge badge-mint">' . $badges['processing-all'] . '</span > ', self::getQueueUri('processing-all'))
                        . ' </li > ';
                }

                $menuItems[] = '<li class="' . self::isActive('booked') . '">'
                    . Html::a('Booked<span class="badge badge-success">' . $badges['booked'] . '</span > ', self::getQueueUri('booked'))
                    . ' </li > ';
                $menuItems[] = '<li class="' . self::isActive('sold') . '">'
                    . Html::a('Sold<span class="badge badge-success">' . $badges['sold'] . '</span > ', self::getQueueUri('sold'))
                    . ' </li > ';

                if (Yii::$app->user->identity->role != 'agent') {
                    $menuItems[] = '<li class="' . self::isActive('trash') . '">'
                        . Html::a('Trash<span class="badge badge-warning">' . $badges['trash'] . '</span > ', self::getQueueUri('trash'))
                        . ' </li > ';
                }
            }
        }
    }

    private static function isActive($type)
    {
        if ($type == Yii::$app->request->get('type') &&
            Yii::$app->controller->action->id = 'queue'
        ) {
            return 'active';
        }
        return '';
    }

    private static function getQueueUri($type)
    {
        return sprintf('%s/queue/%s', Yii::$app->urlManager->getHostInfo(), $type);
    }

    private static function isActiveSubmenu($team, $teamName)
    {
        return ($team == $teamName);
    }
}