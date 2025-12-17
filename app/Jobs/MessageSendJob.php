<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
            return;
        }

        $messageService->sendMessage($message);
    }
}
