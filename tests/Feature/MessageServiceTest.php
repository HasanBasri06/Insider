<?php

namespace Tests\Feature;

use App\Enums\StatusEnum;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class MessageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_message_successfully()
    {
        Http::fake([
            '*' => Http::response([
                'messageId' => 'abc123',
            ], 200),
        ]);

        Cache::flush();

        $message = Message::factory()->create([
            'content' => 'Hello world',
        ]);

        $service = app(MessageService::class);
        $service->sendMessage($message);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => StatusEnum::SENT->value,
        ]);

        $this->assertNotNull(Redis::get("message:{$message->id}"));
    }
}
