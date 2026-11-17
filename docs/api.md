## Index

### Classes

- [`KV`](#kv) — `rafalmasiarek`
- [`SchemaRegistry`](#schemaregistry) — `rafalmasiarek\Schema`
- [`FileStorage`](#filestorage) — `rafalmasiarek\Storage`
- [`MongoStorage`](#mongostorage) — `rafalmasiarek\Storage`
- [`RedisStorage`](#redisstorage) — `rafalmasiarek\Storage`

### Interfaces

- [`StorageInterface`](#storageinterface) — `rafalmasiarek\Storage`

## `KV`

*final class* — `rafalmasiarek\KV` · [source](https://github.com/rafalmasiarek/php-kv/blob/main/src/KV.php#L22)

KV Versioned key-value manager. Responsibilities: - Load/save documents via StorageInterface. - Maintain per-key version history using SchemaRegistry. - Provide simple API: set/get/all/info/history/delete/deleteDocument.

**Depends on:** [`StorageInterface`](#storageinterface)

| Method | Visibility | Returns | Summary |
|---|---|---|---|
| [`__construct()`](#kv-__construct) | public | — | — |
| [`set()`](#kv-set) | public | `void` | Store a new value for (scope, id, key). Creates document if missing. Appends a new version when value changes. |
| [`get()`](#kv-get) | public | `mixed` | Get latest value for (scope, id, key). |
| [`all()`](#kv-all) | public | `array<string, mixed>` | Return all current values for a given (scope, id). |
| [`info()`](#kv-info) | public | `array<string, mixed>\|null` | Return detailed info for a single key (including history). |
| [`history()`](#kv-history) | public | `array<string, array<string, mixed>>` | Get full history for given key. |
| [`delete()`](#kv-delete) | public | `void` | Delete single key (all its versions) from document. |
| [`deleteDocument()`](#kv-deletedocument) | public | `void` | Delete whole document (scope + id). |
| [`now()`](#kv-now) | private | `string` | Current RFC3339 timestamp in UTC. |

<details>
<summary><code>__construct()</code></summary>

#### `KV __construct()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/KV.php#L24)

```php
public function __construct(StorageInterface $storage)
```

| Param | Type | Description |
|---|---|---|
| `$storage` | [`StorageInterface`](#storageinterface) | — |

</details>

<details>
<summary><code>set()</code></summary>

#### `KV set()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/KV.php#L40)

```php
public function set(string $scope, string $id, string $key, mixed $value): void
```

Store a new value for (scope, id, key). Creates document if missing. Appends a new version when value changes.

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |
| `$key` | `string` | — |
| `$value` | `mixed` | — |

</details>

<details>
<summary><code>get()</code></summary>

#### `KV get()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/KV.php#L97)

```php
public function get(string $scope, string $id, string $key, mixed $default): mixed
```

Get latest value for (scope, id, key).

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |
| `$key` | `string` | — |
| `$default` | `mixed` | — |

**Returns:** `mixed`

</details>

<details>
<summary><code>all()</code></summary>

#### `KV all()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/KV.php#L126)

```php
public function all(string $scope, string $id): array
```

Return all current values for a given (scope, id).

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

**Returns:** `array<string, mixed>`

</details>

<details>
<summary><code>info()</code></summary>

#### `KV info()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/KV.php#L159)

```php
public function info(string $scope, string $id, string $key): ?array
```

Return detailed info for a single key (including history).

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |
| `$key` | `string` | — |

**Returns:** `array<string, mixed>\|null`

</details>

<details>
<summary><code>history()</code></summary>

#### `KV history()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/KV.php#L180)

```php
public function history(string $scope, string $id, string $key): array
```

Get full history for given key.

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |
| `$key` | `string` | — |

**Returns:** `array<string, array<string, mixed>>`

</details>

<details>
<summary><code>delete()</code></summary>

#### `KV delete()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/KV.php#L198)

```php
public function delete(string $scope, string $id, string $key): void
```

Delete single key (all its versions) from document.

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |
| `$key` | `string` | — |

</details>

<details>
<summary><code>deleteDocument()</code></summary>

#### `KV deleteDocument()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/KV.php#L226)

```php
public function deleteDocument(string $scope, string $id): void
```

Delete whole document (scope + id).

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

</details>

<details>
<summary><code>now()</code></summary>

#### `KV now()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/KV.php#L236)

```php
private function now(): string
```

Current RFC3339 timestamp in UTC.

**Returns:** `string`

</details>

## `SchemaRegistry`

*final class* — `rafalmasiarek\Schema\SchemaRegistry` · [source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Schema/SchemaRegistry.php#L21)

SchemaRegistry Central definition of document/key schemas for KV. All schema-specific decisions go through this class. Versioning rules: - Each stored document has top-level "schema". - Each key entry has its own "schema" so keys can migrate independently. - CURRENT defines which schema is used when creating new documents/keys. - When reading, unknown/missing schema falls back to CURRENT. This file is the only place you change when introducing e.g. v2.

| Method | Visibility | Returns | Summary |
|---|---|---|---|
| [`normalizeDocument()`](#schemaregistry-normalizedocument) | public static | `array<string, mixed>` | Normalize document to a known schema. - Ensures "schema" is set. - Ensures "keys" container exists. - Does NOT touch individual key payloads (handled lazily). |
| [`normalizeKey()`](#schemaregistry-normalizekey) | public static | `array<string, mixed>` | Normalize a single key structure inside a document. - Ensures required fields exist. - Keeps history as-is if present. |

<details>
<summary><code>normalizeDocument()</code></summary>

#### `SchemaRegistry normalizeDocument()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Schema/SchemaRegistry.php#L64)

```php
public static function normalizeDocument(?array $doc): array
```

Normalize document to a known schema. - Ensures "schema" is set. - Ensures "keys" container exists. - Does NOT touch individual key payloads (handled lazily).

| Param | Type | Description |
|---|---|---|
| `$doc` | `array<string, mixed>\|null` | — |

**Returns:** `array<string, mixed>`

</details>

<details>
<summary><code>normalizeKey()</code></summary>

#### `SchemaRegistry normalizeKey()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Schema/SchemaRegistry.php#L100)

```php
public static function normalizeKey(?array $keyData, ?string $schemaId): array
```

Normalize a single key structure inside a document. - Ensures required fields exist. - Keeps history as-is if present.

| Param | Type | Description |
|---|---|---|
| `$keyData` | `array<string, mixed>\|null` | — |
| `$schemaId` | `string\|null` | — |

**Returns:** `array<string, mixed>`

</details>

## `FileStorage`

*final class* — `rafalmasiarek\Storage\FileStorage` · [source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/FileStorage.php#L16)

FileStorage Simple JSON-file based storage: - One file per (scope, id). - File name: {scope}__{id}.json (both urlencoded for safety).

implements [`StorageInterface`](#storageinterface)

| Method | Visibility | Returns | Summary |
|---|---|---|---|
| [`__construct()`](#filestorage-__construct) | public | — | — |
| [`load()`](#filestorage-load) | public | `?array` | — |
| [`save()`](#filestorage-save) | public | `void` | — |
| [`delete()`](#filestorage-delete) | public | `void` | — |
| [`path()`](#filestorage-path) | private | `string` | — |

<details>
<summary><code>__construct()</code></summary>

#### `FileStorage __construct()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/FileStorage.php#L18)

```php
public function __construct(string $dir)
```

| Param | Type | Description |
|---|---|---|
| `$dir` | `string` | — |

</details>

<details>
<summary><code>load()</code></summary>

#### `FileStorage load()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/FileStorage.php#L26)

```php
public function load(string $scope, string $id): ?array
```

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

**Returns:** `?array`

</details>

<details>
<summary><code>save()</code></summary>

#### `FileStorage save()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/FileStorage.php#L40)

```php
public function save(string $scope, string $id, array $document): void
```

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |
| `$document` | `array` | — |

</details>

<details>
<summary><code>delete()</code></summary>

#### `FileStorage delete()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/FileStorage.php#L60)

```php
public function delete(string $scope, string $id): void
```

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

</details>

<details>
<summary><code>path()</code></summary>

#### `FileStorage path()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/FileStorage.php#L68)

```php
private function path(string $scope, string $id): string
```

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

**Returns:** `string`

</details>

## `MongoStorage`

*final class* — `rafalmasiarek\Storage\MongoStorage` · [source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/MongoStorage.php#L15)

MongoStorage Stores each (scope,id) as a single document: { _id: {scope}:{id}, scope, id, data: {...schema-managed...} }

implements [`StorageInterface`](#storageinterface)

**Depends on:** `Collection`

| Method | Visibility | Returns | Summary |
|---|---|---|---|
| [`__construct()`](#mongostorage-__construct) | public | — | — |
| [`load()`](#mongostorage-load) | public | `?array` | — |
| [`save()`](#mongostorage-save) | public | `void` | — |
| [`delete()`](#mongostorage-delete) | public | `void` | — |
| [`id()`](#mongostorage-id) | private | `string` | — |

<details>
<summary><code>__construct()</code></summary>

#### `MongoStorage __construct()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/MongoStorage.php#L17)

```php
public function __construct(Collection $collection)
```

| Param | Type | Description |
|---|---|---|
| `$collection` | `Collection` | — |

</details>

<details>
<summary><code>load()</code></summary>

#### `MongoStorage load()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/MongoStorage.php#L21)

```php
public function load(string $scope, string $id): ?array
```

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

**Returns:** `?array`

</details>

<details>
<summary><code>save()</code></summary>

#### `MongoStorage save()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/MongoStorage.php#L33)

```php
public function save(string $scope, string $id, array $document): void
```

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |
| `$document` | `array` | — |

</details>

<details>
<summary><code>delete()</code></summary>

#### `MongoStorage delete()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/MongoStorage.php#L46)

```php
public function delete(string $scope, string $id): void
```

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

</details>

<details>
<summary><code>id()</code></summary>

#### `MongoStorage id()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/MongoStorage.php#L51)

```php
private function id(string $scope, string $id): string
```

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

**Returns:** `string`

</details>

## `RedisStorage`

*final class* — `rafalmasiarek\Storage\RedisStorage` · [source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/RedisStorage.php#L16)

RedisStorage Stores each KV document as a single JSON string in Redis: key = prefix + scope + ':' + id

implements [`StorageInterface`](#storageinterface)

**Depends on:** `Redis`

| Method | Visibility | Returns | Summary |
|---|---|---|---|
| [`__construct()`](#redisstorage-__construct) | public | — | — |
| [`load()`](#redisstorage-load) | public | `?array` | — |
| [`save()`](#redisstorage-save) | public | `void` | — |
| [`delete()`](#redisstorage-delete) | public | `void` | — |
| [`key()`](#redisstorage-key) | private | `string` | — |

<details>
<summary><code>__construct()</code></summary>

#### `RedisStorage __construct()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/RedisStorage.php#L18)

```php
public function __construct(Redis $redis, string $prefix)
```

| Param | Type | Description |
|---|---|---|
| `$redis` | `Redis` | — |
| `$prefix` | `string` | — |

</details>

<details>
<summary><code>load()</code></summary>

#### `RedisStorage load()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/RedisStorage.php#L23)

```php
public function load(string $scope, string $id): ?array
```

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

**Returns:** `?array`

</details>

<details>
<summary><code>save()</code></summary>

#### `RedisStorage save()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/RedisStorage.php#L33)

```php
public function save(string $scope, string $id, array $document): void
```

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |
| `$document` | `array` | — |

</details>

<details>
<summary><code>delete()</code></summary>

#### `RedisStorage delete()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/RedisStorage.php#L42)

```php
public function delete(string $scope, string $id): void
```

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

</details>

<details>
<summary><code>key()</code></summary>

#### `RedisStorage key()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/RedisStorage.php#L47)

```php
private function key(string $scope, string $id): string
```

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

**Returns:** `string`

</details>

## `StorageInterface`

*interface* — `rafalmasiarek\Storage\StorageInterface` · [source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/StorageInterface.php#L15)

StorageInterface Minimal abstraction over a document store used by KV. Implementations are responsible for persistence only. Document shape is managed by SchemaRegistry and KV, not by storage.

| Method | Visibility | Returns | Summary |
|---|---|---|---|
| [`load()`](#storageinterface-load) | public | `array<string, mixed>\|null` | Load document for given (scope, id). When no document exists, MUST return null. |
| [`save()`](#storageinterface-save) | public | `void` | Persist document for given (scope, id). Implementations MUST fully replace previous content. |
| [`delete()`](#storageinterface-delete) | public | `void` | Delete document for given (scope, id). MUST NOT throw when document does not exist. |

<details>
<summary><code>load()</code></summary>

#### `StorageInterface load()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/StorageInterface.php#L26)

```php
public function load(string $scope, string $id): ?array
```

Load document for given (scope, id). When no document exists, MUST return null.

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

**Returns:** `array<string, mixed>\|null`

</details>

<details>
<summary><code>save()</code></summary>

#### `StorageInterface save()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/StorageInterface.php#L38)

```php
public function save(string $scope, string $id, array $document): void
```

Persist document for given (scope, id). Implementations MUST fully replace previous content.

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |
| `$document` | `array<string, mixed>` | — |

</details>

<details>
<summary><code>delete()</code></summary>

#### `StorageInterface delete()`

[source](https://github.com/rafalmasiarek/php-kv/blob/main/src/Storage/StorageInterface.php#L49)

```php
public function delete(string $scope, string $id): void
```

Delete document for given (scope, id). MUST NOT throw when document does not exist.

| Param | Type | Description |
|---|---|---|
| `$scope` | `string` | — |
| `$id` | `string` | — |

</details>

