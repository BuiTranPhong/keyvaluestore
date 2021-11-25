<?php

namespace Tests\Feature\App\Http\Controllers\ItemController;

use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class GetAllRecordsTest extends TestCase
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
     * test correct data will be returned when there are multi version of same item
     */
    public function test_correct_data_will_be_returned_when_there_are_multi_version_of_same_item()
    {
        $oldItem = create(Item::class, ['key' => 'valid_key_1', 'value' => 'old_value']);
        $oldItem->update(['value' => 'old_value_1']);
        $oldItem->update(['value' => 'valid_value_1']);

        create(Item::class, ['key' => 'valid_key_2', 'value' => 'valid_value_2']);

        create(Item::class, ['key' => 'valid_json', 'value' => ['foo' => 'bar']]);

        $this->json('GET', '/api/object/get_all_records')
            ->assertOk()
            ->assertJson([
                'message' => 'Successfully fetched object',
                'data'    => [
                    'valid_key_1' => 'valid_value_1',
                    'valid_key_2' => 'valid_value_2',
                    'valid_json' => [
                        'foo' => 'bar',
                    ],
                ],
            ]);
    }
}
