
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')
                ->constrained('items')
                ->restrictOnDelete();

            $table->foreignId('league_id')
                ->constrained('leagues')
                ->restrictOnDelete();

            $table->decimal('value', 16);
            $table->timestamp('observed_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['item_id', 'league_id', 'observed_at']);
            $table->index(['league_id', 'observed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
