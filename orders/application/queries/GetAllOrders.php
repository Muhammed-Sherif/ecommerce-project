<?php
namespace orders\application\queries;

class GetAllOrders
{
    public static function execute(array $data): array
    {
        $filters = [];

        // Optional customer_id filter
        if (!empty($data['customer_id'])) {
            $filters['customer_id'] = $data['customer_id'];
        }

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
