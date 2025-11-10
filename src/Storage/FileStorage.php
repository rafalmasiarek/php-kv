<?php

declare(strict_types=1);

namespace rafalmasiarek\Storage;

use RuntimeException;

/**
 * FileStorage
 *
 * Simple JSON-file based storage:
 *  - One file per (scope, id).
 *  - File name: {scope}__{id}.json (both urlencoded for safety).
 */
final class FileStorage implements StorageInterface
{
    public function __construct(
        private string $dir
    ) {
        if (!is_dir($this->dir) && !@mkdir($this->dir, 0775, true) && !is_dir($this->dir)) {
            throw new RuntimeException('Cannot create KV storage dir: ' . $this->dir);
        }
    }

    public function load(string $scope, string $id): ?array
    {
        $path = $this->path($scope, $id);
        if (!is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new RuntimeException('Cannot read KV file: ' . $path);
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : null;
    }

    public function save(string $scope, string $id, array $document): void
    {
        $path = $this->path($scope, $id);
        $tmp  = $path . '.tmp';

        $json = json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException('KV JSON encode failed.');
        }

        if (file_put_contents($tmp, $json) === false) {
            throw new RuntimeException('Cannot write KV temp file: ' . $tmp);
        }

        if (!@rename($tmp, $path)) {
            @unlink($tmp);
            throw new RuntimeException('Cannot move KV temp file into place: ' . $path);
        }
    }

    public function delete(string $scope, string $id): void
    {
        $path = $this->path($scope, $id);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function path(string $scope, string $id): string
    {
        $safeScope = rawurlencode($scope);
        $safeId    = rawurlencode($id);
        return rtrim($this->dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $safeScope . '__' . $safeId . '.json';
    }
}
