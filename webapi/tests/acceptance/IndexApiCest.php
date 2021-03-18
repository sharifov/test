<?php

namespace webapi\tests;

use yii\helpers\Url;

class IndexApiCest
{
//    public function _before(NoGuy $I)
//    {
//    }
//
//    public function _after(NoGuy $I)
//    {
//    }

    public function checkIndex(NoGuy $I)
    {
        $I->amOnPage(Url::toRoute('/site/index'));
        $I->see('API');
    }

//    public function checkV1TestPage(NoGuy $I)
//    {
//        $I->amOnPage(Url::toRoute('/v1/site/test'));
//        //$I->see('API');
//    }
//
//    public function checkV2TestPage(NoGuy $I)
//    {
//        $I->amOnPage(Url::toRoute('/v2/site/test'));
//        //$I->see('API');
//    }


    /**
     * @example ["/v1/site/test", 200]
     * @example ["/v2/site/test", 200]
     */
    public function checkEndpoints(AcceptanceTester $I, \Codeception\Example $example)
    {
        $I->wantTo('Check any Endpoints (GET) actions and Response Code');
        $I->sendGet($example[0]);
        $I->seeResponseCodeIs($example[1]);
    }

//    /**
//     * @dataProvider pageProvider
//     */
//    public function staticPages(AcceptanceTester $I, \Codeception\Example $example)
//    {
//        $I->amOnPage($example['url']);
//        $I->see($example['title'], 'h1');
//        $I->seeInTitle($example['title']);
//    }
//
//    /**
//     * @return array
//     */
//    protected function pageProvider() // alternatively, if you want the function to be public, be sure to prefix it with `_`
//    {
//        return [
//            ['url'=>"/", 'title'=>"Welcome"],
//            ['url'=>"/info", 'title'=>"Info"],
//            ['url'=>"/about", 'title'=>"About Us"],
//            ['url'=>"/contact", 'title'=>"Contact Us"]
//        ];
//    }


//    public function checkV2TestPagePost(AcceptanceTester $I)
//    {
//        $I->amOnPage(Url::toRoute('/v2/site/test'));
//
//        //$I->amHttpAuthenticated('service_user', '123456');
//        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
//        $I->sendPost(Url::toRoute('/v2/site/test'), [
//            'name' => 'davert',
//            'email' => 'davert@codeception.com'
//        ]);
//        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
//        $I->seeResponseIsJson();
//        //$I->seeResponseContains('{"result":"ok"}');
//
//        //$I->see('API');
//    }
}
