<?php

namespace App\Http\Model\Ldap;

use Illuminate\Foundation\Auth\User as Authenticatable;

class LdapUser extends Authenticatable
{
    use Authenticatable;

    public function __construct(array $user)
    {
        $this->setLdapUserAttributes($user);
    }

    private function setLdapUserAttributes(array $user): void
    {
        foreach ($user as $key => $value) {
            $this->$key = $value;
        }
    }
}