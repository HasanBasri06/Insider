<?php 

namespace App\Repositories;

use Illuminate\Support\Collection;

interface MessageRepositoryInterface {
    /**
     * @param int $limit
     * @return Collection
     */
    public function getPendingMessages(int $limit): Collection;
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
}