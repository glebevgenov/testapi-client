<?php

namespace Application;

use Exception;
use Service\Testapi\TestapiException;
use Service\Testapi\UserPermissionDto;

class Task
{
    public function run(): void {
        try {
            $this->updateUser();
        } catch(Exception $e) {
            do {
                printf("%s:%d %s %d [%s]\n", $e->getFile(), $e->getLine(), $e->getMessage(), $e->getCode(), get_class($e));
            } while ($e = $e->getPrevious());
        }
    }

    /**
     * @throws TestapiException
     */
    private function updateUser(): void
    {
        $testapiClient = Factory::createTestapiClient();
        $userDto = $testapiClient->getUser('ivanov');
        $userDto->name = 'Petr Petrovich';
        $userDto->blocked = true;
        $userDto->permissions->clean();
        $userDto->permissions->addPermission(UserPermissionDto::COMMENT);
        $testapiClient->updateUser($userDto);
    }
}