<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('collector:create-token')]
#[Description('Create a Sanctum API token for the price collector')]
class CreateCollectorToken extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::query()
            ->where('email', 'collector@tli-market-insights.local')
            ->firstOrFail();

        $token = $user->createToken(
            'price-collector',
            ['prices:create'],
        );

        $this->info('Collector API token created successfully.');
        $this->newLine();

        $this->line($token->plainTextToken);

        $this->newLine();
        $this->warn('Save this token now. It will not be displayed again.');

        return self::SUCCESS;
    }
}
