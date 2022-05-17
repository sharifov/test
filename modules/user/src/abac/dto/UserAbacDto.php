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

    public function __construct(?string $attributeName = null)
    {
        if ($attributeName) {
            $this->formAttribute = $attributeName;
            $this->formMultiAttribute[0] = $attributeName;
        }
    }

    public static function createForUpdate(
        string $attributeName,
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
}
