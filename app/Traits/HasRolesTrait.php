<?php

namespace App\Traits;

use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin \App\Models\BaseModel
 */
trait HasRolesTrait
{
    use HasRoles {
        assignRole as protected originalAssignRole;
        removeRole as protected originalRemoveRole;
        syncRoles as protected originalSyncRoles;
    }

    /**
     * @param mixed ...$roles
     * @return $this
     */
    public function assignRole(...$roles)
    {
        $this->originalAssignRole(...$roles);

        $this->fireRoleAssignedEvent($roles);

        return $this;
    }

    /**
     * @param $role
     * @return bool
     */
    public function fireRoleAssignedEvent($role)
    {

        if (is_iterable($role)) {
            return array_walk($role, [$this, 'fireRoleAssignedEvent']);
        }

        // event(new RoleAssignedEvent($this, $this->getStoredRole($role)));

        return true;
    }

    /**
     * @param $role
     * @return $this
     */
    public function removeRole($role)
    {
        $this->originalRemoveRole($role);

        $this->fireRoleRemovedEvent($role);

        return $this;
    }

    /**
     * @param $role
     * @return bool
     */
    public function fireRoleRemovedEvent($role)
    {
        if (is_iterable($role)) {
            return array_walk($role, [$this, 'fireRoleRemovedEvent']);
        }

        // event(new RoleRemovedEvent($this, $this->getStoredRole($role)));

        return true;
    }
}
