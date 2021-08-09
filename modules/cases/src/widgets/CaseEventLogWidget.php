<?php

namespace modules\cases\src\widgets;

use sales\entities\cases\CaseEventLogSearch;
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use Yii;
use yii\helpers\Url;

class CaseEventLogWidget extends Widget
{
    public $url;
    public $case_id;

    public function init()
    {
        // your logic here
        parent::init();
    }
    public function run()
    {
        // you can load & return the view or you can return the output variable

        return $this->render('event-log-widget', [
            'url' => $this->url,
            'case_id' => $this->case_id
        ]);
    }

    public static function initByCase(int $id): string
    {
        return self::widget([
            'case_id' => $id,
            'url' => Url::to(['/cases/ajax-case-event-log']),
        ]);
    }
}
