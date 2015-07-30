<?php

/**
 * This file is part of the GraphAware NeoClient package.
 *
 * (c) GraphAware <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\NeoClient\Formatter;

use Psr\Http\Message\ResponseInterface;

class ResponseFormattingService
{
    /**
     * @var
     */
    protected $nodeClass;

    /**
     * @var
     */
    protected $resultClass;

    /**
     * @var \GraphAware\NeoClient\Formatter\ResultFormatter
     */
    protected $resultFormatter;


    /**
     * @param $resultClass
     * @param $nodeClass
     * @param $relationshipClass
     */
    public function __construct($resultClass, $nodeClass, $relationshipClass)
    {
        $this->resultFormatter = new ResultFormatter();
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $httpResponse
     * @return \GraphAware\NeoClient\Formatter\Response
     */
    public function formatResponse(ResponseInterface $httpResponse)
    {
        $response = new Response($httpResponse);
        if (!$response->hasError()) {
            $body = $response->getBody();
            if (isset($body['results']) && is_array($body['results'])) {
                foreach ($body['results'] as $queryResult) {
                    $result = $this->resultFormatter->formatResult($queryResult);
                }
            }
        }

        return $response;
    }
}