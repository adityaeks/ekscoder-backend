<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Refactor projects table: change string PK to auto-increment id + slug column.
     */
    public function up(): void
    {
        // 1. Create new table with proper structure
        Schema::create('projects_new', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('number');
            $table->string('title');
            $table->string('category');
            $table->string('year');
            $table->text('description');
            $table->json('technologies');
            $table->string('image_bg')->default('from-emerald-900/40 via-neutral-900 to-black');
            $table->string('accent_color')->default('#B8FF00');
            $table->string('link')->nullable();
            $table->boolean('featured')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 2. Migrate existing data: old string `id` becomes `slug`
        $projects = DB::table('projects')->get();
        foreach ($projects as $project) {
            DB::table('projects_new')->insert([
                'slug'         => $project->id,
                'number'       => $project->number,
                'title'        => $project->title,
                'category'     => $project->category,
                'year'         => $project->year,
                'description'  => $project->description,
                'technologies' => $project->technologies,
                'image_bg'     => $project->image_bg,
                'accent_color' => $project->accent_color,
                'link'         => $project->link,
                'featured'     => $project->featured,
                'is_active'    => $project->is_active,
                'order'        => $project->order,
                'created_at'   => $project->created_at,
                'updated_at'   => $project->updated_at,
            ]);
        }

        // 3. Swap tables
        Schema::drop('projects');
        Schema::rename('projects_new', 'projects');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the original string-PK table
        Schema::create('projects_old', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('number');
            $table->string('title');
            $table->string('category');
            $table->string('year');
            $table->text('description');
            $table->json('technologies');
            $table->string('image_bg')->default('from-emerald-900/40 via-neutral-900 to-black');
            $table->string('accent_color')->default('#B8FF00');
            $table->string('link')->nullable();
            $table->boolean('featured')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Move data back: slug becomes id
        $projects = DB::table('projects')->get();
        foreach ($projects as $project) {
            DB::table('projects_old')->insert([
                'id'           => $project->slug,
                'number'       => $project->number,
                'title'        => $project->title,
                'category'     => $project->category,
                'year'         => $project->year,
                'description'  => $project->description,
                'technologies' => $project->technologies,
                'image_bg'     => $project->image_bg,
                'accent_color' => $project->accent_color,
                'link'         => $project->link,
                'featured'     => $project->featured,
                'is_active'    => $project->is_active,
                'order'        => $project->order,
                'created_at'   => $project->created_at,
                'updated_at'   => $project->updated_at,
            ]);
        }

        Schema::drop('projects');
        Schema::rename('projects_old', 'projects');
    }
};
