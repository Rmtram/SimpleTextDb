[![Build Status](https://travis-ci.org/Rmtram/SimpleTextDb.svg)](https://travis-ci.org/Rmtram/SimpleTextDb)
[![Total
Downloads](https://poser.pugx.org/rmtram/simple-text-db/downloads)](https://packagist.org/packages/rmtram/simple-text-db)
[![Latest Stable
Version](https://poser.pugx.org/rmtram/simple-text-db/v/stable.png)](https://packagist.org/packages/rmtram/simple-text-db)

# Simple Text Database. 
Simple Text file based database and database mapper.

# Install.

```
$ composer require rmtram/simple-text-db
```

# Usage.


### Connector

For example create connector:

```php
// argument is save storage.
$connector = new Connector(__DIR__ . '/db');
```

For example create table and select table:

```php
$connector = new Connector(__DIR__ . '/db');

// arg1 = tableName, arg2 = driver(default = ListDriver)
$userManager = $connector->connection('users');
```

For example drop table:

```php
$connector = new Connector(__DIR__ . '/db');

// Exception will occur if it fails.
try {
	$connector->drop('users');
} catch (Exception $e) {
	// fail process..
}
```


### Dirver

- List
    - list (Rmtram\SimpleTextDb\Drivers\ListDriver)
    - hash (Rmtram\SimpleTextDb\Drivers\HashMapDriver)

#### ListDriver

- methods
	- all()
	- find(Closure $callable)
	- add(array $value)
	- bulkAdd(array $values)
	- update(array $update, Closure || null $callable)
	- delete(Closure $callable)
	- truncate()
	
- Where
    - eq($key, $val)
    - notEq($key, $val)
    - lt($key, $val)
    - lte($key, $val)
    - gt($key, $val)
    - gte($key, $val)
    - like($key, $val)
    - notLike($key, $val)
    - orEq($key, $val)
    - orNotEq($key, $val)
    - orLt($key, $val)
    - orLte($key, $val)
    - orGt($key, $val)
    - orGte($key, $val)
    - orLike($key, $val)
    - orNotLike($key, $val)
	
For example:
	
```php
$connector = new Connector(__DIR__ . '/db');
$userManager = $connector->connection('users');
$userManager->add(['id' => 1, 'name' => 'example']);
$user = $userManager->where(function(Where $where) {
    $where->eq('id', 1);
})->first();
var_dump($user);
// array('id' => 1, 'name' => 'example')
```

##### all()

```php
var_dump($userManager->all());
// e.g. 
// [
//  	['id' => 1, 'name' => 'user1'], 
//		['id' => 2, 'name' => 'user2']
// ]
```

##### find(Closure $callable)

- find in result(Result class)
	- get()
	- first()
	- exists()
	- count()
	- call(Closure $callable)

For example result pattern:

```php
$result = $userManager->find(function(Where $where) {
	$where->eq('id', 1);
});

$result->get();
// [['id' => 1, 'name' => 'user'1]]

$result->first();
// ['id' => 1, 'name' => 'user1']

$result->count();
// 1

$result->exists();
// true

// (int)$index is list pointer.
// (array)$row => ['id' => 1, 'name' => 'user1'] 
$result->call(function($index, $row) {
	// any process.
});

```

For example where pattern:

```php
$userManager->find(function(Where $where) {
	// id = 1
	$where->eq('id', 1);
	
	// id != 1
	$where->notEq('id', 1);
	
	// id > 1
	$where->gt('id', 1);
	
	// id >= 1
	$where->gte('id', 1);

	// id < 1
	$where->lt('id', 1);
	
	// id <= 1
	$where->lte('id', 1);
	
	// Regex pattern => /^us$/ 
	$where->like('name', 'us');
	
	// Regex pattern => /^us/ 
	$where->like('name', '%us');
	
	// Regex pattern => /us$/ 
	$where->like('name', 'us%')
	
	// Regex pattern => /us/ 
	$where->like('name', '%us%')
	
	// Opposite of LIKE
	$where->notLike('name', '%us%')
});
```

For example where or:

```php

// (id = 10 || name = user1)
$userManager->find(function(Where $where) {
	$where->eq('id', 10);
	$where->orEq('name', 'user1')
})->first();
// ['id' => 1, 'name' => 'user1']

// (id = 10 && name = user1)
$userManager->find(function(Where $where) {
	$where->eq('id', 10);
	$where->eq('name', 'user1')
})->first();
// null
```

##### add(array $value)

```php
$userManager->add(['id' => 3, 'name' => 'user3']);
// true or false
```

##### bulkAdd(array $values)

```php
$values = [
    ['id' => 4, 'name' => 'user4'],
    ['id' => 5, 'name' => 'user5']
];
$userManager->bulkAdd($values);
// true or false or exception.
```

##### update(array $update, Closure || null $callable)

```php
$bool = $userManager->update(['name' => 'example', 'age' => 20], function(Where $where) {
    $where->eq('id', 1);
});
var_dump($bool) // true || false
// match update data:   ['id' => 1, 'name' => 'example', 'age' => 20]
// unmatch update data: ['id' => 2, 'name' => 'user2']

// item all update.
$bool = $userManager->update(['name' => 'demo', 'age' => 25]);
var_dump($bool) // true || false
// match! update data: ['id' => 1, 'name' => 'demo', 'age' => 25]
// match! update data: ['id' => 2, 'name' => 'demo', 'age' => 25]
```

##### delete(Closure $callable)

```php
$deleted = $userManager->delete(function(Where $where) {
    $where->eq('id', 1);
});
var_dump($deleted) // true

var_dump($userManager->find(function(Where $where) {
    $where->eq('id', 1);
})->exists());
// false

$deleted = $userManager->delete(function(Where $where) {
    $where->eq('id', 'ff3i0920jwsss');
});
var_dump($deleted) // false

$deleted = $userManager->delete(function(Where $where) {
});
var_dump($deleted) // false
```

##### truncate()

Delete all item.
 
```php
$userManager->truncate();
var_dump($userManager->all());
// []
```

#### HashMapDriver

- methods
    - set(string $key, mixed $val)
    - get(string $key)
    - all()
    - has(string $key)
    - delete(string|array $key)
    - truncate()

For example:
	
```php
$connector = new Connector(__DIR__ . '/db');

/** @var Rmtram\SimpleTextDb\Drivers\HashMapDriver $bookManager */
$bookManager = $connector->connection('books', 'hash');
$bookManager->set('dummy1', 'example');
var_dump($bookManager->get('dummy1'));
// 'example'
```

##### set(string $key, mixed $val)

```php
$bookManager->set('dummy1', ['name' => 'dummy1', 'age' => 20]);

// update
$bookManager->set('dummy1', ['name' => 'dummy1', 'age' => 25]);
var_dump($bookManager->get('dummy1'));
// ['name' => 'dummy1', 'age' => 25]
```

##### get(string $key)

```php
$bookManager->set('dummy1', 1);
$bookManager->set('dummy2', 2);

var_dump($bookManager->get('dummy1'));
// 1

var_dump($bookManager->get('none'));
// null

var_dump($bookManager->get(['dummy1', 'dummy2']));
// [1, 2]
```

##### all()

```php
$bookManager->set('dummy1', 1);
$bookManager->set('dummy2', 2);
$bookManager->set('dummy3', 3);

var_dump($bookManager->all());
// [1, 2, 3]
```

##### has(string $key)

```php
$bookManager->set('dummy1', 1);

var_dump($bookManager->has('dummy1'));
// true

var_dump($bookManager->has('none'));
// false
```

##### delete(string|array $key)

```php
$bookManager->set('dummy1', 1);
$bookManager->set('dummy2', 2);
$bookManager->set('dummy3', 3);

$deleted = $bookManager->delete(['dummy1', 'dummy2']);

var_dump($deleted);
// true

var_dump($bookManager->all());
// [3]
```

##### truncate()

```php
$bookManager->set('dummy1', 1);
$bookManager->set('dummy2', 2);
$bookManager->set('dummy3', 3);

$bookManager->truncate();

var_dump($bookManager->all());
// []
```

### Util


#### Unique

- methods
    - id()
    
##### id()

```php
echo Rmtram\SimpleTextDb\Util\Unique::id();
// e.g. microtime() + random string.
// 14615204803576vk9fs8xtiolbah54npy0er7w2m3gq1j
```

# Support version

- PHP 5.4
- PHP 5.5
- PHP 5.6
- PHP 7
- HHVM