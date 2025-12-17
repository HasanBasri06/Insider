<?php

namespace Tests\Feature;

use App\Enums\StatusEnum;
use App\Jobs\MessageSendJob;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SendMessageJobTest extends TestCase
{
    use RefreshDatabase;
    public function test_job_is_dispatched()
    {
        Queue::fake();
        $user = User::factory()->create();
        $message = Message::factory()->create([
            'user_id' => $user->id,
        ]);
        MessageSendJob::dispatch($message->id);
        Queue::assertPushed(MessageSendJob::class);
    }

    public function test_job_executes_service()
    {
        Http::fake([
            '*' => Http::response(['messageId' => 'xyz'], 200),
        ]);
        $user = User::factory()->create();
        $message = Message::factory()->create([
            'user_id' => $user->id,
        ]);
        dispatch_sync(new MessageSendJob($message->id));
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => StatusEnum::SENT->value,
        ]);
    }
}
