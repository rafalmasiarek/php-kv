<?php

declare(strict_types=1);

namespace rafalmasiarek\Storage;

use MongoDB\Collection;

/**
 * MongoStorage
 *
 * Stores each (scope,id) as a single document:
 *  { _id: {scope}:{id}, scope, id, data: {...schema-managed...} }
 */
final class MongoStorage implements StorageInterface
{
    public function __construct(
        private Collection $collection
    ) {}

    public function load(string $scope, string $id): ?array
    {
        $doc = $this->collection->findOne(['_id' => $this->id($scope, $id)]);
        if ($doc === null) {
            return null;
        }

        $doc = $doc->getArrayCopy();

        return isset($doc['data']) && is_array($doc['data']) ? $doc['data'] : null;
    }

    public function save(string $scope, string $id, array $document): void
    {
        $this->collection->updateOne(
            ['_id' => $this->id($scope, $id)],
            ['$set' => [
                'scope' => $scope,
                'id'    => $id,
                'data'  => $document,
            ]],
            ['upsert' => true]
        );
    }

    public function delete(string $scope, string $id): void
    {
        $this->collection->deleteOne(['_id' => $this->id($scope, $id)]);
    }

    private function id(string $scope, string $id): string
    {
        return $scope . ':' . $id;
    }
}
