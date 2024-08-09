<?php

declare(strict_types=1);

namespace Kami\Cocktail\OpenAPI;

use OpenApi\Attributes as OAT;
use Kami\Cocktail\OpenAPI\Schemas\APIError;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotAuthorizedResponse extends OAT\Response
{
    public function __construct()
    {
        parent::__construct(response: 403, description: 'You are not authorized for this action.', content: [
            new OAT\JsonContent(properties: [new OAT\Property(property: 'data', type: 'object', ref: APIError::class)]),
        ]);
    }
}
