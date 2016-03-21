<?php

namespace NewLoGD;

class Extensions {
    protected $extensions_loaded = [];
    
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
    
    protected function extensionToNamespace(string $extension) : string {
        return "\\Extensions\\" . $extension . "\\";
    }
    
    protected function extensionToPath(string $extension) : string {
        return __DIR__ . "/../extensions/" .$extension;
    }
    
    protected function extensionToAnnotationSource(string $extension) : string {
        return $this->extensionToPath($extension) . "/database";
    }
}