<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BabDev\PagerfantaBundle\Tests\View;

class TwitterBootstrap3TranslatedView extends TwitterBootstrapTranslatedViewTest
{
    protected function viewClass()
    {
        return 'Pagerfanta\View\TwitterBootstrap3View';
    }

    protected function translatedViewClass()
    {
        return 'BabDev\PagerfantaBundle\View\TwitterBootstrap3TranslatedView';
    }

    protected function translatedViewName()
    {
        return 'twitter_bootstrap3_translated';
    }
}
