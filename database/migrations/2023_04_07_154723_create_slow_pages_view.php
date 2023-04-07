<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSlowPagesView extends Migration
{
    public function up()
    {
        DB::statement($this->createView());
    }

    public function down()
    {
        DB::statement($this->dropView());
    }

    private function createView(): string
    {
        $query = /** @lang sql */
            <<<SQL
            CREATE VIEW view_slow_pages AS
                select 
                  request_guid, 
                  sum(duration) as the_page_duration, 
                  count(*) as the_query_count, 
                  min(uri) as the_uri ,
                  min(created_at) as created_at,
                  min(route) as the_route

                from 
                  slow_queries 
                group by 
                  request_guid ;
            
SQL;

        return $query;
    }

    private function dropView(): string
    {
        $query = /** @lang sql */
            <<<SQL
            DROP VIEW if EXISTS `view_slow_pages`;            
SQL;

        return $query;
    }
}