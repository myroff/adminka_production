<?php
namespace Viewer;

/**
 * render template.
 */
class Viewer
{
    /**
     * render template and returns the generated content.
     */
    public function render(string $templateFile ,array $data=[]): string
    {
        $options = []; #array('cache' => TWIG_CACHE_DIR);
        $loader = new \Twig\Loader\FilesystemLoader(TWIG_TEMPLATE_DIR);
        $twig = new \Twig\Environment($loader, $options);
        TwigExtension\ExtensionLoader::loadExtensions($twig);
		$twigTmpl = $twig->load($templateFile);
		return $twigTmpl->render($data);
    }
    /**
     * render template and display the results.
     */
    public function display(string $templateFile ,array $data=[])
    {
        echo $this->render($templateFile , $data);
    }
}
