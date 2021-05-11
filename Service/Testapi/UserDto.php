<?php

namespace Service\Testapi;

use Service\Dto;

class UserDto extends Dto
{
    public string $id;
    public bool $blocked;
    public int $created_at;
    public string $name;
    public UserPermissionDto $permissions;

    public function loadArray(array $data): void
    {
        parent::loadArray($data);
        $this->permissions = UserPermissionDto::fromArray($data['permissions']);
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['permissions'] = $this->permissions->toArray();
        return $data;
    }
}