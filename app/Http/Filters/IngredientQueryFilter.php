<?php

declare(strict_types=1);

namespace Kami\Cocktail\Http\Filters;

use Kami\Cocktail\Models\Ingredient;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Kami\Cocktail\Services\IngredientService;

final class IngredientQueryFilter extends QueryBuilder
{
    public function __construct(IngredientService $ingredientService)
    {
        parent::__construct(Ingredient::query());

        $this
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::beginsWithStrict('name_exact', 'name'),
                AllowedFilter::exact('category_id', 'ingredient_category_id'),
                AllowedFilter::partial('origin'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::callback('on_shopping_list', function ($query) {
                    $usersList = $this->request->user()->shoppingLists->pluck('ingredient_id');
                    $query->whereIn('id', $usersList);
                }),
                AllowedFilter::callback('on_shelf', function ($query, $value) {
                    if ($value === true) {
                        $query->join('user_ingredients', 'user_ingredients.ingredient_id', '=', 'ingredients.id')->where('user_ingredients.user_id', $this->request->user()->id);
                    }
                }),
                AllowedFilter::callback('strength_min', function ($query, $value) {
                    $query->where('strength', '>=', $value);
                }),
                AllowedFilter::callback('strength_max', function ($query, $value) {
                    $query->where('strength', '<=', $value);
                }),
                AllowedFilter::callback('main_ingredients', function ($query, $value) use ($ingredientService) {
                    if ($value === true) {
                        $query->whereIn('ingredients.id', $ingredientService->getMainIngredientsInCocktails()->pluck('ingredient_id'));
                    }
                }),
            ])
            ->defaultSort('name')
            ->allowedSorts('name', 'created_at', 'strength')
            ->with('category', 'images', 'parentIngredient')
            ->withCount('cocktails');
    }
}
