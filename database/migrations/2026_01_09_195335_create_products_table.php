<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('title', 2000);
            $table->string('slug', 2000);
            $table->text('description')->nullable();

            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('category_id')->constrained('categories');

            $table->decimal('price', 20, 4);
            $table->string('status')->index();
            $table->integer('quantity')->default(0);

            // ✅ FIXED
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
