<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Product\Entities\SpecificDiscount;
use Modules\Product\Entities\Product;

class CreateSpecificDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specific_discounts', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->timestamp('done_at')->nullable();
            $table->foreignId('creator_id')->constrained('admins')->restrictOnDelete();
            $table->foreignId('updater_id')->constrained('admins')->restrictOnDelete();

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
        Schema::dropIfExists('specific_discounts');
    }
}
