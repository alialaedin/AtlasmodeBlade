<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Product\Entities\Product;

class CreateSpecificDiscountTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specific_discount_types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('specific_discount_id')->constrained('specific_discounts')->cascadeOnDelete();
            $table->enum('discount_type', [Product::DISCOUNT_TYPE_PERCENTAGE, Product::DISCOUNT_TYPE_FLAT])->nullable();
            $table->unsignedInteger('discount')->nullable();

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
        Schema::dropIfExists('specific_discount_type');
    }
}
