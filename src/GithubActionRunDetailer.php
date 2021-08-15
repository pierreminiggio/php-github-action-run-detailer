<?php

namespace PierreMiniggio\GithubActionRunDetailer;

use PierreMiniggio\GithubActionRun\GithubActionRun;
use PierreMiniggio\GithubActionRunDetailer\Exception\NotFoundException;
use PierreMiniggio\GithubActionRunDetailer\Exception\UnknownException;
use PierreMiniggio\GithubUserAgent\GithubUserAgent;
use RuntimeException;

class GithubActionRunDetailer
{

    /**
     * @throws NotFoundException
     * @throws RuntimeException
     */
    public function find(
        string $owner,
        string $repo,
        int $runId,
        ?string $token = null
    ): GithubActionRun
    {

        $curl = curl_init("https://api.github.com/repos/$owner/$repo/actions/runs/$runId");
        
        $curlOptions = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => GithubUserAgent::USER_AGENT
        ];

        if ($token !== null) {
            $curlOptions[CURLOPT_HTTPHEADER] = ['Authorization: token ' . $token];
        }

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);

        if ($response === false) {
            throw new RuntimeException('Curl error' . curl_error($curl));
        }

        $jsonResponse = json_decode($response, true);

        if ($jsonResponse === null) {
            throw new RuntimeException('Bad Github API return : Bad JSON');
        }

        if (! empty($jsonResponse['message'])) {
            $message = $jsonResponse['message'];

            if ($message === 'Not Found') {
                throw new NotFoundException();
            }

            throw new UnknownException($message);
        }

        return new GithubActionRun(
            (int) $jsonResponse['id'],
            $jsonResponse['status'],
            $jsonResponse['conclusion']
        );
    }
}
