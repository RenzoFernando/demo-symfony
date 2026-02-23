<?php

namespace App\Repository;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\BSON\ObjectId;

final class ProductRepository
{
    private Collection $collection;

    public function __construct(Client $client, string $dbName)
    {
        $this->collection = $client->selectCollection($dbName, 'products');
    }

    public function findAll(): array
    {
        $cursor = $this->collection->find([], ['sort' => ['_id' => -1]]);
        return array_map([$this, 'normalize'], iterator_to_array($cursor));
    }

    private function toObjectId(string $id): ?ObjectId
    {
        // ObjectId válido = 24 caracteres hex
        if (!preg_match('/^[a-f0-9]{24}$/i', $id)) {
            return null;
        }

        try {
            return new ObjectId($id);
        } catch (\Throwable) {
            return null;
        }
    }

    public function findById(string $id): ?array
    {
        $oid = $this->toObjectId($id);
        if (!$oid) {
            return null;
        }

        $doc = $this->collection->findOne(['_id' => $oid]);
        return $doc ? $this->normalize($doc) : null;
    }

    public function insert(array $data): string
    {
        $result = $this->collection->insertOne([
            'name' => $data['name'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'createdAt' => new \MongoDB\BSON\UTCDateTime(),
        ]);

        return (string) $result->getInsertedId();
    }

    public function update(string $id, array $data): bool
    {
        $oid = $this->toObjectId($id);
        if (!$oid) {
            return false;
        }

        $result = $this->collection->updateOne(
            ['_id' => $oid],
            ['$set' => [
                'name' => $data['name'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'updatedAt' => new \MongoDB\BSON\UTCDateTime(),
            ]]
        );

        return $result->getMatchedCount() > 0;
    }

    public function delete(string $id): bool
    {
        $oid = $this->toObjectId($id);
        if (!$oid) {
            return false;
        }

        $result = $this->collection->deleteOne(['_id' => $oid]);
        return $result->getDeletedCount() > 0;
    }

    private function normalize(object|array $doc): array
    {
        // MongoDB devuelve BSONDocument; lo convertimos a array simple para JSON
        $arr = (array) $doc;

        $id = isset($arr['_id']) ? (string) $arr['_id'] : null;

        return [
            'id' => $id,
            'name' => $arr['name'] ?? null,
            'price' => (float) ($arr['price'] ?? 0),
            'stock' => (int) ($arr['stock'] ?? 0),
        ];
    }
}