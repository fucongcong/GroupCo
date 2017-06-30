<?php

namespace Group\Controller;

use ServiceProvider;
use Group\Twig\WebExtension;

class TwigServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return object
     */
    public function register()
    {   
        $this->app->singleton('twig', function(){
            $loader = new \Twig_Loader_Filesystem(\Config::get('view::path'));

            if (\Config::get('view::cache')) {
                $env = array(
                    'cache' => \Config::get('view::cache_dir')
                );
            }

            $twig = new \Twig_Environment($loader, isset($env) ? $env : array());
            
            $twig->addExtension(new WebExtension());
            $extensions = \Config::get('view::extensions');
            foreach ($extensions as $extension) {
                $twig->addExtension(new $extension);
            }

            return $twig;
        });
    }

    public function getName()
    {
        return 'twig';
    }

}
