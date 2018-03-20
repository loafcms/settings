<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');

            $table->string('path', 255);
            $table->string('scope', 255)->default( \Loaf\Settings\SettingsManager::getDefaultScope() );

            $table->timestamps();

            // Store data in an efficient binary format if it's available
            $table->jsonb('value')->nullable();
            $table->string('type')->nullable();

            // Composite primary key
            $table->unique(['path', 'scope']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
