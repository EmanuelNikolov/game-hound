<?php

namespace App\Service\Igdb;

use App\Service\Igdb\Exception\ScrollHeaderNotFoundException;
use App\Service\Igdb\Utils\ParameterBuilder;
use App\Service\Igdb\ValidEndpoints as Endpoint;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class IgdbWrapper implements IgdbWrapperInterface
{

    const SCROLL_NEXT_PAGE = 'X-Next-Page';

    const SCROLL_COUNT = 'X-Count';

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var ParameterBuilder
     */
    protected $paramBuilder;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Wrapper's constructor.
     *
     * @param string $key
     * @param string $baseUrl
     * @param ClientInterface $client
     *
     * @throws \Exception
     */
    public function __construct(
      string $key,
      string $baseUrl,
      ClientInterface $client
    ) {
        if (empty($key)) {
            throw new \Exception('IGDB API key is required, please visit https://api.igdb.com/ to request a key');
        }

        if (empty($baseUrl)) {
            throw new \Exception('IGDB Request URL is required, please visit https://api.igdb.com/ to get your Request URL');
        }

        $this->apiKey = $key;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->httpClient = $client;
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
     * @throws \App\Service\Igdb\Exception\ScrollHeaderNotFoundException
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
     * @throws \App\Service\Igdb\Exception\ScrollHeaderNotFoundException
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
     * Call the characters endpoint.
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
     * Call the companies endpoint.
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
     * Call the franchises endpoint.
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
     * Call the game_modes endpoint.
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
     * Call the games endpoint.
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
     * Call the collections endpoint.
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
     * Call the themes endpoint.
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

    public function achievements(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement achievements() method.
    }

    public function credits(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement credits() method.
    }

    public function external_reviews(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement external_reviews() method.
    }

    public function external_review_sources(ParameterBuilder $paramBuilder
    ): array {
        // TODO: Implement external_review_sources() method.
    }

    public function feeds(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement feeds() method.
    }

    public function game_engines(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement game_engines() method.
    }

    public function game_modes(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement game_modes() method.
    }

    public function pages(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement pages() method.
    }

    public function play_times(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement play_times() method.
    }

    public function player_perspectives(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement player_perspectives() method.
    }

    public function pulse_groups(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement pulse_groups() method.
    }

    public function pulse_sources(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement pulse_sources() method.
    }

    public function release_dates(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement release_dates() method.
    }

    public function reviews(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement reviews() method.
    }

    public function titles(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement titles() method.
    }

    public function me(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement me() method.
    }

    public function game_versions(ParameterBuilder $paramBuilder): array
    {
        // TODO: Implement game_versions() method.
    }
}