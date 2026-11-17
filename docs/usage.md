## Concepts

**Scope** + **ID** + **Key** ⇒ versioned value.

Think of:

- `scope` – logical bucket, e.g. `"user"`, `"feature"`, `"tenant"`.
- `id`    – identifier inside scope, e.g. `"123"`, `"acme"`.
- `key`   – name inside that document, e.g. `"theme"`, `"feature_x_enabled"`.

Example:

```php
use rafalmasiarek\KV;
use rafalmasiarek\KV\Storage\FileStorage;

$storage = new FileStorage(__DIR__ . '/storage');
$kv = new KV($storage);

// create versions
$kv->set('user', '123', 'theme', 'dark');
$kv->set('user', '123', 'theme', 'light');

// read latest
$current = $kv->get('user', '123', 'theme'); // "light"

// list all latest values for given (scope,id)
$all = $kv->all('user', '123');

// detailed info (with history)
$info = $kv->info('user', '123', 'theme');
// [
//   'schema'          => 'v1',
//   'current_version' => 2,
//   'created_at'      => '2025-01-01T12:00:00+00:00',
//   'updated_at'      => '2025-01-02T09:00:00+00:00',
//   'history'         => [
//       '1' => ['value' => 'dark',  'created_at' => '...'],
//       '2' => ['value' => 'light', 'created_at' => '...'],
//   ],
// ]

// delete a key (all its versions)
$kv->delete('user', '123', 'theme');

// delete whole document (scope+id)
$kv->deleteDocument('user', '123');
```


## Installation

```bash
composer require rafalmasiarek/php-kv
```

## Minimal usage

```php
use rafalmasiarek\KV;
use rafalmasiarek\KV\Storage\FileStorage;

$kv = new KV(new FileStorage(__DIR__ . '/var/kv'));

// Set
$kv->set('app', 'flags', 'new_ui', true);

// Get
if ($kv->get('app', 'flags', 'new_ui')) {
    // ...
}
```
