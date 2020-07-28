<?php

namespace src\Service\User\Service;

use src\Service\User\Service\Rely\UserProfileBaseService;
use Api\User\UserProfileService;

class UserProfileServiceImpl extends UserProfileBaseService implements UserProfileService
{
    public function getUserProfile($id)
    {
        return ['name' => 'coco', 'sex' => 'male'];
    }
}