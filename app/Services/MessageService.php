<?php

namespace App\Services;

use App\Models\Message;
use App\Repositories\MessageRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class MessageService {
    /**
     * @param MessageRepositoryInterface $messageRepository
     */
    public function __construct(
        private MessageRepositoryInterface $messageRepository
    ) {
        $this->messageRepository = $messageRepository;
    }
    /**
     * @param Message $message
     * @throws Exception
     * @return void
     */
    public function sendMessage(Message $message): void {
        if (mb_strlen($message->content) > 160) {
            $this->writeError($message->id, "Character limit exceeded");
            return;
        }

        try {
            $response = Http::post(config('services.webhook.url'), [
                'to' => $message->user->phone_number,
                'content' => $message->content
            ]);

            if (!$response->successful()) {
                throw new Exception("Webhook request failed");
            } 

            $messageId = $response->json('messageId');
            $sentAt = Carbon::now()->toDateTimeString();

            $this->messageRepository
                ->markAsSent(
                    $message->id,
                    $messageId,
                    $sentAt
                );
        } catch (\Throwable $th) {
            $this->writeError($message->id, $th->getMessage());
        }
    }
    public function getMessageById(int $id): Message {
        return $this
            ->messageRepository
            ->findByIdWithUser($id);
    }
    public function writeError(int $messageId, string $message) {
        $this->messageRepository
            ->markAsFailed($messageId, $message);
    }
}