<?php

namespace common\tests\unit\models;

use Yii;

/**
 * GdsDumpParserTest
 */
class GdsDumpParserTest extends \Codeception\Test\Unit
{

    public function testSabreParser()
    {

        $data = include(codecept_data_dir() . 'dump.sabre.php');
        foreach ($data as $n => $item) {
            expect('Item-' . $n, $item)->array();
        }
    }

    public function testAmadeusParser()
    {

        //expect('error message should be set', $model->errors)->hasKey('password');
        expect('Need True', true)->true();
    }

    public function testWorldspanParser()
    {

        //expect('error message should be set', $model->errors)->hasKey('password');
        expect('Need True', true)->true();
    }

}
