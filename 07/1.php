<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../_libs/kint.phar';
include '../../_libs/kint.php';

// Init vars
$inputFile = 'input.txt';
$count = 0;
$root = new directory('');
$working_directory = &$root;

// Load input
$handle = fopen($inputFile, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        $line = trim($line);
        $matches = [];

        // Check command type
        if (preg_match('/^\$ cd (.+)/', $line, $matches) === 1)
        {
            // It's a cd
            $dirName = $matches[1];

            switch ($dirName)
            {
                case '/':
                    $referenceToCurrentDirectory = $referenceToRoot;
                    break;
                case '..':
                    $referenceToCurrentDirectory = $referenceToCurrentDirectory->getParent();
                    break;
                default:
                    // Find requested dir in content
                    $referenceToCurrentDirectory = $referenceToCurrentDirectory->getReferenceToChildren($dirName);
                    break;
            }
            d($line, 'cd', $dirName);
        }
        
        d($line, $root);
    }

    fclose($handle);
}

echo($count);


// ---- Data Structures ------------------------------------------------------- 

class MyFile 
{
    protected $referenceToParent;
    protected $name;
    protected $size;

    public function __construct($name, $size)
    {
        $this->referenceToParent = null;
        $this->name = $name;
        $this->size = $size;
    }

    public function getParent()
    {
        return $this->referenceToParent;
    }

    public function getSize()
    {
        return $this->size;
    }
}

class MyDirectory extends MyFile
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
        $file->referenceToParent = &$this;
        $this->content[$file->name] = $file;
    }

    public function &getReferenceToChildren($name)
    {
        return $this->content[$name];
    }
}