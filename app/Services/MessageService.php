<?php

namespace App\Services;

use App\Models\Message;
use App\Repositories\MessageRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class MessageService
{
    private string $webhookUrl;

    private const MAX_LENGTH = 160;

    public function __construct(
        private MessageRepositoryInterface $messageRepository
    ) {
        $this->webhookUrl = config('services.webhook.url');
    }

    public function sendMessage(Message $message): void
    {
        if (mb_strlen($message->content) > self::MAX_LENGTH) {
            $this->writeError($message->id, 'Character limit exceeded');

            return;
        }

        try {
            $response = Http::post($this->webhookUrl, [
                'to' => $message->user->phone_number,
                'content' => $message->content,
            ]);

            if (! $response->successful()) {
                throw new Exception('Webhook request failed');
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

    public function getMessageById(int $id): ?Message
    {
        return $this
            ->messageRepository
            ->findByIdWithUser($id);
    }

    public function writeError(int $messageId, string $message): void
    {
        $this->messageRepository
            ->markAsFailed($messageId, $message);
    }

    public function getAllMessagesWithUser(int $limit, ?string $filter): ?Collection
    {
        return $this->messageRepository
            ->getAllMessages($limit, $filter);
    }

    public function getMessageByIdWithUser(int $limit): ?Message
    {
        return $this->messageRepository
            ->getMessageById($limit);
    }
}
