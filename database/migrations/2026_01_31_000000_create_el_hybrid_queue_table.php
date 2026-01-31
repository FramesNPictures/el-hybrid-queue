<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('el_hybrid_queue_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job');
            $table->text('payload')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('el_hybrid_queue_jobs');
    }
};
