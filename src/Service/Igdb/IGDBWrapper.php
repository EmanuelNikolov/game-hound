<?php

namespace App\Service\Igdb;

use App\Service\Igdb\Utils\ParameterBuilder;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class IGDBWrapper
{

    /**
     * @var string
     */
    protected $igdbKey;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var array
     */
    const VALID_RESOURCES = [
      'Achievements' => 'achievements',
      'Characters' => 'characters',
      'Collections' => 'collections',
      'Companies' => 'companies',
      'Credits' => 'credits',
      'ExternalReviews' => 'external_reviews',
      'ExternalReviewSources' => 'external_review_sources',
      'Feeds' => 'feeds',
      'Franchises' => 'franchises',
      'Games' => 'games',
      'GameEngines' => 'game_engines',
      'GameModes' => 'game_modes',
      'Genres' => 'genres',
      'Keywords' => 'keywords',
      'Pages' => 'pages',
      'People' => 'people',
      'Platforms' => 'platforms',
      'PlayTimes' => 'play_times',
      'PlayerPerspectives' => 'player_perspectives',
      'Pulses' => 'pulses',
      'PulseGroups' => 'pulse_groups',
      'PulseSources' => 'pulse_sources',
      'ReleaseDates' => 'release_dates',
      'Reviews' => 'reviews',
      'Themes' => 'themes',
      'Titles' => 'titles',
      'Me' => 'me',
      'GameVersions' => 'game_versions',
    ];

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
        $this->baseUrl = $baseUrl;
        $this->httpClient = $client;
    }

    protected function getResponseBody(
      string $endpoint,
      ParameterBuilder $paramBuilder
    ): array {
        $url = $this->getEndpoint($endpoint);
        $completeUrl = $url . $paramBuilder->buildQueryString();

        $response = $this->httpClient->request('GET', $completeUrl,
          [
            'headers' => [
              'user-key' => $this->igdbKey,
              'Accept' => 'application/json',
            ],
          ]);

        $this->response = $response;

        return json_decode($response->getBody());
    }

    public function search(
      string $search,
      string $endpoint,
      ParameterBuilder $paramBuilder
    ): array {
        $paramBuilder->setSearch($search);

        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    public function getEndpoint(string $endpoint): string
    {
        return rtrim($this->baseUrl, '/')
          . '/'
          . self::VALID_RESOURCES[$endpoint]
          . '/';
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
    public function getCharacters(ParameterBuilder $paramBuilder): array
    {
        // todo: endpoint again :D
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    /**
     * Get company information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function getCompanies(ParameterBuilder $paramBuilder): array
    {
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    /**
     * Get franchise information
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function getFranchises(ParameterBuilder $paramBuilder): array
    {
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    /**
     * Get game mode information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function getGameModes(ParameterBuilder $paramBuilder): array
    {
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    /**
     * Get game information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function getGames(ParameterBuilder $paramBuilder): array
    {
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    /**
     * Get genre information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function getGenres(ParameterBuilder $paramBuilder): array
    {
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    /**
     * Get keyword information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function getKeywords(ParameterBuilder $paramBuilder): array
    {
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    /**
     * Get people information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function getPeople(ParameterBuilder $paramBuilder): array
    {
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    /**
     * Get platform information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function getPlatforms(ParameterBuilder $paramBuilder): array
    {
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    /**
     * Get player perspective information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function getPlayerPerspectives(ParameterBuilder $paramBuilder): array
    {
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    /**
     * Get pulse information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function getPulses(ParameterBuilder $paramBuilder): array
    {
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    /**
     * Get collection information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function getCollections(ParameterBuilder $paramBuilder): array
    {
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }

    /**
     * Get themes information by ID
     *
     * @param \App\Service\Igdb\Utils\ParameterBuilder $paramBuilder
     *
     * @return array
     */
    public function getThemes(ParameterBuilder $paramBuilder): array
    {
        $endpoint = substr(__FUNCTION__, 3);
        return $this->getResponseBody($endpoint, $paramBuilder);
    }
}