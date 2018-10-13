<?php

namespace App\Service\Igdb;

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
      array $options
    ): array {
        $apiUrl = $this->buildQuery();

        $apiUrl .= $id;

        $params = [
          'fields' => implode(',', $fields),
        ];

        $response = $this->callApi($apiUrl, $params + $options);
        $this->response = $response;

        return json_decode($response->getBody());
    }

    protected function search(
      string $search,
      array $options
    ): array {
        $apiUrl = $this->buildQuery();

        $params = [
          'fields' => implode(',', $fields),
          'search' => $search,
        ];

        $response = $this->callApi($apiUrl, $params + $options);
        $this->response = $response;

        return json_decode($response->getBody());
    }

    public function buildQuery(string $endpoint): string
    {
        return rtrim($this->baseUrl,
            '/') . '/' . self::VALID_RESOURCES[$endpoint] . '/';
    }

    /**
     * Using CURL to issue a GET request
     *
     * @param string $url
     * @param array $params
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function callApi(string $url, array $params): ResponseInterface
    {
        $url .= '?' . http_build_query($params);

        $response = $this->httpClient->request('GET', $url, [
          'headers' => [
            'user-key' => $this->igdbKey,
            'Accept' => 'application/json',
          ],
        ]);

        return $response;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Get character information
     *
     * @param integer $characterId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getCharacters(
      int $characterId,
      array $options = ['limit' => 10, 'offset' => 0]
    ): self {
        return $this->getResponseBody($characterId, $fields, $options);
    }

    /**
     * Get company information by ID
     *
     * @param integer $companyId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getCompanies(
      int $companyId,

      array $options = ['limit' => 10, 'offset' => 0]
    ): array {
        return $this->getResponseBody($companyId, $fields, $options);
    }

    /**
     * Get franchise information
     *
     * @param integer $franchiseId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getFranchises(
      int $franchiseId,

      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getResponseBody($franchiseId, $fields, $options);
    }

    /**
     * Get game mode information by ID
     *
     * @param integer $gameModeId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getGameModes(
      int $gameModeId,

      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getResponseBody($gameModeId, $fields, $options);
    }

    /**
     * Get game information by ID
     *
     * @param integer $gameId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getGames(
      int $gameId,

      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getResponseBody($gameId, $fields, $options);
    }

    /**
     * Get genre information by ID
     *
     * @param integer $genreId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getGenres(
      int $genreId,

      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getResponseBody($genreId, $fields, $options);
    }

    /**
     * Get keyword information by ID
     *
     * @param integer $keywordId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getKeywords(
      int $keywordId,

      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getResponseBody($keywordId, $fields, $options);
    }

    /**
     * Get people information by ID
     *
     * @param integer $personId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getPeople(
      int $personId,

      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getResponseBody($personId, $fields, $options);
    }

    /**
     * Get platform information by ID
     *
     * @param integer $platformId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getPlatforms(
      int $platformId,

      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getResponseBody($platformId, $fields, $options);
    }

    /**
     * Get player perspective information by ID
     *
     * @param integer $perspectiveId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getPlayerPerspectives(
      int $perspectiveId,

      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getResponseBody($perspectiveId, $fields, $options);
    }

    /**
     * Get pulse information by ID
     *
     * @param integer $pulseId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getPulses(
      int $pulseId,

      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getResponseBody($pulseId, $fields, $options);
    }

    /**
     * Get collection information by ID
     *
     * @param integer $collectionId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getCollections(
      int $collectionId,

      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getResponseBody($collectionId, $fields, $options);
    }

    /**
     * Get themes information by ID
     *
     * @param integer $themeId
     * @param array $fields
     *
     * @param array $options
     *
     * @return \StdClass
     */
    public function getThemes(
      int $themeId,

      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getResponseBody($themeId, $fields, $options);
    }
}