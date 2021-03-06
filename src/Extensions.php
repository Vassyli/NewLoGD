<?php

namespace NewLoGD;

use Database\{CharacterScene, Scene};

class Extensions {
    protected $extensions_loaded = [];
    protected $extensions_metadata = NULL;
    
    public function __construct(string $extensions) {
        $this->extensions_loaded = require $extensions;
    }
    
    public function addToAutoloader($autoloader) {
        foreach($this->extensions_loaded as $extension) {
            $namespace = $this->extensionToNamespace($extension);
            $path = $this->extensionToPath($extension);
            $autoloader->addPsr4("\\Extensions\\Commentary\\", "extensions/Commentary");
        }
    }
    
    public function addToAnnotationSources(array $sources) {
        foreach($this->extensions_loaded as $extension) {
            array_push($sources, $this->extensionToAnnotationSource($extension));
        }
        
        return $sources;
    }
    
    public function addRoutes(Application $app) {
        $this->loadMetaData();
        foreach($this->extensions_metadata as $extension => $meta) {
            if(isset($meta["routes"])) {
                foreach($meta["routes"] as $route) {
                    $method = $route[0];
                    $path = "/ext/".$extension.$route[1];
                    $controller = str_replace("#", "\\Extensions\\".$extension."\\Http\\Controllers\\", $route[2]);
                    $app->addRoute($method, $path, $controller);
                }
            }
        }
    }
    
    public function addMiddleware(Application $app) {
        $this->loadMetaData();
        foreach($this->extensions_metadata as $extension => $meta) {
            if(isset($meta["middleware"])) {
                foreach($meta["middleware"] as $middleware) {
                    $app->addMiddleware($middleware);
                }
            }
        }
    }
    
    public function extendScene(CharacterScene $c_scene, Scene $scene) {
        $extensions = $scene->getExtensions();
        foreach($extensions as $extension) {
            $extensionname = $extension->getExtension();
            $classname = $this->extensionToSceneClass($extensionname);
            // $classname is a Class implementing changes to the scene.
            $m_scene = new $classname($c_scene, $extension);
            $m_scene->change();
        }
    }
    
    protected function extensionToNamespace(string $extension) : string {
        return "\\Extensions\\" . $extension . "\\";
    }
    
    protected function extensionToPath(string $extension) : string {
        return __DIR__ . "/../extensions/" .$extension;
    }
    
    protected function extensionToAnnotationSource(string $extension) : string {
        return $this->extensionToPath($extension) . "/database";
    }
    
    protected function extensionToSceneClass(string $extension) : string {
        return $this->extensionToNamespace($extension) . "Scene";
    }
    
    protected function loadMetaData() {
        if($this->extensions_metadata === NULL) {
            foreach($this->extensions_loaded as $extension) {
                $this->extensions_metadata[$extension] = require $this->extensionToPath($extension) . "/Meta.php";
            }
        }
    }
}