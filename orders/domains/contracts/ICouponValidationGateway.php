<?php
namespace orders\domains\contracts;

interface ICouponValidationGateway
{
    public function validateByCode($code);
}
