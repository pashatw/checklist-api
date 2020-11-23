<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('attribute_id')
                ->foreign('attribute_id')
                ->references('id')->on('attributes')
                ->onDelete('cascade');
            $table->string('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->string('due')->nullable();
            $table->tinyInteger('urgency')->default(0);
            $table->integer('updated_by')->nullable();
            $table->string('asignee_id')->nullable();
            $table->integer('task_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_attributes');
    }
}
