<?php

declare(strict_types=1);

namespace rafalmasiarek;

use DateTimeImmutable;
use DateTimeZone;
use rafalmasiarek\Schema\SchemaRegistry;
use rafalmasiarek\Storage\StorageInterface;

/**
 * KV
 *
 * Versioned key-value manager.
 *
 * Responsibilities:
 * - Load/save documents via StorageInterface.
 * - Maintain per-key version history using SchemaRegistry.
 * - Provide simple API: set/get/all/info/history/delete/deleteDocument.
 */
final class KV
{
    public function __construct(
        private StorageInterface $storage
    ) {}

    /**
     * Store a new value for (scope, id, key).
     *
     * Creates document if missing.
     * Appends a new version when value changes.
     *
     * @param string $scope
     * @param string $id
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public function set(string $scope, string $id, string $key, mixed $value): void
    {
        $doc = SchemaRegistry::normalizeDocument(
            $this->storage->load($scope, $id)
        );

        $keys = $doc['keys'];

        $existing = $keys[$key] ?? null;
        $keyData  = SchemaRegistry::normalizeKey(
            is_array($existing) ? $existing : null,
            is_array($existing) && isset($existing['schema']) ? (string)$existing['schema'] : null
        );

        $now = $this->now();

        // If there is a current version and value did not change, no-op
        $curVer = (int)$keyData['current_version'];
        if ($curVer > 0 && isset($keyData['history'][(string)$curVer])) {
            $currentVal = $keyData['history'][(string)$curVer]['value'] ?? null;
            if ($currentVal === $value) {
                $doc['keys'][$key] = $keyData;
                $this->storage->save($scope, $id, $doc);
                return;
            }
        }

        // New version index
        $newVer = $curVer + 1;

        if ($curVer === 0) {
            // first time
            $keyData['created_at'] = $now;
        }

        $keyData['current_version'] = $newVer;
        $keyData['updated_at']      = $now;
        $keyData['schema']          = $keyData['schema'] ?: SchemaRegistry::CURRENT;
        $keyData['history'][(string)$newVer] = [
            'value'      => $value,
            'created_at' => $now,
        ];

        $doc['keys'][$key] = $keyData;

        $this->storage->save($scope, $id, $doc);
    }

    /**
     * Get latest value for (scope, id, key).
     *
     * @param string $scope
     * @param string $id
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function get(string $scope, string $id, string $key, mixed $default = null): mixed
    {
        $doc = SchemaRegistry::normalizeDocument(
            $this->storage->load($scope, $id)
        );
        $keys = $doc['keys'];

        if (!isset($keys[$key]) || !is_array($keys[$key])) {
            return $default;
        }

        $keyData = SchemaRegistry::normalizeKey($keys[$key], $keys[$key]['schema'] ?? null);

        $curVer = (int)$keyData['current_version'];
        if ($curVer <= 0) {
            return $default;
        }

        $v = $keyData['history'][(string)$curVer]['value'] ?? null;
        return $v ?? $default;
    }

    /**
     * Return all current values for a given (scope, id).
     *
     * @param string $scope
     * @param string $id
     * @return array<string,mixed>
     */
    public function all(string $scope, string $id): array
    {
        $doc = SchemaRegistry::normalizeDocument(
            $this->storage->load($scope, $id)
        );
        $out = [];

        foreach ($doc['keys'] as $key => $raw) {
            if (!is_array($raw)) {
                continue;
            }
            $keyData = SchemaRegistry::normalizeKey($raw, $raw['schema'] ?? null);
            $curVer  = (int)$keyData['current_version'];
            if ($curVer <= 0) {
                continue;
            }
            if (!array_key_exists((string)$curVer, $keyData['history'])) {
                continue;
            }
            $out[$key] = $keyData['history'][(string)$curVer]['value'] ?? null;
        }

        return $out;
    }

    /**
     * Return detailed info for a single key (including history).
     *
     * @param string $scope
     * @param string $id
     * @param string $key
     * @return array<string,mixed>|null
     */
    public function info(string $scope, string $id, string $key): ?array
    {
        $doc = SchemaRegistry::normalizeDocument(
            $this->storage->load($scope, $id)
        );

        if (!isset($doc['keys'][$key]) || !is_array($doc['keys'][$key])) {
            return null;
        }

        return SchemaRegistry::normalizeKey($doc['keys'][$key], $doc['keys'][$key]['schema'] ?? null);
    }

    /**
     * Get full history for given key.
     *
     * @param string $scope
     * @param string $id
     * @param string $key
     * @return array<string,array<string,mixed>>
     */
    public function history(string $scope, string $id, string $key): array
    {
        $info = $this->info($scope, $id, $key);
        if ($info === null) {
            return [];
        }
        $hist = $info['history'] ?? [];
        return is_array($hist) ? $hist : [];
    }

    /**
     * Delete single key (all its versions) from document.
     *
     * @param string $scope
     * @param string $id
     * @param string $key
     * @return void
     */
    public function delete(string $scope, string $id, string $key): void
    {
        $doc = SchemaRegistry::normalizeDocument(
            $this->storage->load($scope, $id)
        );

        if (!isset($doc['keys'][$key])) {
            return;
        }

        unset($doc['keys'][$key]);

        // If no more keys, delete document entirely
        if (empty($doc['keys'])) {
            $this->storage->delete($scope, $id);
            return;
        }

        $this->storage->save($scope, $id, $doc);
    }

    /**
     * Delete whole document (scope + id).
     *
     * @param string $scope
     * @param string $id
     * @return void
     */
    public function deleteDocument(string $scope, string $id): void
    {
        $this->storage->delete($scope, $id);
    }

    /**
     * Current RFC3339 timestamp in UTC.
     *
     * @return string
     */
    private function now(): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DATE_RFC3339_EXTENDED);
    }
}
