<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Storage;

use Ailos\Sdk\Storage\InMemoryTokenStore;
use Ailos\Sdk\Storage\TokenKeys;
use PHPUnit\Framework\TestCase;

class StorageTest extends TestCase
{
    private InMemoryTokenStore $store;

    protected function setUp(): void
    {
        $this->store = new InMemoryTokenStore();
    }

    // set e get
    public function test_stores_and_retrieves_a_value(): void
    {
        $this->store->set('key', 'value');

        $this->assertSame('value', $this->store->get('key'));
    }

    public function test_stores_any_type_of_value(): void
    {
        $object = new \stdClass();
        $object->name = 'token';

        $this->store->set('obj', $object);

        $this->assertSame($object, $this->store->get('obj'));
    }

    public function test_returns_null_for_nonexistent_key(): void
    {
        $this->assertNull($this->store->get('nonexistent'));
    }

    // has
    public function test_has_returns_true_for_existing_key(): void
    {
        $this->store->set('key', 'value');

        $this->assertTrue($this->store->has('key'));
    }

    public function test_has_returns_false_for_nonexistent_key(): void
    {
        $this->assertFalse($this->store->has('nonexistent'));
    }

    // forget
    public function test_forgets_a_key(): void
    {
        $this->store->set('key', 'value');
        $this->store->forget('key');

        $this->assertFalse($this->store->has('key'));
        $this->assertNull($this->store->get('key'));
    }

    public function test_forget_on_nonexistent_key_does_not_throw(): void
    {
        $this->expectNotToPerformAssertions();

        $this->store->forget('nonexistent');
    }

    // TTL
    public function test_value_with_ttl_is_accessible_before_expiry(): void
    {
        $this->store->set('key', 'value', 60);

        $this->assertTrue($this->store->has('key'));
        $this->assertSame('value', $this->store->get('key'));
    }

    public function test_value_with_zero_ttl_does_not_expire(): void
    {
        $this->store->set('key', 'value', 0);

        $this->assertTrue($this->store->has('key'));
    }

    // flush
    public function test_flush_clears_all_stored_values(): void
    {
        $this->store->set('key1', 'value1');
        $this->store->set('key2', 'value2');

        $this->store->flush();

        $this->assertFalse($this->store->has('key1'));
        $this->assertFalse($this->store->has('key2'));
    }

    // TokenKeys
    public function test_token_keys_are_correct(): void
    {
        $this->assertSame('ailos.access_token', TokenKeys::ACCESS_TOKEN);
        $this->assertSame('ailos.auth_id', TokenKeys::AUTH_ID);
        $this->assertSame('ailos.jwt', TokenKeys::JWT);
    }

    public function test_can_store_and_retrieve_using_token_keys(): void
    {
        $this->store->set(TokenKeys::ACCESS_TOKEN, 'token-abc');
        $this->store->set(TokenKeys::JWT, 'jwt-xyz');

        $this->assertSame('token-abc', $this->store->get(TokenKeys::ACCESS_TOKEN));
        $this->assertSame('jwt-xyz', $this->store->get(TokenKeys::JWT));
    }
}