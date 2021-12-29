<?php

namespace TwinElements\Component\Breadcrumbs;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use TwinElements\PageBundle\Entity\Page\Page;
use TwinElements\PageBundle\PagePath;

class Breadcrumbs
{
    private $breadcrumbs;
    private $translator;
    private $generator;

    /**
     * GenerateBreadcrumbs constructor.
     */
    public function __construct(\WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs $breadcrumbs, TranslatorInterface $translator, UrlGeneratorInterface $generator)
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->translator = $translator;
        $this->generator = $generator;

        $this->breadcrumbs->addItem(
            $this->translator->trans('translations.homepage', [], 'translations'),
            $this->generator->generate('homepage')
        );
    }

    /**
     * @param $page Page Page instance object for which you want generate breadcrumbs
     * @param $isLast bool if true will generate url from request
     *
     * @return \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function generatePageBreadcrumbs(Page $page, bool $isLast = true): \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
    {
        if ($isLast) {
            $this->breadcrumbs->addItem($page->getTitle(), $this->generatePageUrl($page));
        } else {
            $this->breadcrumbs->prependItem($page->getTitle(), $this->generatePageUrl($page));
        }

        $parentPage = $page->getParent();

        while ($parentPage && $parentPage->getId() !== 1) {
            $this->breadcrumbs->prependItem($parentPage->getTitle(), $this->generatePageUrl($parentPage));
            $parentPage = $parentPage->getParent();
        }

        return $this->breadcrumbs;
    }

    private function generatePageUrl(Page $page)
    {
        return $page->getRoute() ?
            $this->generator->generate($page->getRoute()) :
            $this->generator->generate(PagePath::ROUTE, ['id' => $page->getId(), 'slug' => $page->getSlug()]);
    }

    /**
     * @return \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function getBreadcrumbs(): \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
    {
        return $this->breadcrumbs;
    }
}
