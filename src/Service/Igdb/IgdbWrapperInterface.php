<?php

namespace App\Service\Igdb;


use App\Service\Igdb\Utils\ParameterBuilder;
use Psr\Http\Message\ResponseInterface;

interface IgdbWrapperInterface
{

    public function callApi(
      string $endpoint,
      ParameterBuilder $paramBuilder
    ): array;

    public function search(
      string $search,
      string $endpoint,
      ParameterBuilder $paramBuilder
    ): array;

    public function scroll(ResponseInterface $response = null): array;

    public function getScrollCount(ResponseInterface $response = null): int;

    public function sendRequest(string $url): ResponseInterface;

    public function processResponse(ResponseInterface $response): array;

    public function getEndpoint(string $endpoint): string;

    public function getScrollHeader(
      ResponseInterface $response,
      string $header
    ): string;

    public function achievements(ParameterBuilder $paramBuilder): array;

    public function characters(ParameterBuilder $paramBuilder): array;

    public function collections(ParameterBuilder $paramBuilder): array;

    public function companies(ParameterBuilder $paramBuilder): array;

    public function credits(ParameterBuilder $paramBuilder): array;

    public function external_reviews(ParameterBuilder $paramBuilder): array;

    public function external_review_sources(ParameterBuilder $paramBuilder
    ): array;

    public function feeds(ParameterBuilder $paramBuilder): array;

    public function franchises(ParameterBuilder $paramBuilder): array;

    public function games(ParameterBuilder $paramBuilder): array;

    public function game_engines(ParameterBuilder $paramBuilder): array;

    public function game_modes(ParameterBuilder $paramBuilder): array;

    public function genres(ParameterBuilder $paramBuilder): array;

    public function keywords(ParameterBuilder $paramBuilder): array;

    public function pages(ParameterBuilder $paramBuilder): array;

    public function people(ParameterBuilder $paramBuilder): array;

    public function platforms(ParameterBuilder $paramBuilder): array;

    public function play_times(ParameterBuilder $paramBuilder): array;

    public function player_perspectives(ParameterBuilder $paramBuilder): array;

    public function pulses(ParameterBuilder $paramBuilder): array;

    public function pulse_groups(ParameterBuilder $paramBuilder): array;

    public function pulse_sources(ParameterBuilder $paramBuilder): array;

    public function release_dates(ParameterBuilder $paramBuilder): array;

    public function reviews(ParameterBuilder $paramBuilder): array;

    public function themes(ParameterBuilder $paramBuilder): array;

    public function titles(ParameterBuilder $paramBuilder): array;

    public function me(ParameterBuilder $paramBuilder): array;

    public function game_versions(ParameterBuilder $paramBuilder): array;
}