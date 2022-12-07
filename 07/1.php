<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../_libs/kint.phar';
include '../../_libs/kint.php';

// Init vars
$input_file = 'input.txt';
$count = 0;
$root = new directory('');
$working_directory = &$root;

// Load input
$handle = fopen($input_file, "r");
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
            $dir_name = $matches[1];

            switch ($dir_name)
            {
                case '/':
                    $working_directory = &$root;
                    break;
                case '..':
                    $working_directory = $working_directory->getParent();
                    break;
                default:
                    // Find requested dir in content
                    $working_directory = $working_directory->get_children($dir_name);
                    break;
            }
        }
        
        d($line, $root);
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
        $this->content[$file->name] = $file;
    }

    public function &get_children($name)
    {
        return $this->content[$name];
    }
}