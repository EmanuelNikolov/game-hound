<?php

namespace EN\IgdbApiBundle\Igdb\Parameter;


class ParameterBuilder
{

    /**
     * @var array
     */
    private $expand;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var array
     */
    private $ids;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

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
     * Sets the expand parameter.
     * @link https://igdb.github.io/api/references/expander/
     * @param string $expand
     * @return ParameterBuilder
     */
    public function setExpand(string $expand): ParameterBuilder
    {
        $this->expand[] = $expand;
        return $this;
    }

    /**
     * Sets the fields parameter.
     *
     * @link https://igdb.github.io/api/references/fields/
     *
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
     * Sets the filters parameter.
     *
     * @link https://igdb.github.io/api/references/filters
     *
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
     * Sets one Id parameter.
     * If you want to add more at once check setIds().
     *
     * @param int $id
     *
     * @return ParameterBuilder
     */
    public function setId(int $id): ParameterBuilder
    {
        $this->ids[] = $id;
        return $this;
    }

    /**
     * Sets multiple comma(,) separated Id parameters.
     *
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
     * Sets the limit parameter.
     *
     * @link https://igdb.github.io/api/references/pagination/#simple-pagination
     *
     * @param int $limit
     *
     * @return ParameterBuilder
     */
    public function setLimit(int $limit): ParameterBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Sets the offset parameter.
     *
     * @link https://igdb.github.io/api/references/pagination/#simple-pagination
     *
     * @param int $offset
     *
     * @return ParameterBuilder
     */
    public function setOffset(int $offset): ParameterBuilder
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Sets the order parameter.
     *
     * @link https://igdb.github.io/api/references/ordering/
     *
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
     * Sets the search parameter.
     *
     * @link https://igdb.github.io/api/examples/#search-return-certain-fields
     *
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
     * Sets the scroll parameter
     *
     * @link https://igdb.github.io/api/references/pagination/#scroll-api
     *
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
     * Builds the query string from the parameters.
     *
     * @return string
     */
    public function buildQueryString(): string
    {
        $propsArr = get_object_vars($this);

        foreach ($propsArr as $key => $prop) {
            // faster than is_array smh
            if ((array)$prop === $prop) {
                $propsArr[$key] = implode(',', $prop);
            }
        }

        $ids = $propsArr['ids'];
        unset($propsArr['ids']);
        empty($propsArr['fields']) ? $propsArr['fields'] = '*' : null;

        // using urldecode because http_build_query encodes commas :|
        return $ids . '?' . urldecode(http_build_query($propsArr));
    }
}