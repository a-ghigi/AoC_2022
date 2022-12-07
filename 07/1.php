<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../_libs/kint.phar';
include '../../_libs/kint.php';

// Init vars
$input_file = 'input.txt';
$count = 0;

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        // Do something
        
        d($line);
    }

    fclose($handle);
}

echo($count);


// ---- Data Structures ------------------------------------------------------- 

class file 
{
    protected $parent;
    protected $name;
    protected $size;

    public function __construct($name, $size)
    {
        $this->parent = null;
        $this->name = $name;
        $this->size = $size;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getSize()
    {
        return $this->size;
    }
}

class directory extends file
{
    protected $content;

    public function __construct($name)
    {
        parent::__construct($name, null);
        $this->content = [];
    }

    public function getSize()
    {
        $totalSize = 0;

        foreach ($this->content as $fileOrDir)
        {
            $size = $fileOrDir->getSize();
            $totalSize += $size;
        }

        return $totalSize;
    }

    public function insert($file)
    {
        $file->parent = &$this;
        $this->content[] = $file;
    }
}