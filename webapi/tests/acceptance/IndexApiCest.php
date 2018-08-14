<?php
namespace webapi\tests;
use yii\helpers\Url;

class IndexApiCest
{
    public function _before(NoGuy $I)
    {
    }

    public function _after(NoGuy $I)
    {
    }

    public function checkIndex(NoGuy $I)
    {
        $I->amOnPage(Url::toRoute('/site/index'));
        $I->see('API');
    }
}
