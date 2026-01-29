<?php
namespace reviews\application\queries;

class GetProductReviews
{
    public static function execute(array $data): array
    {
        if (empty($data['product_id'])) {
            throw new \InvalidArgumentException('Product ID is required');
        }

        $filters = [
            'product_id' => $data['product_id'],
        ];

        // Optional status filter
        if (!empty($data['status'])) {
            $filters['status'] = $data['status'];
        }

        // Optional pagination
        if (isset($data['page']) && $data['page'] > 0) {
            $filters['page'] = (int) $data['page'];
        }

        if (isset($data['per_page']) && $data['per_page'] > 0) {
            $filters['per_page'] = (int) $data['per_page'];
        }

        return $filters;
    }
}
