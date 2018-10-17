<?php

namespace EN\IgdbApiBundle\Igdb\Parameter\Factory;


use EN\IgdbApiBundle\Igdb\Parameter\AbstractParameterCollection;
use EN\IgdbApiBundle\Igdb\Parameter\ParameterBuilder;

class ParameterCollectionFactory
{

    /**
     * @var ParameterBuilder
     */
    protected $builder;

    /**
     * ParameterCollectionFactory constructor.
     *
     * @param ParameterBuilder $builder
     */
    public function __construct(ParameterBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Creates an instance of the specified collection.
     *
     * @param string $className
     *
     * @return AbstractParameterCollection
     */
    public function createCollection(string $className): AbstractParameterCollection
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Could not load group {$className}: class doesn't exist.");
        }

        if (!is_subclass_of($className, AbstractParameterCollection::class)) {
            throw new \InvalidArgumentException("Could not load group {$className}: class doesn't extend App\Service\Igdb\Utils\AbstractParameterGroup.");
        }

        return new $className($this->builder);
    }
}