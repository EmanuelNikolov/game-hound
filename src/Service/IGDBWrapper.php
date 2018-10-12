<?php

namespace App\Service;

use GuzzleHttp\ClientInterface;

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
     * @param string $url
     * @param ClientInterface $client
     *
     * @throws \Exception
     */
    public function __construct(
      string $key,
      string $url,
      ClientInterface $client
    ) {
        if (empty($key)) {
            throw new \Exception('IGDB API key is required, please visit https://api.igdb.com/ to request a key');
        }

        if (empty($url)) {
            throw new \Exception('IGDB Request URL is required, please visit https://api.igdb.com/ to get your Request URL');
        }

        $this->igdbKey = $key;
        $this->baseUrl = $url;
        $this->httpClient = $client;
    }

    protected function getSingle(
      int $id,
      array $fields,
      array $options
    ): \StdClass {
        $endpointName = $this->getDirectParentCallFunctionName();
        $apiUrl = $this->getEndpoint($endpointName);

        $apiUrl .= $id;

        $params = [
          'fields' => implode(',', $fields),
        ];

        $apiData = $this->apiGet($apiUrl, $params + $options);

        return $this->decode($apiData)->current();
    }

    protected function getMultiple(
      string $search,
      array $fields,
      array $options
    ): \Generator {
        $endpointName = $this->getDirectParentCallFunctionName();
        $apiUrl = $this->getEndpoint($endpointName);

        $params = [
          'fields' => implode(',', $fields),
          'search' => $search,
        ];

        $apiData = $this->apiGet($apiUrl, $params + $options);

        return $this->decode($apiData);
    }

    protected function getDirectParentCallFunctionName(): string
    {
        $name = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'];

        for ($offset = 0; !ctype_upper($name[$offset]); ++$offset) {
        }

        return substr($name, $offset);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getSingle($characterId, $fields, $options);
    }

    /**
     * Search characters by name
     *
     * @param string $search
     * @param array $fields
     * @param array $options
     *
     * @return array
     */
    public function searchCharacters(
      string $search,
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass  {
        return $this->getSingle($companyId, $fields, $options);
    }

    /**
     * Search companies by name
     *
     * @param string $search
     * @param array $fields
     *
     * @param array $options
     *
     * @return array
     */
    public function searchCompanies(
      string $search,
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getSingle($franchiseId, $fields, $options);
    }

    /**
     * Search franchises by name
     *
     * @param string $search
     * @param array $fields
     * @param array $options
     *
     * @return array
     */
    public function searchFranchises(
      string $search,
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getSingle($gameModeId, $fields, $options);
    }

    /**
     * Search game modes by name
     *
     * @param string $search
     * @param array $fields
     * @param array $options
     *
     * @return \Generator
     */
    public function searchGameModes(
      string $search,
      array $fields = ['name', 'slug', 'url'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getSingle($gameId, $fields, $options);
    }

    /**
     * Search games by name
     *
     * @param string $search
     * @param array $fields
     * @param array $options
     *
     * @return array
     */
    public function searchGames(
      string $search,
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getSingle($genreId, $fields, $options);
    }

    /**
     * Search genres by name
     *
     * @param string $search
     * @param array $fields
     * @param array $options
     *
     * @return array
     */
    public function searchGenres(
      string $search,
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getSingle($keywordId, $fields, $options);
    }

    /**
     * Search keywords by name
     *
     * @param string $search
     * @param array $fields
     * @param array $options
     *
     * @return array
     */
    public function searchKeywords(
      string $search,
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getSingle($personId, $fields, $options);
    }

    /**
     * Search people by name
     *
     * @param string $search
     * @param array $fields
     *
     * @param array $options
     *
     * @return array
     */
    public function searchPeople(
      string $search,
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getSingle($platformId, $fields, $options);
    }

    /**
     * Search platforms by name
     *
     * @param string $search
     * @param array $fields
     * @param array $options
     *
     * @return array
     */
    public function searchPlatforms(
      string $search,
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getSingle($perspectiveId, $fields, $options);
    }

    /**
     * Search player perspective by name
     *
     * @param string $search
     * @param array $fields
     * @param array $options
     *
     * @return array
     */
    public function searchPlayerPerspectives(
      string $search,
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getSingle($pulseId, $fields, $options);
    }

    /**
     * Search pulses by title
     *
     * @param string $search
     * @param array $fields
     * @param array $options
     *
     * @return array
     */
    public function searchPulses(
      string $search,
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getSingle($collectionId, $fields, $options);
    }

    /**
     * Search collections by name
     *
     * @param string $search
     * @param array $fields
     * @param array $options
     *
     * @return array
     */
    public function searchCollections(
      string $search,
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
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
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \StdClass {
        return $this->getSingle($themeId, $fields, $options);
    }

    /**
     * Search themes by name
     *
     * @param string $search
     * @param array $fields
     * @param array $options
     *
     * @return \Generator
     */
    public function searchThemes(
      string $search,
      array $fields = ['*'],
      array $options = ['limit' => 10, 'offset' => 0]
    ): \Generator {
        return $this->getMultiple($search, $fields, $options);
    }
    /*
     *  Internally used Methods, set visibility to public to enable more flexibility
     */
    /**
     * @param $name
     *
     * @return mixed
     */
    private function getEndpoint(string $name): string
    {
        return rtrim($this->baseUrl,
            '/') . '/' . self::VALID_RESOURCES[$name] . '/';
    }

    /**
     * Decode the response from IGDB, extract the multiple resource object.
     *
     * @param  string $apiData the api response from IGDB
     *
     * @return \Generator|\StdClass
     * @throws \Exception
     */
    private function decode(string &$apiData): \Generator
    {
        $respData = json_decode($apiData);

        if (!is_array($respData) || empty($respData)) {
            throw new \Exception("Empty JSON Object returned");
        }

        foreach ($respData as $obj) {
            yield $obj;
        };
    }

    /**
     * Using CURL to issue a GET request
     *
     * @param string $url
     * @param array $params
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function apiGet(string $url, array $params): string
    {
        $url .= (!strpos($url, '?') ? '?' : '') . http_build_query($params);

        $response = $this->httpClient->request('GET', $url, [
          'headers' => [
            'user-key' => $this->igdbKey,
            'Accept' => 'application/json',
          ],
        ]);
        dd(json_decode($response->getBody()->getContents()));

        return $response->getBody();
    }
}