<?php

namespace EN\IgdbApiBundle\Igdb;


use EN\IgdbApiBundle\Exception\ScrollHeaderNotFoundException;
use EN\IgdbApiBundle\Igdb\Parameter\AbstractParameterCollection;
use EN\IgdbApiBundle\Igdb\Parameter\ParameterBuilder;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

interface IgdbWrapperInterface
{
    /**
     * Gets the parameter collection.
     *
     * @param string $className
     *
     * @return AbstractParameterCollection
     */
    public function getParameterCollection(string $className);

    /**
     * Call the IGDB API.
     *
     * @param string $endpoint
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function callApi(
      string $endpoint,
      ParameterBuilder $paramBuilder
    ): array;

    /**
     * Searches for a resource from the given endpoint.
     *
     * @param string $search
     * @param string $endpoint
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function search(
      string $search,
      string $endpoint,
      ParameterBuilder $paramBuilder
    ): array;

    /**
     * Calls the IGDB API with the scroll header from a response.
     *
     * @link https://igdb.github.io/api/references/pagination/#scroll-api
     *
     * @param ResponseInterface|null $response
     *
     * @return array
     * @throws ScrollHeaderNotFoundException
     * @throws GuzzleException
     */
    public function scroll(ResponseInterface $response = null): array;

    /**
     * Gets the scroll count from a response.
     *
     * @link https://igdb.github.io/api/references/pagination/#scroll-api
     *
     * @param \Psr\Http\Message\ResponseInterface|null $response
     *
     * @return int
     * @throws ScrollHeaderNotFoundException
     */
    public function getScrollCount(ResponseInterface $response = null): int;

    /**
     * Sends a HTTP Request.
     * @param string $url
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws GuzzleException
     */
    public function sendRequest(string $url): ResponseInterface;

    /**
     * Decodes the response's body to a PHP Assoc Array.
     *
     * @param ResponseInterface $response
     *
     * @return array
     */
    public function processResponse(ResponseInterface $response): array;

    /**
     * Combines the base URL with the endpoint name.
     *
     * @param string $endpoint
     *
     * @return string
     */
    public function getEndpoint(string $endpoint): string;

    /**
     * Gets the requested Scroll Header from the response (if it exists).
     *
     * @param ResponseInterface $response
     * @param string $header
     *
     * @return string
     * @throws ScrollHeaderNotFoundException
     */
    public function getScrollHeader(
      ResponseInterface $response,
      string $header
    ): string;

    public function achievements(ParameterBuilder $paramBuilder): array;

    /**
     * Call the characters endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function characters(ParameterBuilder $paramBuilder): array;

    /**
     * Call the collections endpoint.
     * @link https://igdb.github.io/api/endpoints/collection/
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function collections(ParameterBuilder $paramBuilder): array;

    /**
     * Call the companies endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function companies(ParameterBuilder $paramBuilder): array;

    public function credits(ParameterBuilder $paramBuilder): array;

    public function external_reviews(ParameterBuilder $paramBuilder): array;

    public function external_review_sources(ParameterBuilder $paramBuilder
    ): array;

    public function feeds(ParameterBuilder $paramBuilder): array;

    /**
     * Call the franchises endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function franchises(ParameterBuilder $paramBuilder): array;

    /**
     * Call the games endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function games(ParameterBuilder $paramBuilder): array;

    public function game_engines(ParameterBuilder $paramBuilder): array;

    /**
     * Call the game_modes endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function game_modes(ParameterBuilder $paramBuilder): array;

    /**
     * Call the genres endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function genres(ParameterBuilder $paramBuilder): array;

    /**
     * Call the keywords endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function keywords(ParameterBuilder $paramBuilder): array;

    public function pages(ParameterBuilder $paramBuilder): array;

    /**
     * Call the people endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function people(ParameterBuilder $paramBuilder): array;

    /**
     * Call the platforms endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function platforms(ParameterBuilder $paramBuilder): array;

    public function play_times(ParameterBuilder $paramBuilder): array;

    /**
     * Call the player_perspectives endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function player_perspectives(ParameterBuilder $paramBuilder): array;

    /**
     * Call the pulses endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function pulses(ParameterBuilder $paramBuilder): array;

    public function pulse_groups(ParameterBuilder $paramBuilder): array;

    public function pulse_sources(ParameterBuilder $paramBuilder): array;

    public function release_dates(ParameterBuilder $paramBuilder): array;

    public function reviews(ParameterBuilder $paramBuilder): array;

    /**
     * Call the themes endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function themes(ParameterBuilder $paramBuilder): array;

    public function titles(ParameterBuilder $paramBuilder): array;

    public function me(ParameterBuilder $paramBuilder): array;

    public function game_versions(ParameterBuilder $paramBuilder): array;
}