<?php

namespace PYS_PRO_GLOBAL\GuzzleHttp;

use PYS_PRO_GLOBAL\Psr\Http\Message\RequestInterface;
use PYS_PRO_GLOBAL\Psr\Http\Message\ResponseInterface;
use PYS_PRO_GLOBAL\Psr\Http\Message\UriInterface;
/**
 * Represents data at the point after it was transferred either successfully
 * or after a network error.
 */
final class TransferStats
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var ResponseInterface|null
     */
    private $response;
    /**
     * @var float|null
     */
    private $transferTime;
    /**
     * @var array
     */
    private $handlerStats;
    /**
     * @var mixed|null
     */
    private $handlerErrorData;
    /**
     * @param RequestInterface       $request          Request that was sent.
     * @param ResponseInterface|null $response         Response received (if any)
     * @param float|null             $transferTime     Total handler transfer time.
     * @param mixed                  $handlerErrorData Handler error data.
     * @param array                  $handlerStats     Handler specific stats.
     */
    public function __construct(\PYS_PRO_GLOBAL\Psr\Http\Message\RequestInterface $request, ?\PYS_PRO_GLOBAL\Psr\Http\Message\ResponseInterface $response = null, ?float $transferTime = null, $handlerErrorData = null, array $handlerStats = [])
    {
        $this->request = $request;
        $this->response = $response;
        $this->transferTime = $transferTime;
        $this->handlerErrorData = $handlerErrorData;
        $this->handlerStats = $handlerStats;
    }
    public function getRequest() : \PYS_PRO_GLOBAL\Psr\Http\Message\RequestInterface
    {
        return $this->request;
    }
    /**
     * Returns the response that was received (if any).
     */
    public function getResponse() : ?\PYS_PRO_GLOBAL\Psr\Http\Message\ResponseInterface
    {
        return $this->response;
    }
    /**
     * Returns true if a response was received.
     */
    public function hasResponse() : bool
    {
        return $this->response !== null;
    }
    /**
     * Gets handler specific error data.
     *
     * This might be an exception, a integer representing an error code, or
     * anything else. Relying on this value assumes that you know what handler
     * you are using.
     *
     * @return mixed
     */
    public function getHandlerErrorData()
    {
        return $this->handlerErrorData;
    }
    /**
     * Get the effective URI the request was sent to.
     */
    public function getEffectiveUri() : \PYS_PRO_GLOBAL\Psr\Http\Message\UriInterface
    {
        return $this->request->getUri();
    }
    /**
     * Get the estimated time the request was being transferred by the handler.
     *
     * @return float|null Time in seconds.
     */
    public function getTransferTime() : ?float
    {
        return $this->transferTime;
    }
    /**
     * Gets an array of all of the handler specific transfer data.
     */
    public function getHandlerStats() : array
    {
        return $this->handlerStats;
    }
    /**
     * Get a specific handler statistic from the handler by name.
     *
     * @param string $stat Handler specific transfer stat to retrieve.
     *
     * @return mixed|null
     */
    public function getHandlerStat(string $stat)
    {
        return isset($this->handlerStats[$stat]) ? $this->handlerStats[$stat] : null;
    }
}
