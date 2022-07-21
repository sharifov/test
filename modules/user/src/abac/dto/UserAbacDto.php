<?php

namespace modules\user\src\abac\dto;

use stdClass;

class UserAbacDto extends stdClass
{
    public string $formAttribute = '';
    public array $formMultiAttribute = [];
    public ?bool $isNewRecord = null;
    public ?bool $targetUserIsSameUser = null;
    public ?bool $targetUserIsSameGroup = null;
    public ?bool $targetUserIsSameDepartment = null;
    public ?string $targetUserUsername = null;
    public array $targetUserRoles = [];
    public array $targetUserProjects = [];
    public array $targetUserGroups = [];
    public array $targetUserDepartments = [];
    public array $selectedRoles = [];

    public function __construct(?string $attributeName = null)
    {
        if ($attributeName) {
            $this->formAttribute = $attributeName;
            $this->formMultiAttribute[0] = $attributeName;
        }
    }

    public static function createForUpdate(
        ?string $attributeName,
        bool $targetUserIsSameUser,
        bool $targetUserIsSameGroup,
        bool $targetUserIsSameDepartment,
        string $targetUserUsername,
        array $targetUserRoles,
        array $targetUserProjects,
        array $targetUserGroups,
        array $targetUserDepartments
    ): self {
        $dto = new self($attributeName);
        $dto->isNewRecord = false;
        $dto->targetUserIsSameUser = $targetUserIsSameUser;
        $dto->targetUserIsSameGroup = $targetUserIsSameGroup;
        $dto->targetUserIsSameDepartment = $targetUserIsSameDepartment;
        $dto->targetUserUsername = $targetUserUsername;
        $dto->targetUserRoles = $targetUserRoles;
        $dto->targetUserProjects = $targetUserProjects;
        $dto->targetUserGroups = $targetUserGroups;
        $dto->targetUserDepartments = $targetUserDepartments;
        return $dto;
    }

    public static function createForValidationRole(
        bool $targetUserIsSameUser,
        bool $targetUserIsSameGroup,
        bool $targetUserIsSameDepartment,
        string $targetUserUsername,
        array $targetUserRoles,
        array $targetUserProjects,
        array $targetUserGroups,
        array $targetUserDepartments,
        string $selectedRole,
    ): self {
        $dto = self::createForUpdate(
            'form_roles',
            $targetUserIsSameUser,
            $targetUserIsSameGroup,
            $targetUserIsSameDepartment,
            $targetUserUsername,
            $targetUserRoles,
            $targetUserProjects,
            $targetUserGroups,
            $targetUserDepartments,
        );
        $dto->selectedRoles = [$selectedRole];
        return $dto;
    }
}
