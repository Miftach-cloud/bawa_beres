<?php

namespace App\Exceptions;

use App\Enums\OrderStatus;
use Exception;

class InvalidOrderStateTransitionException extends Exception
{
    public function __construct(OrderStatus $from, OrderStatus $to)
    {
        parent::__construct("Perubahan status pesanan dari '{$from->label()}' ({$from->value}) ke '{$to->label()}' ({$to->value}) tidak diizinkan oleh sistem.");
    }
}
