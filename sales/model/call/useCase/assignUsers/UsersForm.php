<?php

namespace sales\model\call\useCase\assignUsers;

use common\models\Employee;
use yii\base\Model;
use common\components\validators\IsArrayValidator;

/**
 * Class UsersForm
 *
 * @property $selectedUsers
 * @property Employee[] $users
 */
class UsersForm extends Model
{
    public $selectedUsers;

    private array $users;

    /**
     * @param Employee[] $users
     * @param array $config
     */
    public function __construct(array $users, $config = [])
    {
        parent::__construct($config);
        $this->users = $users;
    }

    public function rules(): array
    {
        return [
            ['selectedUsers', 'usersRequiredValidate', 'skipOnEmpty' => false],
            ['selectedUsers', IsArrayValidator::class],
            ['selectedUsers', 'each', 'rule' => ['filter', 'filter' => 'intval'], 'skipOnEmpty' => true, 'skipOnError' => true],
            ['selectedUsers', 'usersValidate', 'skipOnError' => true],
        ];
    }

    public function usersRequiredValidate(): void
    {
        if (!$this->selectedUsers) {
            $this->addError('selectedUsers', 'Users can not be blank.');
        }
    }

    public function usersValidate(): void
    {
        foreach ($this->selectedUsers as $userId) {
            if (!in_array($userId, $this->getUsersIds(), true)) {
                $this->addError('selectedUsers', 'Invalid selected user');
                return;
            }
        }
    }

    private function getUsersIds(): array
    {
        return array_keys($this->users);
    }

    public function getRenderedUsers(): array
    {
        $users = [];
        foreach ($this->users as $user) {
            $users[$user->id] = UserRenderer::render($user);
        }
        return $users;
    }
}
