<?php

namespace frontend\tests\unit\modules\abac\components;

use Codeception\Test\Unit;
use common\models\Employee;
use modules\abac\components\AbacComponent;
use modules\lead\src\abac\LeadAbacObject;
use src\model\user\entity\Access;
use Yii;
use yii\base\Action;
use yii\base\Controller;

class AbacComponentTest extends Unit
{
    private array $employeesData = [];
    private ?AbacComponent $abacComponent = null;
    private array $employees = [];

    private const USER_TEST = 'abac_tester';
    private const USER_SUPERADMIN = 'superadmin';

    protected function _before()
    {
        $this->employeesData = require codecept_data_dir() . 'employee.php';
        $controller = $this->createMock(Controller::class);

        $controller->expects($this->any())
            ->method('getUniqueId')
            ->will($this->returnValue(''));

        $action = $this->createMock(Action::class);

        $action->expects($this->any())
            ->method('getUniqueId')
            ->will($this->returnValue(''));

        $controller->action = $action;

        Yii::$app->controller = $controller;

        $this->initAbacComponent();
        $this->fillEmployeeModels();
    }

    private function initAbacComponent()
    {
        $component = $this->make(AbacComponent::class, [
            'getPolicyListContent' => file_get_contents(
                codecept_data_dir() . 'abac_rules.txt'
            ),
            'cacheEnforceEnable' => false,
        ]);
        $component->init();

        $this->abacComponent = $component;
    }

    private function fillEmployeeModels()
    {
        foreach ($this->employeesData as $employeeData) {
            $access = $this->make(Access::class, [
                'getAllProjects' => $employeeData['accessData']['projects'],
                'getAllGroups' => $employeeData['accessData']['groups'],
                'getAllDepartments' => $employeeData['accessData']['departments'],
            ]);

            $employee = $this->make(Employee::class, [
                'attributes' => array_keys($employeeData),
                'getRoles' => $employeeData['roles'],
                'access' => $access
            ]);

            $employee->setAttributes($employeeData);

            $this->employees[$employeeData['username']] = $employee;
        }
    }

    private function getEmployee(string $username)
    {
        return $this->employees[$username];
    }

    public function testCreateLead(): void
    {
        self::assertTrue(
            $this->abacComponent->can(null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CREATE, $this->getEmployee(self::USER_TEST))
        );

        self::assertTrue(
            $this->abacComponent->can(null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CREATE, $this->getEmployee(self::USER_SUPERADMIN))
        );
    }

    public function testReadLead(): void
    {
        self::assertFalse(
            $this->abacComponent->can(null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_READ, $this->getEmployee(self::USER_TEST))
        );

        self::assertTrue(
            $this->abacComponent->can(null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_READ, $this->getEmployee(self::USER_SUPERADMIN))
        );
    }

    public function testDeleteLead(): void
    {
        self::assertTrue(
            $this->abacComponent->can(null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_DELETE, $this->getEmployee(self::USER_TEST))
        );

        self::assertTrue(
            $this->abacComponent->can(null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_DELETE, $this->getEmployee(self::USER_SUPERADMIN))
        );
    }

    public function testUnmaskLead(): void
    {
        self::assertFalse(
            $this->abacComponent->can(null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_UNMASK, $this->getEmployee(self::USER_TEST))
        );

        self::assertTrue(
            $this->abacComponent->can(null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_UNMASK, $this->getEmployee(self::USER_SUPERADMIN))
        );
    }

    public function testCloneLead(): void
    {
        self::assertTrue(
            $this->abacComponent->can(null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CLONE, $this->getEmployee(self::USER_TEST))
        );

        self::assertTrue(
            $this->abacComponent->can(null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CLONE, $this->getEmployee(self::USER_SUPERADMIN))
        );
    }
}
