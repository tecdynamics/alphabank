<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('ec_orders','installments' )) //check the column
        {
            Schema::table('ec_orders', function (Blueprint $table) {
                $table->addColumn('integer', 'installments')->nullable()->default(0);


              });
            }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('ec_orders', 'installments')) //check the column
        {
            Schema::table('ec_orders', function (Blueprint $table) {
                $table->dropColumn('installments'); //drop it
            });
        }
    }
};
