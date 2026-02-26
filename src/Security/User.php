<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private string $id;
    private string $email;
    private string $password;
    private array $roles;

    public function __construct(string $id, string $email, string $password, array $roles = [])
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        // Al menos debe tener ROLE_USER
        $this->roles = empty($roles) ? ['ROLE_USER'] : $roles;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // Todos los usuarios deben tener al menos ROLE_USER
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }


}
