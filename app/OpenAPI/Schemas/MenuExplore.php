<?php

declare(strict_types=1);

namespace Kami\Cocktail\OpenAPI\Schemas;

use OpenApi\Attributes as OAT;

#[OAT\Schema()]
class MenuExplore
{
    #[OAT\Property(type: 'object', properties: [
        new OAT\Property(type: 'string', property: 'name', example: 'Bar name'),
        new OAT\Property(type: 'string', property: 'subtitle', example: 'Bar subtitle'),
        new OAT\Property(type: 'string', property: 'description', example: 'Bar description'),
    ])]
    public array $bar;
    #[OAT\Property(type: 'array', items: new OAT\Items(type: 'object', properties: [
        new OAT\Property(type: 'string', property: 'name', example: 'Category name'),
        new OAT\Property(type: 'array', property: 'cocktails', items: new OAT\Items(type: 'object', properties: [
            new OAT\Property(type: 'string', property: 'sort', example: 1),
            new OAT\Property(type: 'object', property: 'price', properties: [
                new OAT\Property(type: 'integer', property: 'full', example: 2385),
                new OAT\Property(type: 'string', property: 'formatted', example: 'EUR 23.85'),
            ]),
            new OAT\Property(type: 'string', property: 'public_id', example: '01ARZ3NDEKTSV4RRFFQ69G5FAV'),
            new OAT\Property(type: 'string', property: 'slug', example: 'cocktail-name-1'),
            new OAT\Property(type: 'string', property: 'currency', example: 'EUR'),
            new OAT\Property(type: 'string', property: 'name', example: 'Cocktail name'),
            new OAT\Property(type: 'array', property: 'short_ingredients', items: new OAT\Items(type: 'string', example: 'Vodka')),
            new OAT\Property(type: 'string', property: 'image', example: 'https://example.com/image.jpg'),
        ])),
    ]))]
    public array $categories;
}
