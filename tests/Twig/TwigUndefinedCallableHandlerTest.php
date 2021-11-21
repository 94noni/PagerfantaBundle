<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\Twig;

use BabDev\PagerfantaBundle\Twig\UndefinedCallableHandler;
use PHPUnit\Framework\TestCase;
use Twig\Error\SyntaxError;

final class TwigUndefinedCallableHandlerTest extends TestCase
{
    public function dataSupportedFunctions(): \Generator
    {
        yield '"pagerfanta" function' => ['pagerfanta'];
        yield '"pagerfanta_page_url" function' => ['pagerfanta_page_url'];
    }

    /**
     * @dataProvider dataSupportedFunctions
     */
    public function testThrowsASyntaxErrorForSupportedTwigFunctionsWhenNotDefined(string $function): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(sprintf('Unknown function "%s". Did you forget to run "composer require pagerfanta/twig"?', $function));

        (new UndefinedCallableHandler())->onUndefinedFunction($function);
    }

    public function testReportsAFunctionAsNotSupported(): void
    {
        self::assertFalse((new UndefinedCallableHandler())->onUndefinedFunction('asset'));
    }
}
