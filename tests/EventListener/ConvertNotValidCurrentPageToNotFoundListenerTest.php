<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\EventListener;

use BabDev\PagerfantaBundle\EventListener\ConvertNotValidCurrentPageToNotFoundListener;
use Pagerfanta\Exception\NotValidCurrentPageException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ConvertNotValidCurrentPageToNotFoundListenerTest extends TestCase
{
    public function testListenerConvertsExceptionForEvent(): void
    {
        $exception = new NotValidCurrentPageException();

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $this->createMock(Request::class),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        (new ConvertNotValidCurrentPageToNotFoundListener())->onKernelException($event);

        $this->assertInstanceOf(NotFoundHttpException::class, $event->getThrowable());
        $this->assertSame($exception, $event->getThrowable()->getPrevious());
    }

    public function testListenerDoesNotConvertUnknownExceptionForEvent(): void
    {
        $exception = new \RuntimeException();

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $this->createMock(Request::class),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        (new ConvertNotValidCurrentPageToNotFoundListener())->onKernelException($event);

        $this->assertSame($exception, $event->getThrowable());
    }
}
