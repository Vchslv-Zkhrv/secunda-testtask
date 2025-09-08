<?php

namespace App\Console\Commands\ApiKey;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class ProlongApiKeyCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = '
        app:api-key:prolong
            { id : ApiKey id or Authorization token contents }
            { --valid_till= : new token expiration date. Leave empty to make token dateless }
    ';

    /**
     * @var string
     */
    protected $description = 'Sets new valid_till for ApiKey';

    /**
     * Execute the console command.
     */
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

        $validTill = $this->option('valid_till');
        if ($validTill) {
            $apiKey->valid_till = new \DateTimeImmutable($validTill);
        } else {
            $apiKey->valid_till = null;
        }

        $apiKey->save();

        $this->output->text("ApiKey created: `{$apiKey->toTokenValue()}`");
        return self::SUCCESS;
    }
}
