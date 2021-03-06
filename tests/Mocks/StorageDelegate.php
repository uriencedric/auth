<?php

namespace Solution10\Auth\Tests\Mocks;

use Solution10\Auth\Package;
use Solution10\Auth\UserRepresentation;
use Solution10\Auth\Tests\Mocks\UserRepresentation as UserRepMock;

/**
 * Storage Delegate Mock.
 */
class StorageDelegate implements \Solution10\Auth\StorageDelegate
{
    // Public only so the tests can access this data to verify:
    public $users = array(
        1 => array(
            'id'        => 1,
            'username'  => 'Alex',
            'email'     => 'alex@solution10.com',
            'password'  => '$2a$08$pQIwqrJ00RbAikHLcQ8tOuSrDFEvToDmbXxtXEFO8vJRC38cXZX76', // Alex
            'packages'  => array(),
            'overrides' => array(),
        ),
        2 => array(
            'id'        => 2,
            'username'  => 'Lucie',
            'email'     => 'lucie@solution10.com',
            'packages'  => array(),
            'overrides' => array(),
        ),
    );

    public function authFetchUserByUsername($instance_name, $username)
    {
        foreach ($this->users as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }

        return false;
    }

    public function authFetchUserRepresentation($instance_name, $user_id)
    {
        return (array_key_exists($user_id, $this->users)) ? new UserRepMock($this->users[$user_id]) : false;
    }

    public function authAddPackageToUser(
        $instance_name,
        UserRepresentation $user,
        Package $package
    ) {
        foreach ($this->users as &$u) {
            if ($u['id'] == $user->id()) {
                $u['packages'][] = $package;
            }
        }
    }


    public function authRemovePackageFromUser(
        $instance_name,
        UserRepresentation $user,
        Package $package
    ) {
        foreach ($this->users as &$u) {
            if ($u['id'] == $user->id()) {
                foreach ($u['packages'] as $idx => $p) {
                    if ($p->name() === $package->name()) {
                        unset($u['packages'][$idx]);
                    }
                }
            }
        }
        return true;
    }

    public function authFetchPackagesForUser($instance_name, UserRepresentation $user)
    {
        $packages = array();
        foreach ($this->users as $u) {
            if ($u['id'] == $user->id()) {
                $packages = $u['packages'];
                break;
            }
        }

        return $packages;
    }

    public function authUserHasPackage(
        $instance_name,
        UserRepresentation $user,
        Package $package
    ) {
        foreach ($this->users[$user->id()]['packages'] as $p) {
            if ($p->name() === $package->name()) {
                return true;
            }
        }

        return false;
    }

    public function authOverridePermissionForUser(
        $instance_name,
        UserRepresentation $user,
        $permission,
        $new_value
    ) {
        if (array_key_exists($user->id(), $this->users)) {
            $this->users[$user->id()]['overrides'][$permission] = $new_value;
            return true;
        }

        return false;
    }

    public function authFetchOverridesForUser($instance_name, UserRepresentation $user)
    {
        $overrides = array();
        if (array_key_exists($user->id(), $this->users)) {
            $overrides = $this->users[$user->id()]['overrides'];
        }

        return $overrides;
    }

    public function authRemoveOverrideForUser($instance_name, UserRepresentation $user, $permission)
    {
        if (array_key_exists($permission, $this->users[$user->id()]['overrides'])) {
            unset($this->users[$user->id()]['overrides'][$permission]);
        }
        return true;
    }

    public function authResetOverridesForUser($instance_name, UserRepresentation $user)
    {
        if (array_key_exists($user->id(), $this->users)) {
            $this->users[$user->id()]['overrides'] = array();
        }

        return true;
    }
}
