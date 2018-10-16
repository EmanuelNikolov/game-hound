<?php

namespace App\Service\Igdb\Utils;


abstract class AbstractParameterCollection
{

    protected $builder;

    public function __construct(ParameterBuilder $builder)
    {
        $this->builder = $builder;
    }
}