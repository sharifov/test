<?php

namespace modules\order\src\processManager\queue;

use yii\helpers\VarDumper;

class DummyQueue implements Queue
{
    public function push($job): ?string
    {
        $id = rand(0, 10);
        \Yii::info([
            'message' => 'Pushed Job',
            'id' => $id,
            'job' => VarDumper::dumpAsString($job),
        ], 'info\ProcessManagerQueue');
        return $id;
    }

    public function delay($value)
    {
        \Yii::info([
            'message' => 'Added delay',
            'value' => $value,
        ], 'info\ProcessManagerQueue');
        return $this;
    }

    public function priority($value)
    {
        \Yii::info([
            'message' => 'Added priority',
            'value' => $value,
        ], 'info\ProcessManagerQueue');
        return $this;
    }
}
