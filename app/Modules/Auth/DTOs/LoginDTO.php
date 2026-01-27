<?php

namespace App\Modules\Auth\DTOs;

use App\Base\BaseDTO;

class LoginDTO extends BaseDTO
{
    /**
     * Constructor.
     *
     * @param  string  $email  Set email property.
     * @param  string  $password  Set password property.
     */
    public function __construct(
        public string $email,
        public string $password
    ) {}

    /**
     * Create DTO from request
     */
    public static function fromRequest(array $request): static
    {
        return new static(
            email: $request['email'],
            password: $request['password'],
        );
    }

    public function credentials(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
