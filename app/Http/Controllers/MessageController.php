<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\ResponseTrait;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    use ResponseTrait;

    public function __construct(private MessageService $messageService) {}

    public function getAllMessages(Request $request): AnonymousResourceCollection
    {
        $limit = $request->query('limit', 10) ?? 10;
        $filter = $request->has('filter') ? $request->query('filter') : null;
        $messages = $this->messageService->getAllMessagesWithUser($limit, $filter);

        return MessageResource::collection($messages);
    }

    /**
     * @return MessageResource
     */
    public function getMessageById(int $id): MessageResource|Response
    {
        $message = $this->messageService->getMessageById($id);
        if (! $message) {
            return $this->error('Not found message', 404);
        }

        return new MessageResource($message);
    }
}
