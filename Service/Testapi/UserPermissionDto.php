<?php

namespace Service\Testapi;

use Service\Dto;

class UserPermissionDto extends Dto
{
    const COMMENT = 'comment';
    const UPLOAD_PHOTO = 'upload photo';
    const ADD_EVENT = 'add event';

    public string $id;
    public string $permission;

    public function addPermission(string $permission)
    {
        $dto = new self();
        $dto->id = $this->count() + 1;
        $dto->permission = $permission;
        $this->add($dto);
    }
}