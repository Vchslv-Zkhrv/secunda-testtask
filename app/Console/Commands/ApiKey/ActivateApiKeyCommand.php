<?php

namespace App\Console\Commands\ApiKey;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class ActivateApiKeyCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:api-key:activate { id : ApiKey id or Authorization token contents }';

    /**
     * @var string
     */
    protected $description = 'Restores soft-deleted ApiKey';

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

        $apiKey->deleted_at = null;
        $apiKey->save();

        $this->output->text("ApiKey now active: {$apiKey->toTokenValue()}");
        return self::SUCCESS;
    }
}
