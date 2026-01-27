<?php

namespace App\Modules\Auth\DTOs;

use App\Base\BaseDTO;
use Illuminate\Http\Request;

class RegisterDTO extends BaseDTO
{
    /**
     * Constructor.
     *
     * @param  string  $name  Set name property.
     * @param  string  $email  Set email property.
     * @param  string  $password  Set password property.
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}

    /**
     * Create DTO from request
     */
    public static function fromRequest(array $request): static
    {
        return new static(
            name: $request['name'],
            email: $request['email'],
            password: $request['password'],
        );
    }
}
