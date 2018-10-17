<?php

namespace EN\IgdbApiBundle\Igdb\Parameter;


abstract class AbstractParameterCollection
{

    protected $builder;

    public function __construct(ParameterBuilder $builder)
    {
        $this->builder = $builder;
    }
}