<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $industry = null;
        if (session()->has('industry')) {
            $industry = Session::get('industry');
        } else {
            $industry = DB::table('settings')->where('slug', 'industry')->value('config_value') ?? 'ecommerce';
        }
        if ($industry === 'education') {
            Schema::create('course_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
                $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['course_id', 'tag_id']);
            });
        } else {
            Schema::create('product_tags', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['product_id', 'tag_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $industry = null;
        if (session()->has('industry')) {
            $industry = Session::get('industry');
        } else {
            $industry = DB::table('settings')->where('slug', 'industry')->value('config_value') ?? 'ecommerce';
        }

        if ($industry === 'education') {
            Schema::dropIfExists('course_tag');
        } else {
            Schema::dropIfExists('product_tags');
        }
    }
};
