<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'customer_id')) {
                $table->foreignId('customer_id')
                    ->constrained()
                    ->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('messages', 'status')) {
                $table->string('status')->default('pending');
            }
            
            if (!Schema::hasColumn('messages', 'variables')) {
                $table->json('variables')->nullable();
            }
            
            if (!Schema::hasColumn('messages', 'response')) {
                $table->json('response')->nullable();
            }
            
            if (!Schema::hasColumn('messages', 'sent_at')) {
                $table->timestamp('sent_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['customer_id', 'status', 'variables', 'response', 'sent_at']);
        });
    }
};
