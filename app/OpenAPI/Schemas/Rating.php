<?php

declare(strict_types=1);

namespace Kami\Cocktail\OpenAPI\Schemas;

use OpenApi\Attributes as OAT;

#[OAT\Schema()]
class Rating
{
    #[OAT\Property(example: 1)]
    public int $rateableId;
    #[OAT\Property(example: 1)]
    public int $userId;
    #[OAT\Property(example: 3)]
    public int $rating;
}
