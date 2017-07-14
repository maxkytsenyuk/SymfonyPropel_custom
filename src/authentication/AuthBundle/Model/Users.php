<?php

namespace authentication\AuthBundle\Model;

use authentication\AuthBundle\Model\om\BaseUsers;
use Symfony\Component\Security\Core\User\UserInterface;

class Users extends BaseUsers implements UserInterface
{
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
        return [
            'ROLE_USER'
        ];

    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
        return $this->salt;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
