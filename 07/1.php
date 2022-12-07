<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../_libs/kint.phar';
include '../../_libs/kint.php';

// Init vars
$inputFile = 'input.txt';
$root = new Dir('');
$referenceToRoot = &$root;
$referenceToCurrentDir = $referenceToRoot;
d($root, $referenceToRoot, $referenceToCurrentDir);

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
                                $directory = new Dir($dirName);
                                $referenceToCurrentDir->insert($directory);
                                d($line, 'insert dir', $dirName);
                                break;
                            default:
                                // It's a file
                                $size = intval($matches[1]);
                                $fileName = $matches[2];
                                $file = new File($fileName, $size);
                                $referenceToCurrentDir->insert($file);
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
                    $referenceToCurrentDir = $referenceToRoot;
                    break;
                case '..':
                    $referenceToCurrentDir = $referenceToCurrentDir->getParent();
                    break;
                default:
                    // Find requested dir in content
                    $referenceToCurrentDir = $referenceToCurrentDir->getReferenceToChildren($dirName);
                    break;
            }
            d($line, 'cd', $dirName);
        }
    }

    fclose($handle);
}

d($referenceToRoot, $referenceToCurrentDir);

// ---- Data Structures ------------------------------------------------------- 

class File 
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

class Dir extends File
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