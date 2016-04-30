<?php
/**
 * Created by PhpStorm.
 * User: Benoît
 * Date: 14/04/2016
 * Time: 21:36
 */

namespace Services;

class Twig extends \Twig_Environment
{
    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem('Views');
        parent::__construct($loader);
        $function = new \Twig_SimpleFunction('path',array($this, 'path'));
        $this->addFunction($function);
    }

    public function path($chemin, $params = null)
    {
        $router = new Routing();
        $serveur = $_SERVER['SCRIPT_NAME'];

        $monChemin = $router->findByRoute($chemin);
        $chemin = $monChemin['chemin'];
        $idParams = $router->formatParam($chemin);
        if($params != null )
        {
            $chemin = $router->formatChemin($chemin);
            $chemin = rtrim($chemin,"/");
            foreach($idParams as $element)
            {
                $chemin = $chemin."/".$params[$element];
            }
        }
        return $serveur.$chemin;
    }
}