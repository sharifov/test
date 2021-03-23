<?php

namespace webapi\tests;

use webapi\fixtures\UserFixture;
use yii\helpers\Url;

class IndexApiCest
{
    public const API_USERNAME = 'test';
    public const API_PASSWORD = 'test123';

//    /**
//     * @param \webapi\tests\ApiTester $I
//     */
//    public function _before(ApiTester $I): void
//    {
//    }
//
//    /**
//     * @param \webapi\tests\ApiTester $I
//     */
//    public function _after(ApiTester $I): void
//    {
//    }

//    public function checkIndex(NoGuy $I)
//    {
//        $I->amOnPage(Url::toRoute('/site/index'));
//        $I->see('API');
//    }

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


//    public function _before(ApiTester $I)
//    {
//        $I->haveFixtures([
//            'user' => [
//                'class' => UserFixture::class,
//                'dataFile' => codecept_data_dir() . 'user.php'
//            ]
//        ]);
//    }


//    /**
//     * @param \webapi\tests\ApiTester $I
//     * @param \Codeception\Example $example
//     * @example ["/v1/site/test", 200]
//     * @example ["/v2/site/test", 200]
//     * @example ["/v1/site/index", 200]
//     * @example ["/v2/site/index", 200]
//     */
//    public function checkEndpoints(ApiTester $I, \Codeception\Example $example): void
//    {
//        $I->wantTo('[GET] Check Endpoint and Response Code');
//        $I->sendGet($example[0]);
//        $I->seeResponseCodeIs($example[1]);
//    }

    /**
     * @param \webapi\tests\ApiTester $I
     * @param \Codeception\Example $example
     * @example ["Sales"]

     */
    public function getDepartmentPhoneProject(ApiTester $I, \Codeception\Example $example): void
    {
        $I->wantTo('[POST] Get Department Phone Project');
        $I->amHttpAuthenticated(self::API_USERNAME, self::API_PASSWORD);
        $I->haveHttpHeader('Accept-Encoding', 'Accept-Encoding: gzip, deflate');

        echo Url::toRoute('/v2/department-phone-project/get');
        exit;

        $I->sendPost(
            Url::toRoute('/v2/department-phone-project/get'),
            [
            'project_id' => 6,
            'department' => $example[0]
            ]
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);

        $I->seeResponseJsonMatchesJsonPath('$.data.phones');
        //$I->seeResponseJsonMatchesJsonPath('$.data.phones[*].phone');


        //$I->seeResponseJsonMatchesJsonPath('$.store..price');

        $I->seeResponseContainsJson([
            'status' => 200,
            'message' => 'OK',
            'data' => [
                'phones' => [
                ]
            ],
            'technical' => [],
            'request' => [],
        ]);
        //$I->seeResponseContainsJson(['data' => []]);
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
