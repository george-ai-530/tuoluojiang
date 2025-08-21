<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use LDAP\Connection;
use App\Http\Model\Ldap\LdapUser;

class AuthLdap
{
    public function handle(Request $request, Closure $next)
    {
        $ldapConn = new Connection(
            host: Env::get('LDAP_URL'),
            port: 389,
            useSSL: false
        );

        $ldapConn->bind(
            dn: Env::get('LDAP_BIND_DN'),
            password: Env::get('LDAP_BIND_PASSWORD')
        );

        $userManager = app('enforcer')->getLdapUserRepository();
        $userManager->setConnection($ldapConn);

        return $next($request);
    }
}