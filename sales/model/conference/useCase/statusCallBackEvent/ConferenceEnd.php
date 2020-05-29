<?php

namespace sales\model\conference\useCase\statusCallBackEvent;

use common\models\Conference;
use Yii;
use yii\helpers\VarDumper;

class ConferenceEnd
{
    private Conference $conference;

    public function __construct(Conference $conference)
    {
        $this->conference = $conference;
    }

    public function __invoke()
    {
        $conference = $this->conference;
        $conference->end();
        if (!$conference->save()) {
            Yii::error(VarDumper::dumpAsString([
                'errors' => $conference->getErrors(),
                'model' => $conference->getAttributes(),
            ]), static::class);
        }
    }
}
