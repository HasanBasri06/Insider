<?php

namespace App\Repositories;

use App\Models\Message;
use Illuminate\Support\Collection;

interface MessageRepositoryInterface
{
    public function getAllMessages(int $limit, ?string $filter): ?Collection;

    public function getMessageById(int $limit): ?Message;

    public function markAsSent(int $id, string $messageId, string $sentAt): void;

    public function markAsFailed(int $id, string $errorMessage): void;

    /**
     * @return void
     */
    public function getSentMessages(): Collection;

    /**
     * @return void
     */
    public function findByIdWithUser(int $id): ?Message;
}
