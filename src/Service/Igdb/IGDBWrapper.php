<?php

namespace App\Service\Igdb;

use App\Service\Igdb\Exception\ScrollHeaderNotFoundException;
use App\Service\Igdb\Utils\ParameterBuilder;
use App\Service\Igdb\ValidEndpoints as Endpoint;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class IGDBWrapper
{

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $igdbKey;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * IGDB constructor.
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

        $this->igdbKey = $key;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->httpClient = $client;
    }

    public function callApi(
      string $endpoint,
      ParameterBuilder $paramBuilder
    ): array {
        $url = $this->getEndpoint($endpoint) . $paramBuilder->buildQueryString();

        $response = $this->sendRequest($url);

        return $this->processResponse($response);
    }

    public function search(
      string $search,
      string $endpoint,
      ParameterBuilder $paramBuilder
    ): array {
        $paramBuilder->setSearch($search);

        return $this->callApi($endpoint, $paramBuilder);
    }

    public function scroll(ResponseInterface $response = null): array
    {
        if (null === $response) {
            $response = $this->response;
        }

        $endpoint = $this->getScrollHeader($response, 'X-Next-Page');

        $url = $this->baseUrl . $endpoint;

        $scrollResponse = $this->sendRequest($url);

        return $this->processResponse($scrollResponse);
    }

    public function getScrollCount(ResponseInterface $response = null): int
    {
        if (null === $response) {
            $response = $this->response;
        }

        return $this->getScrollHeader($response, 'X-Count');
    }

    public function sendRequest(string $url): ResponseInterface
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
              'headers' => [
                'user-key' => $this->igdbKey,
                'Accept' => 'application/json',
              ],
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }

        $this->response = $response;

        return $response;
    }

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

    public function getEndpoint(string $endpoint): string
    {
        return $this->baseUrl . '/' . $endpoint . '/';
    }

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

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Get character information
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function characters(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::CHARACTERS, $paramBuilder);
    }

    /**
     * Get company information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function companies(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::COMPANIES, $paramBuilder);
    }

    /**
     * Get franchise information
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function franchises(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::FRANCHISES, $paramBuilder);
    }

    /**
     * Get game mode information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function gameModes(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::GAME_MODES, $paramBuilder);
    }

    /**
     * Get game information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function games(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::GAMES, $paramBuilder);
    }

    /**
     * Get genre information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function genres(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::GENRES, $paramBuilder);
    }

    /**
     * Get keyword information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function keywords(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::KEYWORDS, $paramBuilder);
    }

    /**
     * Get people information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function people(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PEOPLE, $paramBuilder);
    }

    /**
     * Get platform information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function platforms(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PLATFORMS, $paramBuilder);
    }

    /**
     * Get player perspective information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function playerPerspectives(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PLAYER_PERSPECTIVES, $paramBuilder);
    }

    /**
     * Get pulse information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function pulses(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::PULSES, $paramBuilder);
    }

    /**
     * Get collection information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function collections(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::COLLECTIONS, $paramBuilder);
    }

    /**
     * Get themes information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function themes(ParameterBuilder $paramBuilder): array
    {
        return $this->callApi(Endpoint::THEMES, $paramBuilder);
    }
}