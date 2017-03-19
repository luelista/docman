<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('doc_date')->nullable();
            $table->string('tags');
            $table->string('doc_name');
            $table->string('title');
            $table->string('doc_mandant');
            $table->text('description');
            $table->integer('page_count');
            $table->integer('file_size');
            $table->string('import_filename');
            $table->string('import_source');
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
        Schema::drop('documents');
    }
}
