<?php

declare(strict_types=1);

namespace rafalmasiarek\Storage;

/**
 * StorageInterface
 *
 * Minimal abstraction over a document store used by KV.
 * Implementations are responsible for persistence only.
 *
 * Document shape is managed by SchemaRegistry and KV, not by storage.
 */
interface StorageInterface
{
    /**
     * Load document for given (scope, id).
     *
     * When no document exists, MUST return null.
     *
     * @param string $scope
     * @param string $id
     * @return array<string,mixed>|null
     */
    public function load(string $scope, string $id): ?array;

    /**
     * Persist document for given (scope, id).
     *
     * Implementations MUST fully replace previous content.
     *
     * @param string               $scope
     * @param string               $id
     * @param array<string,mixed>  $document
     * @return void
     */
    public function save(string $scope, string $id, array $document): void;

    /**
     * Delete document for given (scope, id).
     *
     * MUST NOT throw when document does not exist.
     *
     * @param string $scope
     * @param string $id
     * @return void
     */
    public function delete(string $scope, string $id): void;
}
