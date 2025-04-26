<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Color\Entities\ColorRange;
use Modules\Product\Entities\Variety;

class CreateColorRangeVarietyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('color_range_variety', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ColorRange::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Variety::class)->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('color_range_variety');
    }
}
