<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('application_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('action');
            $table->enum('actor_type', ['user', 'admin', 'system']);
            $table->foreignId('actor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['application_id', 'created_at']);
            $table->index(['action']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('application_histories');
    }
};
