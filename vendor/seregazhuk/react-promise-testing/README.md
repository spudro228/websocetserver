# ReactPHP Promises Testing
A library that provides a set of convenient assertions for testing ReactPHP promises.
Under the hood uses [clue/php-block-react](https://github.com/clue/php-block-react) to block promises.

[![Build Status](https://travis-ci.org/seregazhuk/php-react-promise-testing.svg?branch=master)](https://travis-ci.org/seregazhuk/php-react-promise-testing)
[![Maintainability](https://api.codeclimate.com/v1/badges/689230cdae09d2e32600/maintainability)](https://codeclimate.com/github/seregazhuk/php-react-promise-testing/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/689230cdae09d2e32600/test_coverage)](https://codeclimate.com/github/seregazhuk/php-react-promise-testing/test_coverage)

When testing asynchronous code and promises things can be a bit tricky. This library provides a set of convenient 
assertions for testing ReactPHP promises. 

**Table of Contents**
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Assertions](#assertions)
    - [assertPromiseFulfills()](#assertpromisefulfills)
    - [assertPromiseFulfillsWith()](#assertpromisefulfillswith)
    - [assertPromiseRejects()](#assertpromiserejects())
    - [assertPromiseRejectsWith()](#assertpromiserejectswith)
    
- [Helpers](#helpers)
    - [waitForPromiseToFulfill()](#waitforpromisetofulfill)
    - [waitForPromise()](#waitforpromise)
    
## Installation

### Dependencies
Library requires PHP 5.6.0 or above.

The recommended way to install this library is via [Composer](https://getcomposer.org). 
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

See also the [CHANGELOG](CHANGELOG.md) for details about version upgrades.

```
composer require seregazhuk/react-promise-testing
```

## Quick Start
To start using it extend your test classes from `seregazhuk\React\PromiseTesting\TestCase` class, 
which itself extends PHPUnit `TestCase`:
 
```php
class MyTest extends TestCase
{
    /** @test */
    public function promise_fulfills()
    {
        $resolve = function(callable $resolve, callable $reject) {
            return $resolve('Promise resolved!');
        };

        $cancel = function(callable $resolve, callable $reject) {
            $reject(new \Exception('Promise cancelled!'));
        };

        $promise = new Promise($resolve, $cancel);
        $this->assertPromiseFulfills($promise);
    }
}

```

Test above checks that a specified promise fulfills. If the promise was rejected this test fails.

## Assertions

### assertPromiseFulfills()

`public function assertPromiseFulfills(PromiseInterface $promise, $timeout = null)`

The test fails if the `$promise` rejects. 

You can specify `$timeout` in seconds to wait for promise to be resolved.
If the promise was not fulfilled in specified timeout the test fails. When not specified, timeout is set to 2 seconds.

```php
class PromiseFulfillsTest extends TestCase
{
    /** @test */
    public function promise_fulfills()
    {
        $deferred = new Deferred();
        $deferred->reject();
        $this->assertPromiseFulfills($deferred->promise(), 1);
    }
}
```

```bash
PHPUnit 5.7.23 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 189 ms, Memory: 4.00MB

There was 1 failure:

1) seregazhuk\React\PromiseTesting\tests\PromiseFulfillTest::promise_fulfills
Failed asserting that promise fulfills. Promise was rejected.
```

### assertPromiseFulfillsWith()
`assertPromiseFulfillsWith(PromiseInterface $promise, $value, $timeout = null)`

The test fails if the `$promise` doesn't fulfills with a specified `$value`.

You can specify `$timeout` in seconds to wait for promise to be fulfilled.
If the promise was not fulfilled in specified timeout the test fails. 
When not specified, timeout is set to 2 seconds.

```php
class PromiseFulfillsWithTest extends TestCase
{
    /** @test */
    public function promise_fulfills_with_a_specified_value()
    {
        $deferred = new Deferred();
        $deferred->resolve(1234);
        $this->assertPromiseFulfillsWith($deferred->promise(), 1);
    }
}
```

```bash
PHPUnit 5.7.23 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 180 ms, Memory: 4.00MB

There was 1 failure:

1) seregazhuk\React\PromiseTesting\tests\PromiseFulfillsWithTest::promise_fulfills_with_a_specified_value
Failed asserting that promise fulfills with a specified value. 
Failed asserting that 1234 matches expected 1.
```

### assertPromiseRejects()
`assertPromiseRejects(PromiseInterface $promise, $timeout = null)`

The test fails if the `$promise` fulfills.

You can specify `$timeout` in seconds to wait for promise to be resolved.
If the promise was not fulfilled in specified timeout, it rejects with `React\Promise\Timer\TimeoutException` . 
When not specified, timeout is set to 2 seconds.

```php
class PromiseRejectsTest extends TestCase
{
    /** @test */
    public function promise_rejects()
    {
        $deferred = new Deferred();
        $deferred->resolve();
        $this->assertPromiseRejects($deferred->promise());
    }
}
```

```bash
PHPUnit 5.7.23 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 175 ms, Memory: 4.00MB

There was 1 failure:

1) seregazhuk\React\PromiseTesting\tests\PromiseRejectsTest::promise_rejects
Failed asserting that promise rejects. Promise was fulfilled.
```

### assertPromiseRejectsWith()
`assertPromiseRejectsWith(PromiseInterface $promise, $reasonExceptionClass, $timeout = null)`

The test fails if the `$promise` doesn't reject with a specified exception class.

You can specify `$timeout` in seconds to wait for promise to be resolved.
If the promise was not fulfilled in specified timeout, it rejects with `React\Promise\Timer\TimeoutException`. 
When not specified, timeout is set to 2 seconds.

```php
class PromiseRejectsWithTest extends TestCase
{
    /** @test */
    public function promise_rejects_with_a_specified_reason()
    {
        $deferred = new Deferred();
        $deferred->reject(new \LogicException());
        $this->assertPromiseRejectsWith($deferred->promise(), \InvalidArgumentException::class);
    }
}
```

```bash
PHPUnit 5.7.23 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 136 ms, Memory: 4.00MB

There was 1 failure:

1) seregazhuk\React\PromiseTesting\tests\PromiseRejectsWithTest::promise_rejects_with_a_specified_reason
Failed asserting that promise rejects with a specified reason.
Failed asserting that LogicException Object (...) is an instance of class "InvalidArgumentException".
```


## Helpers

### waitForPromiseToFulfill()
`function waitForPromise(PromiseInterface $promise, $timeout = null)`.

This helper can be used when you want to resolve a promise and get the resolved value.

Tries to resolve a `$promise` in a specified `$timeout` seconds and returns resolved value. If `$timeout` is not 
set uses 2 seconds by default. The test fails if the `$promise` doesn't fulfill.

```php
class WaitForPromiseToFulfillTest extends TestCase
{
    /** @test */
    public function promise_fulfills()
    {
        $deferred = new Deferred();

        $deferred->reject(new \Exception());
        $value = $this->waitForPromiseToFulfill($deferred->promise());
    }
}
```

```bash
PHPUnit 5.7.23 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 223 ms, Memory: 6.00MB

There was 1 failure:

1) seregazhuk\React\PromiseTesting\tests\WaitForPromiseToFulfillTest::promise_fulfills
Failed to fulfill a promise. It was rejected with Exception.
```

### waitForPromise()
`function waitForPromise(PromiseInterface $promise, $timeout = null)`.

Tries to resolve a specified `$promise` in a specified `$timeout` seconds. If `$timeout` is not set uses 2 
seconds by default. If the promise fulfills returns a resolved value, otherwise throws an exception. If the 
promise rejects throws the rejection reason, if the promise doesn't fulfill in a specified `$timeout` throws 
`React\Promise\Timer\TimeoutException`.

This helper can be useful when you need to get the value from the fulfilled promise in a synchronous way:

```php
$value = $this->waitForPromise($cahce->get('key'));
```
