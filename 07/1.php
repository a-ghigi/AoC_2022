<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../_libs/kint.phar';
include '../../_libs/kint.php';

// Init vars
$inputFile = 'input.txt';
$root = new MyDirectory('');
$referenceToRoot = &$root;
$referenceToCurrentDirectory = $referenceToRoot;
d($root, $referenceToRoot, $referenceToCurrentDirectory);

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
        if (preg_match('/^\$ ls$/', $line, $matches) === 1)
        {
            // It's a ls

            // Start reading directory listing
            $stillInDirListing = true;
            do
                // Read an input line
                if (($line = fgets($handle)) !== null)
                {
                    $line = trim($line);

                    // Parse input line
                    if (preg_match('/^([^ ]+) (.+)/', $line, $matches) === 1)
                    {
                        switch ($matches[1])
                        {
                            case '$':
                                $stillInDirListing = false;
                                break;
                            case 'dir':
                                $dirName = $matches[2];
                                $directory = new MyDirectory($dirName);
                                $referenceToCurrentDirectory->insert($directory);
                                d($line, 'insert dir', $dirName);
                                break;
                            default:
                                // It's a file
                                $size = intval($matches[1]);
                                $fileName = $matches[2];
                                $file = new MyFile($fileName, $size);
                                $referenceToCurrentDirectory->insert($file);
                                d($line, 'insert file', $fileName, $size);
                                break;
                        }
                    }
                    else
                    {
                        // End of input
                        $stillInDirListing = false;
                    }
                }
                else
                {
                    // End of input
                    $stillInDirListing = false;
                }
            while ($stillInDirListing);
        }

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
    }

    fclose($handle);
}

d($referenceToRoot, $referenceToCurrentDirectory);

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