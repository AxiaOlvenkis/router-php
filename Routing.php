<?php
/**
 * Created by PhpStorm.
 * User: Benoît
 * Date: 14/04/2016
 * Time: 21:34
 */

namespace Services;


use Controller\ControllerFactory;
use Symfony\Component\Yaml\Parser;

class Routing
{
    private $listRoutes = [];
    private $path;

    public function __construct()
    {
        $this->path = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : '/';
        $this->listRoutes = $this->parseRoute();
        $rep = null;
    }

    private function parseRoute()
    {
        $data = file_get_contents('Config/router.yml');
        $parser = new Parser();
        $routes = $parser->parse($data);
        return $routes;
    }

    private function find(){
        foreach($this->listRoutes as $detail)
        {
            $chemin = $detail['chemin'];
            $explodeURL = explode('/', $this->path);
            $params = $this->formatParam($chemin);
            if(count($explodeURL) > 2)
            {
                $chemin = $this->correspondanceParams($params, $explodeURL, $chemin);
            }

            if($chemin == $this->path || $chemin.'/' == $this->path)
            {
                return $detail;
            }
        }
        return null;
    }

    private function action($nom, $action, $params = '')
    {
        $twig = new Twig();
        return ControllerFactory::createController($nom, $twig)->$action($params);
    }

    public function routing()
    {
        $maRoute = $this->find();
        if($maRoute == null)
        {
            throw new RouterException('No matching routes',404);
        }

        $action = $maRoute['action']."Action";
        $nom = $maRoute['controller'];
        $chemin = $maRoute['chemin'];
        $tabParam = $this->formatParam($chemin);

        $params = $this->correspondanceTabParams($tabParam);

        return $this->action($nom, $action, $params);
    }

    public function findByRoute($route)
    {
        if(!$this->listRoutes[$route])
        {
            throw new RouterException('No matching routes');
        }
        else
        {
            return $this->listRoutes[$route];
        }
    }

    public function formatParam($chemin)
    {
        $tabParam = array();
        $tabTemp = explode("/{", $chemin);
        foreach($tabTemp as $element)
        {
            if(strpos($element, "}"))
            {
                $element = rtrim($element, '}');
                $tabParam[$element] = $element;
            }
        }
        return $tabParam;
    }

    public function formatChemin($chemin)
    {
        $pos = strpos($chemin,'{');
        $chemin = substr($chemin,0,$pos);
        $chemin = rtrim($chemin, '/');
        return $chemin;
    }

    private function correspondanceParams($tabId, $tabURL, $chemin)
    {
        $i = 0;
        $c = array('{','}');
        $explodeChemin = explode('/', $chemin);
        foreach($explodeChemin as $expl)
        {
            if(strpos($expl,'{') === 0)
            {
                if(count($tabURL) != count($explodeChemin))
                {
                    return "";
                }
                $valSearch = $tabURL[$i];
                $expl = str_replace($c, '', $expl);
                $chemin = str_replace($tabId[$expl],$valSearch,$chemin);
            }
            $i++;
        }
        $chemin = str_replace($c, '', $chemin);
        return $chemin;
    }

    private function correspondanceTabParams($tabID)
    {
        $explodeURL = explode('/', $this->path);
        $cptURL = count($explodeURL);
        $cptID = count($tabID);
        $nbArg = $cptURL - $cptID;
        foreach($tabID as $id)
        {
            $tabID[$id] = $explodeURL[$nbArg];
            $nbArg++;
        }

        return $tabID;
    }

    public function gestionErreur($code)
    {
        if($code == 403 )
        {
            $code = "deniedAction";
        }
        else
        {
            $code = "notFoundAction";
        }
        $twig = new Twig();
        return ControllerFactory::createController('Erreur', $twig)->$code();
    }
}