<?php

namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    public static $roles = [
        'ADMINISTRATOR' => 'Administrator',
        'STORE_ADMINISTRATOR' => 'Store administrator',
        'TECHNICAL_SUPPORT_ENGINEER' => 'Technical Support Engineer',
        'CLINICAL_SUPPORT_ENGINEER' => 'Clinical Support Engineer',
        'CUSTOMER' => 'Customer'
    ];
}
