<?php

declare(strict_types=1);

namespace App\Model\Response\Visit;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'VisitStatsResponse', required: [
    'total_visits',
    'unique_visitors',
    'visits_by_page',
    'visits_by_day',
    'top_referrers',
])]
final readonly class VisitStatsResponse
{
    /**
     * @param array<string, int>                              $visits_by_page
     * @param array<int, array{date: string, count: int}>     $visits_by_day
     * @param array<int, array{referrer: string, count: int}> $top_referrers
     */
    public function __construct(
        #[OA\Property(example: 1234)]
        public int $total_visits,
        #[OA\Property(example: 567)]
        public int $unique_visitors,
        #[OA\Property(
            type: 'object',
            example: [
                '/' => 500,
                '/projects' => 300,
            ],
            additionalProperties: new OA\AdditionalProperties(type: 'integer'),
        )]
        public array $visits_by_page,
        #[OA\Property(
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'date', type: 'string', example: '2024-01-01'),
                    new OA\Property(property: 'count', type: 'integer', example: 42),
                ],
            ),
        )]
        public array $visits_by_day,
        #[OA\Property(
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'referrer', type: 'string', example: 'https://google.com'),
                    new OA\Property(property: 'count', type: 'integer', example: 100),
                ],
            ),
        )]
        public array $top_referrers,
    ) {
    }
}
