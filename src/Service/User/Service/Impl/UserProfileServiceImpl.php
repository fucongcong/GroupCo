<?php

namespace src\Service\User\Service\Impl;

use src\Service\User\Service\Rely\UserProfileBaseService;
use src\Service\User\Service\UserProfileService;

class UserProfileServiceImpl extends UserProfileBaseService implements UserProfileService
{
    public function getUserProfile($id)
    {
        return ['name' => 'coco', 'sex' => 'male'];
    }
}