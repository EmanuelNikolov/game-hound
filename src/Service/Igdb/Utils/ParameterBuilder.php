<?php

namespace App\Service\Igdb\Utils;


class ParameterBuilder
{

    /**
     * @var string
     */
    private $filters;

    /**
     * @var string
     */
    private $offset;

    /**
     * @var string
     */
    private $ids;

    /**
     * @var string
     */
    private $fields;

    /**
     * @var string
     */
    private $expand;

    /**
     * @var string
     */
    private $limit;

    /**
     * @var string
     */
    private $order;

    /**
     * @var string
     */
    private $search;

    /**
     * @var string
     */
    private $scroll;

    /**
     * @var string
     */
    private $query;

    /**
     * @return string
     */
    public function getFilters(): string
    {
        return $this->filters;
    }

    /**
     * @param string $filters
     *
     * @return ParameterBuilder
     */
    public function setFilters(string $filters): ParameterBuilder
    {
        $this->filters[] = $filters;
        return $this;
    }

    /**
     * @return string
     */
    public function getOffset(): string
    {
        return $this->offset;
    }

    /**
     * @param string $offset
     *
     * @return ParameterBuilder
     */
    public function setOffset(string $offset): ParameterBuilder
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return string
     */
    public function getIds(): string
    {
        return $this->ids;
    }

    /**
     * @param string $ids
     *
     * @return ParameterBuilder
     */
    public function setIds(string $ids): ParameterBuilder
    {
        $this->ids[] = $ids;
        return $this;
    }

    /**
     * @return string
     */
    public function getFields(): string
    {
        return $this->fields;
    }

    /**
     * @param string $fields
     *
     * @return ParameterBuilder
     */
    public function setFields(string $fields): ParameterBuilder
    {
        $this->fields[] = $fields;
        return $this;
    }

    /**
     * @return string
     */
    public function getExpand(): string
    {
        return $this->expand;
    }

    /**
     * @param string $expand
     *
     * @return ParameterBuilder
     */
    public function setExpand(string $expand): ParameterBuilder
    {
        $this->expand[] = $expand;
        return $this;
    }

    /**
     * @return string
     */
    public function getLimit(): string
    {
        return $this->limit;
    }

    /**
     * @param string $limit
     *
     * @return ParameterBuilder
     */
    public function setLimit(string $limit): ParameterBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @param string $order
     *
     * @return ParameterBuilder
     */
    public function setOrder(string $order): ParameterBuilder
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string
     */
    public function getSearch(): string
    {
        return $this->search;
    }

    /**
     * @param string $search
     *
     * @return ParameterBuilder
     */
    public function setSearch(string $search): ParameterBuilder
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @return string
     */
    public function getScroll(): string
    {
        return $this->scroll;
    }

    /**
     * @param string $scroll
     *
     * @return ParameterBuilder
     */
    public function setScroll(string $scroll): ParameterBuilder
    {
        $this->scroll = $scroll;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    public function buildQueryString(): string
    {
        $propsArr = get_object_vars($this);

        foreach ($propsArr as $key => $prop) {
            // faster than is_array smh
            if ((array)$prop === $prop) {
                $propsArr[$key] = implode(",", $prop);
            }
        }

        $ids = $propsArr['ids'];
        unset($propsArr['ids']);
        empty($propsArr['fields']) ? $propsArr['fields'] = '*' : null;

        // using urldecode because http_build_query encodes commas :|
        return $ids . '?' . urldecode(http_build_query($propsArr));
    }
}