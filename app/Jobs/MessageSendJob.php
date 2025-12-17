<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\MessageService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Redis;

class MessageSendJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param int $messageId
     */
    public function __construct(public int $messageId)
    {
        $this->messageId = $messageId;
    }

    public function handle(MessageService $messageService): void
    {
        $message = $messageService->getMessageById($this->messageId);
        if (!$message) {
            throw new Exception(
                "Message not found. ID: {$this->messageId}"
            );
        }

        $messageService->sendMessage($message);
        Redis::set('message:'.$message->id, $message->toJson());
    }
}
