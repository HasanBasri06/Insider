<?php

namespace App\Console\Commands;

use App\Enums\StatusEnum;
use App\Jobs\MessageSendJob;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendMessagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch messages 2 by 2 every 5 seconds';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $messages = Message::where('status', StatusEnum::PENDING->value)
            ->orderBy('id')
            ->get();

        $delaySeconds = 0;
        foreach ($messages->chunk(2) as $chunk) {
            foreach ($chunk as $message) {
                MessageSendJob::dispatch($message->id)
                    ->delay(Carbon::now()->addSeconds($delaySeconds));
            }

            $delaySeconds += 5;
        }

        $this->info('Jobs dispatched successfully.');

        return Command::SUCCESS;
    }
}
