<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

class DefaultTranslatedViewTest extends TranslatedViewTest
{
    protected function viewClass()
    {
        return 'Pagerfanta\View\DefaultView';
    }

    protected function translatedViewClass()
    {
        return 'BabDev\PagerfantaBundle\View\DefaultTranslatedView';
    }

    protected function previousMessageOption()
    {
        return 'prev_message';
    }

    protected function nextMessageOption()
    {
        return 'next_message';
    }

    protected function buildPreviousMessage($text)
    {
        return sprintf('&#171; %s', $text);
    }

    protected function buildNextMessage($text)
    {
        return sprintf('%s &#187;', $text);
    }

    protected function translatedViewName()
    {
        return 'default_translated';
    }
}
