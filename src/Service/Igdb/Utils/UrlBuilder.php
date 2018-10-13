<?php

namespace App\Service\Igdb\Utils;


use App\Service\Igdb\IGDBWrapper;

class UrlBuilder
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
     * @return UrlBuilder
     */
    public function setFilters(string $filters): UrlBuilder
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
     * @return UrlBuilder
     */
    public function setOffset(string $offset): UrlBuilder
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
     * @return UrlBuilder
     */
    public function setIds(string $ids): UrlBuilder
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
     * @return UrlBuilder
     */
    public function setFields(string $fields): UrlBuilder
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
     * @return UrlBuilder
     */
    public function setExpand(string $expand): UrlBuilder
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
     * @return UrlBuilder
     */
    public function setLimit(string $limit): UrlBuilder
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
     * @return UrlBuilder
     */
    public function setOrder(string $order): UrlBuilder
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
     * @return UrlBuilder
     */
    public function setSearch(string $search): UrlBuilder
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
     * @return UrlBuilder
     */
    public function setScroll(string $scroll): UrlBuilder
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

    public function buildUrl(string $baseUrl, string $endpoint)
    {
        $endpointComplete = rtrim($baseUrl, '/')
          . '/'
          . IGDBWrapper::VALID_RESOURCES['Games']
          . '/';

        $propsArr = get_object_vars($this);

        foreach ($propsArr as $key => $props) {
            // faster than is_array smh
            if ((array)$props === $props) {
                $propsArr[$key] = implode(",", $props);
            }
        }

        return $endpointComplete . '?' . urldecode(http_build_query($propsArr));
    }
}