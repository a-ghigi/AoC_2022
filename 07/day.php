<?php

// Init vars
$inputFile = 'input.txt';
$fileSystem = new FileSystem();
$result1 = 0;
$dirSizes = [];

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
            {
                // Save input position
                $inputPos = ftell($handle);

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
                                // New command, directory content ended
                                $stillInDirListing = false;

                                // Rewind input
                                fseek($handle, $inputPos);
                                break;
                            case 'dir':
                                // It's a directory
                                $dirName = $matches[2];
                                $fileSystem->mkdir($dirName);
                                break;
                            default:
                                // It's a file
                                $size = intval($matches[1]);
                                $fileName = $matches[2];
                                $fileSystem->write($fileName, $size);
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
            }
            while ($stillInDirListing);
        }
        elseif (preg_match('/^\$ cd (.+)/', $line, $matches) === 1)
        {
            // It's a cd
            $dirName = $matches[1];
            $fileSystem->cd($dirName);
        }
    }

    fclose($handle);
}

$fileSystem->collectDataForResults(100000);

// Part 1
echo('Part 1: ' . $result1);


// Part2

// Sort dir sizes in ascending order
sort($dirSizes);

$diskSize = 70000000;
$diskUsage = $dirSizes[count($dirSizes) - 1];     // Root size, the biggest one
$diskFree = $diskSize - $diskUsage;

$updateSize = 30000000;
$spaceNeeded = $updateSize - $diskFree;

// Find first dir bigger than spaceNeeded
foreach ($dirSizes as $size)
{
    if ($size >= $spaceNeeded)
    {
        echo('<br />' . PHP_EOL . 'Part 2: ' . $size);
        die();
    }
}


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

    public function getReferenceToParent()
    {
        return $this->referenceToParent;
    }

    public function getName()
    {
        if ($this->name)
        {
            $name = $this->name;
        }
        else
        {
            $name = '\\';
        }

        return $name;
    }

    public function getSize($limit = 0)
    {
        return $this->size;
    }

    public function dump($level = 0)
    {
        echo(str_pad('', $level * 2));
        echo('- ');
        echo($this->getName());
        echo(' (file, size=' . $this->size . ')' . PHP_EOL);
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

    public function getSize($limit = 0)
    {
        global $result1;
        global $dirSizes;

        $totalSize = 0;

        foreach ($this->content as $fileOrDir)
        {
            $size = $fileOrDir->getSize($limit);
            $totalSize += $size;
        }

        if ($this->name AND ($limit === null OR $totalSize <= $limit))
        {
            $result1 += $totalSize;
        }

        $dirSizes[] = $totalSize;

        return $totalSize;
    }

    public function insert($fileOrDir)
    {
        $fileOrDir->referenceToParent = &$this;
        $this->content[$fileOrDir->name] = $fileOrDir;
    }

    public function &getReferenceToChildren($name)
    {
        return $this->content[$name];
    }

    public function dump($level = 0)
    {
        echo(str_pad('', $level * 2));
        echo('- ');
        echo($this->getName());
        echo(' (dir)' . PHP_EOL);

        foreach($this->content as $fileOrDir)
        {
            $fileOrDir->dump($level + 1);
        }
    }
}

class FileSystem
{
    protected $referenceToRoot;
    protected $referenceToCurrentDir;

    public function __construct()
    {
        $root = new Dir('');
        $this->referenceToRoot = &$root;
        $this->referenceToCurrentDir = $this->referenceToRoot;
    }

    public function cd($dirName)
    {
        switch ($dirName)
        {
            case '/':
                $this->referenceToCurrentDir = $this->referenceToRoot;
                break;
            case '..':
                $this->referenceToCurrentDir = $this->referenceToCurrentDir->getReferenceToParent();
                break;
            default:
                // Find requested dir in content
                $this->referenceToCurrentDir = $this->referenceToCurrentDir->getReferenceToChildren($dirName);
                break;
        }
    }

    public function mkdir($dirName)
    {
        $directory = new Dir($dirName);
        $this->referenceToCurrentDir->insert($directory);
    }

    public function write($fileName, $size)
    {
        $file = new File($fileName, $size);
        $this->referenceToCurrentDir->insert($file);
    }

    public function dump()
    {
        echo('<pre>' . PHP_EOL);
        $this->referenceToRoot->dump();
        echo('</pre>' . PHP_EOL);
    }
    public function collectDataForResults($limit)
    

    {
        $this->referenceToRoot->getSize($limit);
    }
}