<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VhFormContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('vh_form_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable()->index();

             $table->string('name')->nullable()->index();
            $table->string('slug')->nullable()->index();
            $table->boolean('is_published')->nullable()->index();
            $table->boolean('is_use_default_url')->nullable()->index();
            $table->string('action_url')->nullable();
            $table->string('method_type')->nullable()->index();

            $table->text('meta')->nullable();
            $table->text('mail_fields')->nullable();
            $table->text('message_fields')->nullable();

            $table->integer('created_by')->nullable()->index();
            $table->integer('updated_by')->nullable()->index();
            $table->integer('deleted_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['created_at', 'updated_at', 'deleted_at']);

        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('vh_form_contact_forms');
    }
}
