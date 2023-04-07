<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlowQueriesTable extends Migration
{
    public function up()
    {
        Schema::create('slow_queries', function (Blueprint $table) {
            $table->id();

            $table->string('uri')->nullable();
            $table->string('action')->nullable();
            $table->string('route')->nullable();
            $table->string('source_file')->nullable();
            $table->string('line')->nullable();
            $table->string('query_hashed', 32)->nullable();
            $table->text('query_with_bindings')->nullable();
            $table->text('query_without_bindings')->nullable();
            $table->float('duration')->nullable();
            $table->string('request_guid')->nullable();

            $table->timestamps();
        });
    }
};
