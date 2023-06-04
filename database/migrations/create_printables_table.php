<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('filament-printables.table'), function (Blueprint $table) {
            $table->id('id');
            $table->string('slug')->unique();
            $table->string('name');
            $table->enum('type', ['report', 'form', 'label']);
            $table->text('template_view');
            $table->json('format')->nullable();
            $table->json('linked_resources')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('filament-printables.table'));
    }
};
