<?php

namespace App\Console\Commands\ApiKey;

use App\Models\ApiKey;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;

class GenerateApiKeyCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:api-key:generate { --valid-till= : when token expires. Leave empty to make dateless token }';

    /**
     * @var string
     */
    protected $description = 'Generates ApiKey';

    public function handle()
    {
        $apiKey = new ApiKey();
        $apiKey->id = Uuid::uuid7();
        $apiKey->created_at = new \DateTimeImmutable('now');

        $validTill = $this->option('valid-till');
        if ($validTill) {
            $apiKey->valid_till = new \DateTimeImmutable($validTill);
        }

        $apiKey->save();

        $this->output->text("ApiKey created: `{$apiKey->toTokenValue()}`");
        return self::SUCCESS;
    }
}
