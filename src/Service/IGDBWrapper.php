<?php

namespace App\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

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
      'games' => 'games',
      'characters' => 'characters',
      'companies' => 'companies',
      'game_engines' => 'game_engines',
      'game_modes' => 'game_modes',
      'keywords' => 'keywords',
      'people' => 'people',
      'platforms' => 'platforms',
      'pulses' => 'pulses',
      'themes' => 'themes',
      'collections' => 'collections',
      'player_perspectives' => 'player_perspectives',
      'reviews' => 'reviews',
      'franchises' => 'franchises',
      'genres' => 'genres',
      'release_dates' => 'release_dates',
    ];

    /**
     * IGDB constructor.
     *
     * @param string $key
     *
     * @param string $url
     *
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

    /**
     * Get character information
     *
     * @param integer $characterId
     * @param array $fields
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getCharacter($characterId, $fields = ['*']): \StdClass
    {
        return $this->getSingle($characterId, $fields);
    }

    protected function getSingle(int $id, array $fields): \StdClass
    {
        $endpointName = $this->getDirectParentCallFunctionName();
        $apiUrl = $this->getEndpoint($endpointName);
        $apiUrl .= $id;

        $params = [
          'fields' => implode(',', $fields),
        ];

        $apiData = $this->apiGet($apiUrl, $params);

        return $this->decodeSingle($apiData);
    }

    protected function getDirectParentCallFunctionName(): string
    {
        $name = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'];
        return lcfirst(substr($name, 3));
    }

    /**
     * Search characters by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function searchCharacters(
      string $search,
      array $fields = ['*'],
      int $limit = 10,
      int $offset = 0
    ): \StdClass {
        return $this->getMultiple($fields, $limit, $offset, $search);
    }

    protected function getMultiple(
      array $fields,
      int $limit,
      int $offset,
      string $search = null,
      string $order = null
    ): \StdClass {
        $endpointName = $this->getDirectParentCallFunctionName();
        $apiUrl = $this->getEndpoint($endpointName);

        $params = [
          'fields' => implode(',', $fields),
          'limit' => $limit,
          'offset' => $offset,
        ];

        !$search ? $params['search'] = $search : null;
        !$order ? $params['order'] = $order : null;

        $apiData = $this->apiGet($apiUrl, $params);

        return $this->decodeMultiple($apiData);
    }

    /**
     * Get company information by ID
     *
     * @param integer $companyId
     * @param array $fields
     *
     * @return \StdClass
     */
    public function getCompanies($companyId, $fields = ['*']): \StdClass
    {
        return $this->getSingle($companyId, $fields);
    }

    /**
     * Search companies by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function searchCompanies(
      string $search,
      array $fields = ['*'],
      int $limit = 10,
      int $offset = 0
    ): \StdClass {
        return $this->getMultiple($fields, $limit, $offset, $search);
    }

    /**
     * Get franchise information
     *
     * @param integer $franchiseId
     * @param array $fields
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getFranchises(
      int $franchiseId,
      array $fields = ['*']
    ): \StdClass {
        return $this->getSingle($franchiseId, $fields);
    }

    /**
     * Search franchises by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function searchFranchises(
      string $search,
      array $fields = ['*'],
      int $limit = 10,
      int $offset = 0
    ) {
        return $this->getMultiple($fields, $limit, $offset, $search);
    }

    /**
     * Get game mode information by ID
     *
     * @param integer $gameModeId
     * @param array $fields
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getGameModes(
      int $gameModeId,
      array $fields = ['name', 'slug', 'url']
    ): \StdClass {
        return $this->getSingle($gameModeId, $fields);
    }

    /**
     * Search game modes by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function searchGameModes(
      string $search,
      array $fields = ['name', 'slug', 'url'],
      int $limit = 10,
      int $offset = 0
    ) {
        return $this->getMultiple($fields, $limit, $offset, $search);
    }

    /**
     * Get game information by ID
     *
     * @param integer $gameId
     * @param array $fields
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getGames(int $gameId, array $fields = ['*']): \StdClass
    {
        return $this->getSingle($gameId, $fields);
    }

    /**
     * Search games by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     * @param string $order
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function searchGames(
      string $search,
      array $fields = ['*'],
      int $limit = 10,
      int $offset = 0,
      string $order = null
    ): \StdClass {
        return $this->getMultiple($fields, $limit, $offset, $search, $order);
    }

    /**
     * Get genre information by ID
     *
     * @param integer $genreId
     * @param array $fields
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getGenres(
      int $genreId,
      array $fields = ['name', 'slug', 'url']
    ): \StdClass {
        return $this->getSingle($genreId, $fields);
    }

    /**
     * Search genres by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function searchGenres(
      string $search,
      array $fields = ['name', 'slug', 'url'],
      int $limit = 10,
      int $offset = 0
    ): \StdClass {
        return $this->getMultiple($fields, $limit, $offset, $search);
    }

    /**
     * Get keyword information by ID
     *
     * @param integer $keywordId
     * @param array $fields
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getKeywords(
      int $keywordId,
      array $fields = ['name', 'slug', 'url']
    ): \StdClass {
        return $this->getSingle($keywordId, $fields);
    }

    /**
     * Search keywords by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function searchKeywords(
      string $search,
      array $fields = ['name', 'slug', 'url'],
      int $limit = 10,
      int $offset = 0
    ): \StdClass {
        return $this->getMultiple($fields, $limit, $offset, $search);
    }

    /**
     * Get people information by ID
     *
     * @param integer $personId
     * @param array $fields
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getPeople(int $personId, array $fields = ['*']): \StdClass
    {
        return $this->getSingle($personId, $fields);
    }

    /**
     * Search people by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function searchPeople(
      string $search,
      array $fields = ['name', 'slug', 'url'],
      int $limit = 10,
      int $offset = 0
    ): \StdClass {
        return $this->getMultiple($fields, $limit, $offset, $search);
    }

    /**
     * Get platform information by ID
     *
     * @param integer $platformId
     * @param array $fields
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getPlatforms(
      int $platformId,
      array $fields = ['name', 'logo', 'slug', 'url']
    ): \StdClass {
        return $this->getSingle($platformId, $fields);
    }

    /**
     * Search platforms by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function searchPlatforms(
      string $search,
      array $fields = ['name', 'logo', 'slug', 'url'],
      int $limit = 10,
      int $offset = 0
    ): \StdClass {
        return $this->getMultiple($fields, $limit, $offset, $search);
    }

    /**
     * Get player perspective information by ID
     *
     * @param integer $perspectiveId
     * @param array $fields
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getPlayerPerspectives(
      int $perspectiveId,
      array $fields = ['name', 'slug', 'url']
    ): \StdClass {
        // TODO: fix endpoint naming
        return $this->getSingle($perspectiveId, $fields);
    }

    /**
     * Search player perspective by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function searchPlayerPerspectives(
      string $search,
      array $fields = ['name', 'slug', 'url'],
      int $limit = 10,
      int $offset = 0
    ): \StdClass {
        return $this->getMultiple($fields, $limit, $offset, $search);
    }

    /**
     * Get pulse information by ID
     *
     * @param integer $pulseId
     * @param array $fields
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getPulses(int $pulseId, array $fields = ['*']): \StdClass
    {
        return $this->getSingle($pulseId, $fields);
    }

    /**
     * Search pulses by title
     *
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function fetchPulses(
      array $fields = ['*'],
      int $limit = 10,
      int $offset = 0
    ): \StdClass {
        return $this->getMultiple($fields, $limit, $offset);
    }

    /**
     * Get collection information by ID
     *
     * @param integer $collectionId
     * @param array $fields
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getCollections(
      int $collectionId,
      array $fields = ['*']
    ): \StdClass {
        return $this->getSingle($collectionId, $fields);
    }

    /**
     * Search collections by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function searchCollections(
      string $search,
      array $fields = ['*'],
      int $limit = 10,
      int $offset = 0
    ): \StdClass {
        return $this->getMultiple($fields, $limit, $offset, $search);
    }

    /**
     * Get themes information by ID
     *
     * @param integer $themeId
     * @param array $fields
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getThemes(
      int $themeId,
      array $fields = ['name', 'slug', 'url']
    ): \StdClass {
        return $this->getSingle($themeId, $fields);
    }

    /**
     * Search themes by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function searchThemes(
      string $search,
      array $fields = ['name', 'slug', 'url'],
      int $limit = 10,
      int $offset = 0
    ): \StdClass {
        return $this->getMultiple($fields, $limit, $offset, $search);
    }
    /*
     *  Internally used Methods, set visibility to public to enable more flexibility
     */
    /**
     * @param $name
     *
     * @return mixed
     */
    private function getEndpoint(string $name)
    {
        return rtrim($this->baseUrl,
            '/') . '/' . self::VALID_RESOURCES[$name] . '/';
    }

    /**
     * Decode the response from IGDB, extract the single resource object.
     * (Don't use this to decode the response containing list of objects)
     *
     * @param  string $apiData the api response from IGDB
     *
     * @throws \Exception
     * @return bool|\StdClass
     */
    private function decodeSingle(string &$apiData): \StdClass
    {
        $resObj = json_decode($apiData);

        if (!is_array($resObj) || count($resObj) == 0) {
            throw new \Exception("Empty JSON Object returned");
        }

        if (isset($resObj->status)) {
            $msg = "Error " . $resObj->status . " " . $resObj->message;
            throw new \Exception($msg);
        }
        dd($resObj[0]);
        return $resObj[0];
    }

    /**
     * Decode the response from IGDB, extract the multiple resource object.
     *
     * @param  string $apiData the api response from IGDB
     *
     * @throws \Exception
     * @return \StdClass
     */
    private function decodeMultiple(string &$apiData): \StdClass
    {
        $resObj = json_decode($apiData);

        if (isset($resObj->status)) {
            $msg = "Error " . $resObj->status . " " . $resObj->message;
            throw new \Exception($msg);
        } else {
            //$itemsArray = $resObj->items;
            if (!is_array($resObj)) {
                throw new \Exception("Empty JSON Object returned");
            } else {
                return $resObj;
            }
        }
    }

    /**
     * Using CURL to issue a GET request
     *
     * @param $url
     * @param $params
     *
     * @return mixed
     * @throws \Exception
     */
    private function apiGet(string $url, $params)
    {
        $url = $url . (strpos($url,
            '?') === false ? '?' : '') . http_build_query($params);
        try {
            $response = $this->httpClient->request('GET', $url, [
              'headers' => [
                'user-key' => $this->igdbKey,
                'Accept' => 'application/json',
              ],
            ]);
        } catch (RequestException $exception) {
            if ($response = $exception->getResponse()) {
                throw new \Exception($exception);
            }
            throw new \Exception($exception);
        } catch (GuzzleException $exception) {
            throw new \Exception($exception);
        }
        return $response->getBody();
    }
}