<?php

namespace frontend\widgets\infoBlock;

use common\models\InfoBlock;
use yii\base\Widget;

class InfoBlockWidget extends Widget
{
    public string $key = '';
    public string $btnEl = '#js-info_block_btn';

    public function run(): string
    {
        $model = InfoBlock::find()
            ->andWhere(['ib_key' => $this->key])
            ->andWhere(['ib_enabled' => true])
            ->limit(1)
            ->one();

        return $this->render('info-block', [
            'model' => $model,
            'btnEl' => $this->btnEl,
            'key' => $this->key
        ]);
    }
}
