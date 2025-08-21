<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use LDAP\Connection;
use App\Http\Model\Ldap\LdapUser;
use Illuminate\Support\Env;

class LdapUserProvider implements UserProvider
{
    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function retrieveById($identifier)
    {
        return $this->searchUser($identifier)?->toLdapUser();
    }

    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    public function updateRememberToken(UserContract $user, $token)
    {
        // LDAP无此功能
    }

    public function retrieveByCredentials(array $credentials)
    {
        $username = $credentials['username'] ?? $credentials['uid'] ?? '';
        return $this->searchUser($username)?->toLdapUser();
    }

    public function validateCredentials(UserContract $user, array $credentials):
    {
        $username = $credentials['username'] ?? '';
        $password = $credentials['password'] ?? '';

        return $this->connection->bind(
            dn: "uid={$username},{$this->getBaseDn()}",
            password: $password
        );
    }

    protected function searchUser(string $username)
    {
        $search = $this->connection->search(
            base: $this->getBaseDn(),
            filter: "uid={$username}",
            scope: 'sub'
        );

        return $search->current();
    }

    protected function getBaseDn(): string
    {
        return Env::get('LDAP_USER_BASE_DN', 'dc=example,dc=com');
    }

    protected function toLdapUser($entry): ?LdapUser
    {
        if (!$entry) return null;

        return new LdapUser([
            'uid' => $entry->getAttribute('uid')[0] ?? null,
            'real_name' => $entry->getAttribute(Env::get('LDAP_USER_REAL_NAME_ATTRIBUTE', 'cn'))[0] ?? null,
            'email' => $entry->getAttribute(Env::get('LDAP_USER_EMAIL_ATTRIBUTE', 'mail'))[0] ?? null,
        ]);
    }
}