<?php

namespace EN\IgdbApiBundle\Igdb;

use EN\IgdbApiBundle\Exception\ScrollHeaderNotFoundException;
use EN\IgdbApiBundle\Igdb\Parameter\AbstractParameterCollection;
use EN\IgdbApiBundle\Igdb\Parameter\Factory\ParameterCollectionFactory;
use EN\IgdbApiBundle\Igdb\Parameter\ParameterBuilder;
use EN\IgdbApiBundle\Igdb\ValidEndpoints as Endpoint;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class IgdbWrapper implements IgdbWrapperInterface
{

    public const SCROLL_NEXT_PAGE = 'X-Next-Page';

    public const SCROLL_COUNT = 'X-Count';

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    /**
     * @var ParameterCollectionFactory
     */
    protected $parameterCollectionFactory;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Wrapper's constructor.
     *
     * @param string $key
     * @param string $baseUrl
     * @param ParameterCollectionFactory $parameterCollectionFactory
     * @param ClientInterface $client
     *
     * @throws \Exception
     */
    public function __construct(
      string $baseUrl,
      string $key,
      ClientInterface $client,
      ParameterCollectionFactory $parameterCollectionFactory
    ) {
        if (empty($key)) {
            throw new \Exception('IGDB API key is required, please visit https://api.igdb.com/ to request a key');
        }

        if (empty($baseUrl)) {
            throw new \Exception('IGDB Request URL is required, please visit https://api.igdb.com/ to get your Request URL');
        }

        $this->apiKey = $key;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->parameterCollectionFactory = $parameterCollectionFactory;
        $this->httpClient = $client;
    }

    /**
     * Gets the parameter collection.
     *
     * @param string $className
     *
     * @return AbstractParameterCollection
     */
    public function getParameterCollection(string $className
    ): AbstractParameterCollection {
        return $this->parameterCollectionFactory->createCollection($className);
    }

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
    ): array {
        $url = $this->getEndpoint($endpoint) . $paramBuilder->buildQueryString();

        $response = $this->sendRequest($url);

        return $this->processResponse($response);
    }

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
    ): array {
        $paramBuilder->setSearch($search);

        return $this->callApi($endpoint, $paramBuilder);
    }

    /**
     * Call the IGDB API with the scroll header from a response.
     *
     * @link https://igdb.github.io/api/references/pagination/#scroll-api
     *
     * @param ResponseInterface|null $response
     *
     * @return array
     * @throws ScrollHeaderNotFoundException
     * @throws GuzzleException
     */
    public function scroll(ResponseInterface $response = null): array
    {
        if (null === $response) {
            $response = $this->response;
        }

        $endpoint = $this->getScrollHeader($response, self::SCROLL_NEXT_PAGE);
        $url = $this->baseUrl . $endpoint;

        $scrollResponse = $this->sendRequest($url);

        return $this->processResponse($scrollResponse);
    }

    /**
     * Gets the scroll count from a response.
     *
     * @link https://igdb.github.io/api/references/pagination/#scroll-api
     *
     * @param \Psr\Http\Message\ResponseInterface|null $response
     *
     * @return int
     * @throws \EN\IgdbApiBundle\Exception\ScrollHeaderNotFoundException
     */
    public function getScrollCount(ResponseInterface $response = null): int
    {
        if (null === $response) {
            $response = $this->response;
        }

        return (int)$this->getScrollHeader($response, self::SCROLL_COUNT);
    }

    /**
     * Sends a HTTP Request.
     *
     * @param string $url
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws GuzzleException
     */
    public function sendRequest(string $url): ResponseInterface
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
              'headers' => [
                'user-key' => $this->apiKey,
                'Accept' => 'application/json',
              ],
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }

        $this->response = $response;

        return $response;
    }

    /**
     * Decodes the response's body to a PHP Assoc Array.
     *
     * @param ResponseInterface $response
     *
     * @return array
     */
    public function processResponse(ResponseInterface $response): array
    {
        $contents = $response->getBody()->getContents();
        $decodedJson = json_decode($contents, true);

        if (null === $decodedJson) {
            // When API returns a string, return type doesn't change (returns array with the string inside)
            $decodedJson = [$contents];
        }

        return $decodedJson;
    }

    /**
     * Combines the base URL with the endpoint name.
     *
     * @param string $endpoint
     *
     * @return string
     */
    public function getEndpoint(string $endpoint): string
    {
        return $this->baseUrl . '/' . $endpoint . '/';
    }

    /**
     * Gets the requested Scroll Header from the response (if it exists).
     *
     * @param ResponseInterface $response
     * @param string $header
     *
     * @return string
     * @throws \EN\IgdbApiBundle\Exception\ScrollHeaderNotFoundException
     */
    public function getScrollHeader(
      ResponseInterface $response,
      string $header
    ): string {
        $headerData = $response->getHeader($header);

        if (empty($headerData)) {
            throw new ScrollHeaderNotFoundException($header . " Header doesn't exist.");
        }

        return $headerData[0];
    }

    /**
     * @return null|ResponseInterface
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Call the achievements endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/achievement/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function achievements(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::ACHIEVEMENTS, $paramBuilder);
    }

    /**
     * Call the characters endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/character/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function characters(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::CHARACTERS, $paramBuilder);
    }

    /**
     * Call the collections endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/collection/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function collections(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::COLLECTIONS, $paramBuilder);
    }

    /**
     * Call the companies endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/company/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function companies(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::COMPANIES, $paramBuilder);
    }


    /**
     * Call the credits endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/credit/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function credits(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::CREDITS, $paramBuilder);
    }

    /**
     * Call the external_reviews endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/external-review/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function external_reviews(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::EXTERNAL_REVIEWS, $paramBuilder);
    }

    /**
     * Call the external_review_sources endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/external-review-source/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function external_review_sources(ParameterBuilder $paramBuilder
    ): array {
        return $this->callApi(Endpoint::EXTERNAL_REVIEW_SOURCES, $paramBuilder);
    }


    /**
     * Call the feeds endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/feed/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function feeds(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::FEEDS, $paramBuilder);
    }

    /**
     * Call the franchises endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/franchise/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function franchises(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::FRANCHISES, $paramBuilder);
    }

    /**
     * Call the games endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function games(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::GAMES, $paramBuilder);
    }

    /**
     * Call the game_modes endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-mode/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function gameModes(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::GAME_MODES, $paramBuilder);
    }

    /**
     * Call the genres endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function genres(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::GENRES, $paramBuilder);
    }

    /**
     * Call the keywords endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function keywords(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::KEYWORDS, $paramBuilder);
    }

    /**
     * Call the people endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function people(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PEOPLE, $paramBuilder);
    }

    /**
     * Call the platforms endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function platforms(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PLATFORMS, $paramBuilder);
    }

    /**
     * Call the player_perspectives endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function playerPerspectives(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PLAYER_PERSPECTIVES, $paramBuilder);
    }

    /**
     * Call the pulses endpoint.
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function pulses(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PULSES, $paramBuilder);
    }

    /**
     * Call the themes endpoint.
     *
     * @link
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws GuzzleException
     */
    public function themes(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::THEMES, $paramBuilder);
    }

    /**
     * Call the game_engines endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-engine/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function game_engines(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::GAME_ENGINES, $paramBuilder);
    }

    /**
     * Call the game_modes endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-mode/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function game_modes(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::GAME_MODES, $paramBuilder);
    }

    /**
     * Call the game_modes endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-mode/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pages(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PAGES, $paramBuilder);
    }

    /**
     * Call the game_modes endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-mode/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function play_times(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PLAY_TIMES, $paramBuilder);
    }

    /**
     * Call the game_modes endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-mode/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function player_perspectives(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PLAYER_PERSPECTIVES, $paramBuilder);
    }

    /**
     * Call the game_modes endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-mode/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pulse_groups(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PULSE_GROUPS, $paramBuilder);
    }

    /**
     * Call the game_modes endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-mode/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pulse_sources(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PULSE_SOURCES, $paramBuilder);
    }

    /**
     * Call the game_modes endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-mode/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function release_dates(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::RELEASE_DATES, $paramBuilder);
    }

    /**
     * Call the game_modes endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-mode/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function reviews(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::REVIEWS, $paramBuilder);
    }

    /**
     * Call the game_modes endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-mode/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function titles(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::TITLES, $paramBuilder);
    }

    /**
     * Call the game_modes endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-mode/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function me(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::ME, $paramBuilder);
    }

    /**
     * Call the game_modes endpoint.
     *
     * @link https://igdb.github.io/api/endpoints/game-mode/
     *
     * @param ParameterBuilder $paramBuilder
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function game_versions(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::GAME_VERSIONS, $paramBuilder);
    }
}