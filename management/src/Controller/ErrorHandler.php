<?php

declare(strict_types=1);

namespace App\Controller;

use DomainException;
use Psr\Log\LoggerInterface;

/**
 * Class ErrorHandler
 * @package App\Controller
 */
class ErrorHandler
{
    private LoggerInterface $logger;

    /**
     * ErrorHandler constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param DomainException $exception
     */
    public function handle(DomainException $exception): void
    {
        $this->logger->warning($exception->getMessage(), ['exception' => $exception]);
    }
}
