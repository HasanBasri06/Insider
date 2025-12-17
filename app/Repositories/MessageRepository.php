<?php 

namespace App\Repositories;

use App\Enums\StatusEnum;
use App\Models\Message;
use Illuminate\Support\Collection;

class MessageRepository implements MessageRepositoryInterface {
    /**
     * @param Message $message
     */
    public function __construct(
        private Message $message,
    ) {
        $this->message = $message;
    }
    /**
     * @param int $limit
     * @return Collection
     */
    public function getPendingMessages(int $limit): Collection
    {
        return $this->message
            ->where('status', StatusEnum::PENDING->value)
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }
    /**
     * @param int $id
     * @param string $messageId
     * @param string $sentAt
     * @return void
     */
    public function markAsSent(int $id, string $messageId, string $sentAt): void {
        $this->message
            ->where('id', $id)
            ->update([
                'status' => StatusEnum::SENT->value,
                'message_id' => $messageId,
                'sent_at' => $sentAt
            ]);
    }
    /**
     * @param int $id
     * @param string $errorMessage
     * @return void
     */
    public function markAsFailed(int $id, string $errorMessage): void {
        $this->message
            ->where('id', $id)
            ->update([
                'status' => StatusEnum::FAILED->value,
                'error_message' => $errorMessage
            ]);
    }
    /**
     * @return Collection
     */
    public function getSentMessages(): Collection
    {
        return $this->message
            ->where('status', StatusEnum::SENT->value)
            ->orderByDesc('sent_at')
            ->get();
            
    }
    public function findByIdWithUser(int $id): Message
    {
        return $this
            ->message
            ->with('user')
            ->find($id);
    }
}