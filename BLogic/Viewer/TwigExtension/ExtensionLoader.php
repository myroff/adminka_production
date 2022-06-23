<?php
namespace Viewer\TwigExtension;

/**
 * load our custom twig extensions
 */
class ExtensionLoader
{
    /**
     * add my extensions to twig environment.
     */
    public static function loadExtensions(\Twig\Environment &$twig)
    {
        $twig->addExtension(new \Viewer\TwigExtension\FunctionLoader());
    }
}
