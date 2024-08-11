<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description');
        $table->binary('image');
        $table->decimal('price', 8, 2);
        $table->integer('quantity');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('products');
}

};
