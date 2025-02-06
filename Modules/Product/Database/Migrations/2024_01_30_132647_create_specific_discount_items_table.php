<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Product\Entities\SpecificDiscountItem;

class CreateSpecificDiscountItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specific_discount_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('specific_discount_type_id')->constrained('specific_discount_types')->cascadeOnDelete();
            $table->enum('type', [SpecificDiscountItem::TYPE_CATEGORY,SpecificDiscountItem::TYPE_PRODUCT,SpecificDiscountItem::TYPE_BALANCE,SpecificDiscountItem::TYPE_RANGE]);

            $table->text('model_ids')->nullable();
            $table->string('balance')->nullable();
            $table->enum('balance_type', [SpecificDiscountItem::BALANCE_TYPE_LESS, SpecificDiscountItem::BALANCE_TYPE_MORE])->nullable();
            $table->unsignedInteger('range_from')->nullable();
            $table->unsignedInteger('range_to')->nullable();

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
        Schema::dropIfExists('specific_discount_items');
    }
}
