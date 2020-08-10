<?php
/**
 * Local configuration.
 *
 * Copy this file to `local.php` and change its settings as required.
 * `local.php` is ignored by git and safe to use for local and sensitive data like usernames and passwords.
 */

declare(strict_types=1);

return [
    'authentication' => [
        'pdo' => [
            'dsn' => 'mysql:host=91.239.234.17;port=3306;dbname=medicine_german',
            'table' => 'user',
            'field' => [
                'identity' => 'email',
                'password' => 'password',
            ],
            'username' => 'medicine_german',
            'password' => 'socket27_german',
            'sql_get_roles' =>
                'select role.name from role inner join user as u on role.role_id = u.role_id where u.email=:identity',
            // zend-expressive-authorization >0.5
            'sql_get_details' =>
                'select u.full_name as user_name,u.phone_in as phone_in,u.phone_out as phone_out '
                . 'from user as u where email=:identity',
        ],
        'redirect' => '/login',
    ],
];
