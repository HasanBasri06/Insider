<?php

namespace App\Repositories;

use App\Enums\StatusEnum;
use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MessageRepository implements MessageRepositoryInterface
{
    public function __construct(
        private Message $message,
    ) {
        $this->message = $message;
    }

    public function getAllMessages(int $limit, ?string $filter): ?Collection
    {
        return $this->message
            ->with('user')
            ->when($filter, function (Builder $query) use ($filter) {
                $query->where('status', $filter);
            })
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }

    public function getMessageById(int $id): ?Message
    {
        return $this->message
            ->with('user')
            ->where('id', $id)
            ->first();
    }

    public function markAsSent(int $id, string $messageId, string $sentAt): void
    {
        $this->message
            ->where('id', $id)
            ->update([
                'status' => StatusEnum::SENT->value,
                'message_id' => $messageId,
                'sent_at' => $sentAt,
            ]);
    }

    public function markAsFailed(int $id, string $errorMessage): void
    {
        $this->message
            ->where('id', $id)
            ->update([
                'status' => StatusEnum::FAILED->value,
                'error_message' => $errorMessage,
            ]);
    }

    public function getSentMessages(): Collection
    {
        return $this->message
            ->where('status', StatusEnum::SENT->value)
            ->orderByDesc('sent_at')
            ->get();

    }

    /**
     * @return Message|\Illuminate\Database\Eloquent\Builder<Message>
     */
    public function findByIdWithUser(int $id): ?Message
    {
        return $this
            ->message
            ->with('user')
            ->find($id);
    }
}
