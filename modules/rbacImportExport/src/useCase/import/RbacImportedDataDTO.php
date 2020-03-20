<?php
namespace modules\rbacImportExport\src\useCase\import;

class RbacImportedDataDTO
{
	public $roles;

	public $rules;

	public $permissions;

	public function __construct(array $data)
	{
		$this->roles = $data['roles'] ?? [];
		$this->rules = $data['rules'] ?? [];
		$this->permissions = $data['permissions'] ?? [];
	}
}