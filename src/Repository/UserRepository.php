<?php

namespace App\Repository;

use App\Security\User;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\BSON\ObjectId;

class UserRepository
{
    private Collection $collection;

    public function __construct(Client $client, string $dbName)
    {
        $this->collection = $client->selectCollection($dbName, 'users');
    }

    public function findOneByEmail(string $email): ?User
    {
        $doc = $this->collection->findOne(['email' => $email]);

        if (!$doc) {
            return null;
        }

        $arr = (array) $doc;
        $id = isset($arr['_id']) ? (string) $arr['_id'] : '';
        $email = $arr['email'] ?? '';
        $password = $arr['password'] ?? '';
        // Roles in mongodb can be BSON objects, ensure they are simple arrays
        $roles = isset($arr['roles']) ? array_values((array)$arr['roles']) : ['ROLE_USER'];

        return new User($id, $email, $password, $roles);
    }

    public function insertUser(string $email, string $hashedPassword, array $roles): string
    {
        // Revisar si ya existe
        $existing = $this->findOneByEmail($email);
        if ($existing) {
            // Eliminar si existe para el demo y crearlo de cero
            $this->collection->deleteOne(['email' => $email]);
        }

        $result = $this->collection->insertOne([
            'email' => $email,
            'password' => $hashedPassword,
            'roles' => $roles,
            'createdAt' => new \MongoDB\BSON\UTCDateTime(),
        ]);

        return (string) $result->getInsertedId();
    }
}
