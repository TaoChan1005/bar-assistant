<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('bar_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        DB::table('bar_types')->insert([
            ['id' => 1, 'name' => 'Normal'],
            ['id' => 2, 'name' => 'Premium'],
        ]);

        Schema::create('bars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->string('description')->nullable();
            $table->string('search_driver_api_key')->nullable();
            $table->foreignId('bar_type_id')->default(1)->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('invite_code')->unique()->nullable();
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        // User can have only one role
        // Roles have permission levels in asc/desc order
        // Hopefully this design wont annoy me in the future :^)
        DB::table('user_roles')->insert([
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'Moderator'],
            ['id' => 3, 'name' => 'General'],
            ['id' => 4, 'name' => 'Guest'],
        ]);

        Schema::create('bar_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_role_id')->constrained()->onDelete('restrict');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['bar_id', 'user_id', 'user_role_id']);
        });

        Schema::create('glasses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('cocktail_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->tinyInteger('dilution_percentage');
        });

        Schema::create('ingredient_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('name');
            $table->decimal('strength')->default(0.0);
            $table->string('description')->nullable();
            $table->text('origin')->nullable();
            $table->string('color')->nullable();
            $table->foreignId('ingredient_category_id')->constrained();
            $table->foreignId('parent_ingredient_id')->nullable()->constrained('ingredients')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('cocktails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('instructions');
            $table->text('description')->nullable();
            $table->string('source')->nullable();
            $table->text('garnish')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('glass_id')->nullable()->constrained();
            $table->foreignId('cocktail_method_id')->nullable()->constrained('cocktail_methods');
            $table->ulid('public_id')->nullable();
            $table->dateTime('public_at')->nullable();
            $table->dateTime('public_expires_at')->nullable();
            $table->decimal('abv')->nullable();
            $table->index('abv', 'cocktails_abv_index');
            $table->timestamps();
        });

        Schema::create('cocktail_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->foreignId('cocktail_id')->constrained()->onDelete('cascade');
            $table->decimal('amount');
            $table->string('units');
            $table->integer('sort')->default(0);
            $table->boolean('optional')->default(false);
        });

        Schema::create('cocktail_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_membership_id')->constrained()->onDelete('cascade');
            $table->foreignId('cocktail_id')->unique()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['bar_membership_id', 'cocktail_id']);
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained()->onDelete('cascade');
            $table->string('name');
        });

        Schema::create('cocktail_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->foreignId('cocktail_id')->constrained()->onDelete('cascade');
        });

        Schema::create('user_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_membership_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->unique(['bar_membership_id', 'ingredient_id']);
        });

        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('imageable');
            $table->string('file_path');
            $table->string('file_extension');
            $table->string('copyright')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->integer('sort')->default(1);
            $table->string('placeholder_hash')->nullable();
            $table->timestamps();
        });

        Schema::create('user_shopping_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_membership_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['bar_membership_id', 'ingredient_id']);
        });

        Schema::create('cocktail_ingredient_substitutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cocktail_ingredient_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->morphs('rateable');
            $table->foreignId('bar_membership_id')->constrained()->onDelete('cascade');
            $table->smallInteger('rating');
            $table->timestamps();
            $table->unique(['bar_membership_id', 'rateable_id', 'rateable_type']);
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->morphs('noteable');
            $table->foreignId('bar_membership_id')->constrained()->onDelete('cascade');
            $table->string('note');
            $table->timestamps();
        });

        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignId('bar_membership_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('collections_cocktails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cocktail_id')->constrained()->onDelete('cascade');
            $table->foreignId('collection_id')->constrained()->onDelete('cascade');

            $table->unique(['cocktail_id', 'collection_id']);
        });

        Schema::create('utensils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('cocktail_utensil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cocktail_id')->constrained()->onDelete('cascade');
            $table->foreignId('utensil_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
