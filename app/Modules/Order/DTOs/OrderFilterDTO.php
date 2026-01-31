<?php

namespace App\Modules\Order\DTOs;

use App\Base\BaseDTO;

class OrderFilterDTO extends BaseDTO
{
    public function __construct(
        public ?string $status = null,
        public ?string $from_date = null,
        public ?string $to_date = null,
        public int $per_page = 15,
        public int $page = 1
    ) {}

    /**
     * Create DTO from request
     */
    public static function fromRequest(array $request): static
    {
        return new static(
            status: $request['status'] ?? null,
            from_date: $request['from_date'] ?? null,
            to_date: $request['to_date'] ?? null,
            per_page: $request['per_page'] ?? 15,
            page: $request['page'] ?? 1,
        );
    }

    public function hasStatus(): bool
    {
        return $this->status !== null;
    }

    public function hasDateRange(): bool
    {
        return $this->from_date !== null || $this->to_date !== null;
    }
}
