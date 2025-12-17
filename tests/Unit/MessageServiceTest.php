<?php

namespace Tests\Unit;

use App\Enums\StatusEnum;
use App\Models\Message;
use App\Repositories\MessageRepositoryInterface;
use App\Services\MessageService;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class MessageServiceTest extends TestCase
{
    protected $messageRepository;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.webhook.url' => 'http://fake-webhook-url']);

        $this->messageRepository = Mockery::mock(MessageRepositoryInterface::class);
        $this->service = new MessageService($this->messageRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_send_message_calls_write_error_if_content_too_long()
    {
        $message = new Message([
            'id' => 1,
            'content' => str_repeat('a', 200),
        ]);

        $this->messageRepository
            ->shouldReceive('markAsFailed')
            ->once()
            ->with($message->id, 'Character limit exceeded');

        $this->service->sendMessage($message);
    }

    public function test_send_message_calls_mark_as_sent_on_successful_response()
    {
        $message = new Message([
            'id' => 2,
            'content' => 'Valid content',
            'user' => (object) ['phone_number' => '1234567890'],
        ]);

        Http::fake([
            '*' => Http::response(['messageId' => 'abc123'], 200),
        ]);

        $this->messageRepository
            ->shouldReceive('markAsSent')
            ->once()
            ->with(
                $message->id,
                'abc123',
                Mockery::type('string')
            );

        $this->service->sendMessage($message);
    }

    public function test_send_message_calls_write_error_on_failed_response()
    {
        $message = new Message([
            'id' => 3,
            'content' => 'Valid content',
            'user' => (object) ['phone_number' => '1234567890'],
        ]);

        Http::fake([
            '*' => Http::response(null, 500),
        ]);

        $this->messageRepository
            ->shouldReceive('markAsFailed')
            ->once()
            ->with(
                $message->id,
                Mockery::on(function ($msg) {
                    return str_contains($msg, 'Webhook request failed');
                })
            );

        $this->service->sendMessage($message);
    }

    public function test_get_message_by_id_calls_repository()
    {
        $id = 5;

        $message = new Message(['id' => $id]);
        $this->messageRepository
            ->shouldReceive('findByIdWithUser')
            ->once()
            ->with($id)
            ->andReturn($message);

        $result = $this->service->getMessageById($id);

        $this->assertEquals($message, $result);
    }

    public function test_get_all_messages_with_user_calls_repository()
    {
        $limit = 10;
        $filter = 'sent';

        $collection = collect([
            new Message(['id' => 1]),
            new Message(['id' => 2]),
        ]);

        $this->messageRepository
            ->shouldReceive('getAllMessages')
            ->once()
            ->with($limit, $filter)
            ->andReturn($collection);

        $result = $this->service->getAllMessagesWithUser($limit, $filter);

        $this->assertEquals($collection, $result);
    }

    public function test_get_message_by_id_with_user_calls_repository()
    {
        $id = 7;

        $message = new Message(['id' => $id]);
        $this->messageRepository
            ->shouldReceive('getMessageById')
            ->once()
            ->with($id)
            ->andReturn($message);

        $result = $this->service->getMessageByIdWithUser($id);

        $this->assertEquals($message, $result);
    }
}
