<?php

namespace webapi\tests\forms;

use common\models\Project;
use Faker\Factory;
use Faker\Generator;
use sales\entities\cases\CaseCategory;
use sales\model\cases\useCases\cases\api\create\CreateForm;

/**
 * Class CaseCreateFormValidationTest
 * @package webapi\tests\forms
 *
 * @property-read int|null $caseCategoryId
 * @property-read int|null $projectId
 * @property-read Generator $faker
 */
class CaseCreateFormValidationTest extends \Codeception\Test\Unit
{
    /**
     * @var \webapi\tests\UnitTester
     */
    protected $tester;

    private $caseCategoryId;

    private $projectId;

    private $faker;

    protected function _before()
    {
        $this->caseCategoryId = (CaseCategory::find()->select(['cc_id'])->asArray()->one())['cc_id'] ?? null;
        $this->projectId = Project::find()->select(['id'])->asArray()->one()['id'] ?? null;

        $this->faker = Factory::create();
    }

    protected function _after()
    {
    }

    // tests
    public function testWithoutRequiredFields()
    {
        $form = $this->setupForm();

        $this->assertFalse($form->validate());
    }

    public function testWithEmail()
    {
        $form = $this->setupForm();
        $form->contact_email = $this->faker->email;

        $this->assertTrue($form->validate());
    }

    public function testWithPhone()
    {
        $form = $this->setupForm();
        $form->contact_phone = $this->faker->phoneNumber;

        $this->assertTrue($form->validate());
    }

    public function testWithChatVisitorId()
    {
        $form = $this->setupForm();
        $form->chat_visitor_id = $this->faker->regexify('[A-Za-z0-9]{20}');

        $this->assertTrue($form->validate());
    }

    public function testWithInvalidChatVisitorId()
    {
        $form = $this->setupForm();
        $form->chat_visitor_id = $this->faker->regexify('[A-Za-z0-9]{51}');

        $this->assertFalse($form->validate());
    }

    private function setupForm()
    {
        $form = new CreateForm($this->projectId);
        $form->category_id = $this->caseCategoryId;
        return $form;
    }
}
