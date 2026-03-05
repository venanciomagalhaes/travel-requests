<?php

use App\Enums\V1\TravelRequest\TravelRequestStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('travelers_name')->index();
            $table->string('destination')->index();
            $table->date('departure_date')->index();
            $table->date('return_date')->index();
            $table->enum('status', [
                TravelRequestStatusEnum::REQUESTED->value,
                TravelRequestStatusEnum::APPROVED->value,
                TravelRequestStatusEnum::CANCELED->value,
            ])->default(TravelRequestStatusEnum::REQUESTED->value)->index();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_requests');
    }
};
