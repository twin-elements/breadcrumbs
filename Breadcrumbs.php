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
            $this->generator->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );
    }

    /**
     * @param $page Page Page instance object for which you want generate breadcrumbs
     *
     * @return \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function generatePageBreadcrumbs(Page $page): \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
    {
        if($page->getParent()){
            $this->generatePageBreadcrumbs($page->getParent());
        }

        $this->breadcrumbs->addItem($page->getTitle(), $this->generatePageUrl($page));

        return $this->breadcrumbs;
    }

    private function generatePageUrl(Page $page)
    {
        return $page->getRoute() ?
            $this->generator->generate($page->getRoute(),[],UrlGeneratorInterface::ABSOLUTE_URL) :
            $this->generator->generate(PagePath::ROUTE, [
                'id' => $page->getId(),
                'slug' => $page->getSlug()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @return \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function getBreadcrumbs(): \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
    {
        return $this->breadcrumbs;
    }
}
