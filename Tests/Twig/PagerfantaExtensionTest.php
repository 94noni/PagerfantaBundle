<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\Twig;

use BabDev\PagerfantaBundle\Twig\PagerfantaExtension;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\DefaultView;
use Pagerfanta\View\ViewFactory;
use Pagerfanta\View\ViewFactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PagerfantaExtensionTest extends TestCase
{
    /**
     * @var ViewFactoryInterface
     */
    private $viewFactory;

    /**
     * @var MockObject|UrlGeneratorInterface
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var PagerfantaExtension
     */
    private $extension;

    protected function setUp(): void
    {
        $this->viewFactory = new ViewFactory();
        $this->viewFactory->set('default', new DefaultView());

        $this->router = $this->createRouter();

        $this->requestStack = new RequestStack();

        $this->extension = new PagerfantaExtension(
            'default',
            $this->viewFactory,
            $this->router,
            $this->requestStack
        );
    }

    protected function tearDown(): void
    {
        do {
            $request = $this->requestStack->pop();
        } while ($request !== null);
    }

    /**
     * @return MockObject|UrlGeneratorInterface
     */
    private function createRouter(): MockObject
    {
        /** @var MockObject|UrlGeneratorInterface $router */
        $router = $this->createMock(UrlGeneratorInterface::class);

        $router->expects($this->any())
            ->method('generate')
            ->willReturnCallback(static function (string $name, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string {
                if (!empty($parameters)) {
                    $query = http_build_query($parameters, '', '&');

                    if ($query !== '') {
                        return '/my-page?'.$query;
                    }
                }

                return '/my-page';
            });

        return $router;
    }

    private function createPagerfanta(): Pagerfanta
    {
        return new Pagerfanta(new FixedAdapter(100, range(1, 100)));
    }

    public function testTheDefaultPagerfantaViewIsRendered(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'pagerfanta_view');
        $request->attributes->set('_route_params', []);

        $this->requestStack->push($request);

        $this->assertViewOutputMatches(
            $this->extension->renderPagerfanta($this->createPagerfanta()),
            '<nav>
    <span class="disabled">Previous</span>
    <span class="current">1</span>
    <a href="/my-page?page=2">2</a>
    <a href="/my-page?page=3">3</a>
    <a href="/my-page?page=4">4</a>
    <a href="/my-page?page=5">5</a>
    <span class="dots">...</span>
    <a href="/my-page?page=10">10</a>
    <a href="/my-page?page=2" rel="next">Next</a>
</nav>'
        );
    }

    public function testTheDefaultPagerfantaViewIsRenderedFromALaterPageWithFirstPageOmitted(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'pagerfanta_view');
        $request->attributes->set('_route_params', []);

        $this->requestStack->push($request);

        $pagerfanta = $this->createPagerfanta();
        $pagerfanta->setCurrentPage(5);

        $this->assertViewOutputMatches(
            $this->extension->renderPagerfanta($pagerfanta, null, ['omitFirstPage' => true]),
            '<nav>
    <a href="/my-page?page=4" rel="prev">Previous</a>
    <a href="/my-page">1</a>
    <a href="/my-page?page=2">2</a>
    <a href="/my-page?page=3">3</a>
    <a href="/my-page?page=4">4</a>
    <span class="current">5</span>
    <a href="/my-page?page=6">6</a>
    <a href="/my-page?page=7">7</a>
    <span class="dots">...</span>
    <a href="/my-page?page=10">10</a>
    <a href="/my-page?page=6" rel="next">Next</a>
</nav>'
        );
    }

    public function testTheDefaultPagerfantaViewIsRenderedWhenConvertingTheViewNameFromAnArray(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'pagerfanta_view');
        $request->attributes->set('_route_params', []);

        $this->requestStack->push($request);

        $pagerfanta = $this->createPagerfanta();
        $pagerfanta->setCurrentPage(5);

        $this->assertViewOutputMatches(
            $this->extension->renderPagerfanta($pagerfanta, ['omitFirstPage' => true]),
            '<nav>
    <a href="/my-page?page=4" rel="prev">Previous</a>
    <a href="/my-page">1</a>
    <a href="/my-page?page=2">2</a>
    <a href="/my-page?page=3">3</a>
    <a href="/my-page?page=4">4</a>
    <span class="current">5</span>
    <a href="/my-page?page=6">6</a>
    <a href="/my-page?page=7">7</a>
    <span class="dots">...</span>
    <a href="/my-page?page=10">10</a>
    <a href="/my-page?page=6" rel="next">Next</a>
</nav>'
        );
    }

    public function testTheDefaultPagerfantaViewIsRenderedWithRouteParams(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'pagerfanta_view');
        $request->attributes->set('_route_params', ['foo' => 'bar']);

        $this->requestStack->push($request);

        $this->assertViewOutputMatches(
            $this->extension->renderPagerfanta($this->createPagerfanta(), null, ['routeParams' => ['goo' => 'car']]),
            '<nav>
    <span class="disabled">Previous</span>
    <span class="current">1</span>
    <a href="/my-page?foo=bar&goo=car&page=2">2</a>
    <a href="/my-page?foo=bar&goo=car&page=3">3</a>
    <a href="/my-page?foo=bar&goo=car&page=4">4</a>
    <a href="/my-page?foo=bar&goo=car&page=5">5</a>
    <span class="dots">...</span>
    <a href="/my-page?foo=bar&goo=car&page=10">10</a>
    <a href="/my-page?foo=bar&goo=car&page=2" rel="next">Next</a>
</nav>'
        );
    }

    public function testTheDefaultPagerfantaViewIsRenderedWhenThePageParameterIsInsideAnArray(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'pagerfanta_view');
        $request->attributes->set('_route_params', []);

        $this->requestStack->push($request);

        $this->assertViewOutputMatches(
            $this->extension->renderPagerfanta($this->createPagerfanta(), null, ['pageParameter' => '[foo][page]']),
            '<nav>
    <span class="disabled">Previous</span>
    <span class="current">1</span>
    <a href="/my-page?foo%5Bpage%5D=2">2</a>
    <a href="/my-page?foo%5Bpage%5D=3">3</a>
    <a href="/my-page?foo%5Bpage%5D=4">4</a>
    <a href="/my-page?foo%5Bpage%5D=5">5</a>
    <span class="dots">...</span>
    <a href="/my-page?foo%5Bpage%5D=10">10</a>
    <a href="/my-page?foo%5Bpage%5D=2" rel="next">Next</a>
</nav>'
        );
    }

    public function testTheDefaultPagerfantaViewIsNotRenderedWhenASubrequestIsActiveAndARouteNameIsNotSpecified(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('PagerfantaBundle can not guess the route when used in a sub-request');

        $masterRequest = Request::create('/');
        $masterRequest->attributes->set('_route', 'pagerfanta_view');
        $masterRequest->attributes->set('_route_params', []);

        $subRequest = Request::create('/_internal');

        $this->requestStack->push($masterRequest);
        $this->requestStack->push($subRequest);

        $this->extension->renderPagerfanta($this->createPagerfanta());
    }

    public function testAPageUrlCanBeGenerated(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'pagerfanta_view');
        $request->attributes->set('_route_params', []);

        $this->requestStack->push($request);

        $this->assertSame(
            '/my-page?page=1',
            $this->extension->getPageUrl($this->createPagerfanta(), 1)
        );
    }

    public function testAPageUrlCannotBeGeneratedIfThePageIsOutOfBounds(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Page '1000' is out of bounds");

        $request = Request::create('/');
        $request->attributes->set('_route', 'pagerfanta_view');
        $request->attributes->set('_route_params', []);

        $this->requestStack->push($request);

        $this->extension->getPageUrl($this->createPagerfanta(), 1000);
    }

    private function assertViewOutputMatches(string $view, string $expected)
    {
        $this->assertSame($this->removeWhitespacesBetweenTags($expected), $view);
    }

    private function removeWhitespacesBetweenTags($string)
    {
        return preg_replace('/>\s+</', '><', $string);
    }
}
