<?php

namespace App\EventListener;

use App\Exception\ApplicationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $systemError = 'Произошла ошибка системы, пожалуйста повторите позднее';
        $applicationErrors = [];
        $exception = $event->getThrowable();

        if ($exception instanceof ApplicationException) {
            $applicationErrors = $exception->getErrors();
        } else {
            $this->logger->error($exception->getMessage().'; '.$exception->getFile().'; '.$exception->getLine());
        }

        $response = $this->render('error/errors.html.twig', [
            'systemError' => $systemError,
            'applicationErrors' => $applicationErrors
        ]);
        $event->setResponse($response);
    }
}
