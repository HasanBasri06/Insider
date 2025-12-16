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
        if (strlen($message) > 160) {
            throw new Exception("Character limit exceeded");
        }

        try {
            $response = Http::post(config('services.webhook.url'), [
                'phone' => $message->phone_number,
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
            $this->messageRepository
                ->markAsFailed($message->id, $th->getMessage());
        }
    }
}