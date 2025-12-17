<?php 

namespace App\Repositories;

use App\Models\Message;
use Illuminate\Support\Collection;

interface MessageRepositoryInterface {
    /**
     * @param int $limit
     * @param string $filter
     * @return void
     */
    public function getAllMessages(int $limit, string $filter): Collection;
    /**
     * @param int $limit
     * @return ?Message
     */
    public function getMessageById(int $limit): ?Message;
    /**
     * @param int $id
     * @param string $messageId
     * @param string $sentAt
     * @return void
     */
    public function markAsSent(int $id, string $messageId, string $sentAt): void;
    /**
     * @param int $id
     * @param string $errorMessage
     * @return void
     */
    public function markAsFailed(int $id, string $errorMessage): void;
    /**
     * @return void
     */
    public function getSentMessages(): Collection;
    /**
     * @param int $id
     * @return void
     */
    public function findByIdWithUser(int $id): ?Message;
}