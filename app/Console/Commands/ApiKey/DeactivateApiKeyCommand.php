<?php

namespace App\Console\Commands\ApiKey;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class DeactivateApiKeyCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:api-key:deactivate { id : ApiKey id or Authorization token contents }';

    /**
     * @var string
     */
    protected $description = 'Softly deletes ApiKey';

    public function handle()
    {
        $id = $this->argument('id');

        if (!is_string($id)) {
            $this->error("Empty id provided");
            return self::INVALID;
        }

        $uuid = ApiKey::extractIdFromString($id);
        $apiKey = ApiKey::query()->find($uuid);

        if ($apiKey === null) {
            $this->error("No such ApiKey");
            return self::INVALID;
        }

        $apiKey->deleted_at = new \DateTimeImmutable('now');
        $apiKey->save();

        $this->output->text("ApiKey deleted: `{$apiKey->toTokenValue()}`");
        return self::SUCCESS;
    }
}
