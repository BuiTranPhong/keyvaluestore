<?php

namespace Tests\Feature\App\Http\Controllers\ItemController;

use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /**
     * Setup before tests
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = create(User::class);
        $this->actingAs($this->user, 'api');
    }

    /**
     * test error response will be returned when empty json body is provided
     *
     * @return void
     */
    public function test_error_response_will_be_returned_when_empty_json_body_is_provided()
    {
        $this->json(
            'POST',
            '/api/object',
            []
        )
            ->assertStatus(422)
            ->assertJson([
                'error' => 'Invalid request body provided',
            ]);
    }

    /**
     * test error response will be returned when provided json body is sequential array
     *
     * @return void
     */
    public function test_error_response_will_be_returned_when_provided_json_body_is_sequential_array()
    {
        $this->json(
            'POST',
            '/api/object',
            [1, 2]
        )
            ->assertStatus(422)
            ->assertJson([
                'error' => 'Invalid request body provided',
            ]);
    }

    /**
     * test object will be stored when single key value is provided
     *
     * @return void
     */
    public function test_object_will_be_stored_when_single_key_value_is_provided()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(111111));

        $this->json(
            'POST',
            '/api/object',
            [
                'foo' => 'bar',
            ],
        )
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Successfully created object(s)',
                'data'    => [
                    [
                        'key'       => 'foo',
                        'value'     => 'bar',
                        'timestamp' => 111111,
                        'user_id'   => $this->user->id,
                    ],
                ],
            ]);

        $itemFoo = Item::firstWhere(['key' => 'foo']);

        $this->assertDatabaseHas(
            'items',
            [
                'key'       => 'foo',
                'value'     => 'bar',
                'timestamp' => 111111,
                'user_id'   => $this->user->id,
            ],
        );
        $this->assertDatabaseHas(
            'item_histories',
            [
                'item_id'   => $itemFoo->id,
                'value'     => 'bar',
                'timestamp' => 111111,
                'user_id'   => $this->user->id,
            ],
        );

        Carbon::setTestNow();
    }

    /**
     * test object will be stored when a multi key value object is provided
     *
     * @return void
     */
    public function test_object_will_be_stored_when_a_multi_key_value_object_is_provided()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(111111));

        $this->json(
            'POST',
            '/api/object',
            [
                'foo' => 'bar',
                'bar' => 'baz',
            ],
        )
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Successfully created object(s)',
                'data'    => [
                    [
                        'key'       => 'foo',
                        'value'     => 'bar',
                        'timestamp' => 111111,
                        'user_id'   => $this->user->id,
                    ],
                    [
                        'key'       => 'bar',
                        'value'     => 'baz',
                        'timestamp' => 111111,
                        'user_id'   => $this->user->id,
                    ],
                ],
            ]);

        $itemFoo = Item::firstWhere(['key' => 'foo']);
        $itemBar = Item::firstWhere(['key' => 'bar']);

        $this->assertDatabaseHas(
            'items',
            [
                'key'       => 'foo',
                'value'     => 'bar',
                'timestamp' => 111111,
                'user_id'   => $this->user->id,
            ],
        );
        $this->assertDatabaseHas(
            'items',
            [
                'key'       => 'bar',
                'value'     => 'baz',
                'timestamp' => 111111,
                'user_id'   => $this->user->id,
            ],
        );
        $this->assertDatabaseHas(
            'item_histories',
            [
                'item_id'   => $itemFoo->id,
                'value'     => 'bar',
                'timestamp' => 111111,
                'user_id'   => $this->user->id,
            ],
        );
        $this->assertDatabaseHas(
            'item_histories',
            [
                'item_id'   => $itemBar->id,
                'value'     => 'baz',
                'timestamp' => 111111,
                'user_id'   => $this->user->id,
            ],
        );

        Carbon::setTestNow();
    }

    /**
     * test object will be stored when single key value is provided
     *
     * @return void
     */
    public function test_existing_object_will_be_updated_if_provided_key_already_exists()
    {
        $validItem = create(Item::class, ['key' => 'foo', 'value' => 'old_value']);

        Carbon::setTestNow(Carbon::createFromTimestamp(111111));

        $this->json(
            'POST',
            '/api/object',
            [
                'foo' => 'bar',
            ],
        )
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Successfully created object(s)',
                'data'    => [
                    [
                        'key'       => 'foo',
                        'value'     => 'bar',
                        'timestamp' => 111111,
                        'user_id'   => $this->user->id,
                    ],
                ],
            ]);

        $itemFoo = Item::firstWhere(['key' => 'foo']);

        $this->assertDatabaseHas(
            'items',
            [
                'key'       => 'foo',
                'value'     => 'bar',
                'timestamp' => 111111,
                'user_id'   => $this->user->id,
            ],
        );

        $this->assertDatabaseHas(
            'item_histories',
            [
                'item_id'   => $itemFoo->id,
                'value'     => 'bar',
                'timestamp' => 111111,
                'user_id'   => $this->user->id,
            ],
        );

        Carbon::setTestNow();
    }
}
