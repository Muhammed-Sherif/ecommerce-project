<?php
namespace orders\domains\models;

class Address
{
    public $street;
    public $city;
    public $state;
    public $country;
    public $zipCode;

    public function __construct(
        string $street,
        string $city,
        string $state,
        string $country,
        string $zipCode
    ) {
        $this->validate($street, $city, $state, $country, $zipCode);
        
        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->zipCode = $zipCode;
    }

    private function validate(
        string $street,
        string $city,
        string $state,
        string $country,
        string $zipCode
    ): void {
        if (empty($street)) {
            throw new \InvalidArgumentException('Street is required');
        }
        if (empty($city)) {
            throw new \InvalidArgumentException('City is required');
        }
        if (empty($state)) {
            throw new \InvalidArgumentException('State is required');
        }
        if (empty($country)) {
            throw new \InvalidArgumentException('Country is required');
        }
        if (empty($zipCode)) {
            throw new \InvalidArgumentException('Zip code is required');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['street'] ?? '',
            $data['city'] ?? '',
            $data['state'] ?? '',
            $data['country'] ?? '',
            $data['zip_code'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'zip_code' => $this->zipCode,
        ];
    }
}
