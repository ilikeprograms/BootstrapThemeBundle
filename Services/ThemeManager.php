<?php

// vendor/ILP/BootstrapThemeBundle/Service/ThemeManager.php
namespace ILP\BootstrapThemeBundle\Services;

use Symfony\Component\HttpKernel\KernelInterface,
    Symfony\Bundle\FrameworkBundle\Console\Application,
    Symfony\Bundle\FrameworkBundle\Command\AssetsInstallCommand,
    Symfony\Bundle\AsseticBundle\Command\DumpCommand,
    Symfony\Component\Console\Input\StringInput,
    Symfony\Component\Console\Output\NullOutput,
    Symfony\Component\Finder\Finder,
    
    Doctrine\Bundle\DoctrineBundle\Registry,

    ILP\BootstrapThemeBundle\Command\GenerateThemeCommand;

/**
 * Theme Manager service which allows Themes/Templates to be compiled and Installed/Dumped to the file system.
 * Also provided methods to find the current Template/Theme and the names of all Templates/Themes installed.
 * Uses the details injected in through the bundle configuration.
 * 
 * @see \ILP\BootstrapThemeBundle\Config\services.yml
 * 
 * @author Thomas Coleman <tom@ilikeprograms.com>
 */
class ThemeManager
{
    protected $themeBase;
    protected $templateBase;
    protected $bundle;
    protected $kernel;
    protected $em;
    protected $themeEntity = null;

    /**
     * Constructs the Class an Injects the Dependencies.
     * 
     * @param string $themeBase The base folder where the Themes are stored.
     * @param string $templateBase The base folder where the Templates are stored.
     * @param string $bundle The bundle where the Resources are stored.
     */
    public function __construct($themeBase, $templateBase, $bundle)
    {
        $this->themeBase    = $themeBase;
        $this->templateBase = $templateBase;
        $this->bundle       = $bundle;
    }
    
    /**
     * Injects the Kernel Interface.
     * 
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }
    
    /**
     * Sets the Entity Manager.
     * 
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $entityManager
     */
    public function setEntityManager(Registry $entityManager)
    {
        $this->em = $entityManager;
    }
    
    /**
     * Finds the Theme Base folder absolute path, uses the $themeBase which should
     * be relative from the src folder. So $themeBase = src/MyVendor/MyBundle/Resources/public/css etc.
     * 
     * @return string
     */
    public function getThemeBase()
    {
        return $this->getKernelRootDir() . '/../' . $this->themeBase;
    }
    
    /**
     * Finds the Theme Base folder absolute path, uses the $themeBase which should
     * be relative from the src folder. So $themeBase = src/MyVendor/MyBundle/Resources/views etc.
     * 
     * @return string
     */
    public function getTemplateBase()
    {
        return $this->getKernelRootDir() . '/../' . $this->templateBase;
    }
    
    /**
     * Finds the Current Theme choice. E.g. "Bootstrap".
     * 
     * @return string
     */
    public function getThemeChoice()
    {
        return $this->getThemeEntity()->getThemeChoice();
    }
    
    /**
     * Finds the Current Theme choice. E.g. "Bootstrap".
     * 
     * @return string
     */
    public function getTemplateChoice()
    {
        return $this->getThemeEntity()->getTemplateChoice();
    }

    /**
     * Find the current Theme Path, includes the base path and the current Theme.
     * E.g. src/MyVendor/MyBundle/Resources/public/css/Bootstrap.
     * 
     * @return string
     */
    public function getThemePath()
    {
        return $this->getThemeBase() . '/' . $this->getThemeChoice();
    }
    
    /**
     * Find the current Theme Path, includes the base path and the current Theme.
     * E.g. src/MyVendor/MyBundle/Resources/views/Bootstrap.
     * 
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->getTemplateBase() . '/' . $this->getTemplateChoice();
    }
    
    /**
     * Gets the Configured Bundle web path.
     * 
     * @return string
     */
    public function getConfiguredBundlePath()
    {
        $bundleLower = strtolower($this->bundle);

        return 'bundles/'. $bundleLower;
    }
    
    /**
     * Gets the Current Theme's theme.json file path.
     * 
     * @return string
     */
    public function getCurrentThemeJsonPath()
    {
        return $this->getConfiguredBundlePath() . '/css/' . $this->getThemeChoice() . '/theme.json';
    }

    /**
     * Saves the Theme modifications into a theme.json file in the current theme folder.
     * 
     * @param string $themeData Json stringified variable keys and values, can be used with Less_Parser->modifyVars().
     * 
     * @return int
     */
    public function saveTheme($themeData)
    {
        // Get the current theme folder
        $themePath = $this->getThemePath();
        
        // Save the theme.json file in the current theme folder
        file_put_contents($themePath . '/theme.json', $themeData);
        
        // Now generate the theme using bootstrap.less + theme.json
        return $this->generateTheme();
    }
    
    /**
     * Generates the theme.css file for the current theme and Installs/Dumps the file.
     * 
     * @return int
     */
    public function generateTheme()
    {
        // Get the current theme folder
        $themePath = $this->getThemePath();
        
        // Create a Less parse so we can compile the theme
        $lessParser = new \Less_Parser();

        // If a theme.json file already exist, we can use the details inside to apply
        // custom modifications to the base bootstrap theme
        if (file_exists($themePath . '/theme.json')) {
            $themeOptions = file_get_contents($themePath . '/theme.json');
            $themeOptions = json_decode($themeOptions);
        }

        // Try to parse the source files and modify the variables if theme.json exists
        try {
            $lessParser->parseFile($this->getKernelRootDir() . '/../vendor/ilp/bootstrap-theme-bundle/ILP/BootstrapThemeBundle/Resources/public/Cluckles/build/less/bootstrap.less');
            
            if (isset($themeOptions)) {
                $lessParser->ModifyVars($themeOptions);
            }
        } catch (Exception $ex) {
        }

        // Now get the compiled CSS and save it in the current theme folder
        $compiledCss = $lessParser->getCss();

        // Save the compiled theme file to the current Theme directory
        file_put_contents($themePath . '/theme.css', $compiledCss);
        
        // Now Install/Dump the Theme so the changes to the css files will appear
        return $this->installTheme();
    }
    
    /**
     * Calls the GenerateThemeCommand which Installs and Dumps the theme file for the current theme.
     * 
     * @return int
     */
    public function installTheme()
    {
        // Create a new Application, which we can use to run the GenerateThemeCommand
        // Inject the kernel because thats required
        $app = new Application($this->kernel);
        // Add the Other commands our command will run
        $app->add(new AssetsInstallCommand());
        $app->add(new DumpCommand());

        // Create a new GenerateThemeCommand so we can run it
        $themeCommand = new GenerateThemeCommand();

        // Set the Application, so the Command can run
        $themeCommand->setApplication($app);

        // Find the full path to the web directory
        // Remove app from the kernelRootDir e.g. /path/Corvus/app
        // As /path/Corvus/app/../web doesnt work
        $webDirectory = substr($this->getKernelRootDir(), 0, -3) . 'web';
        
        // The String input sends the path to the web directory to the Command,
        // So that the assets can be installed to the web directory
        $resultCode = $themeCommand->run(new StringInput($webDirectory), new NullOutput());
        
        // return the status code indicating status
        return $resultCode;
    }
    
    /**
     * Find the names of the Folders in the Base Template folder.
     * 
     * @return array
     */
    public function getTemplateFolders()
    {
        return $this->scanFolderNamesInDirectory($this->getTemplateBase());
    }

    /**
     * Find the names of the Folders in the Base Theme folder.
     * 
     * @return array
     */
    public function getThemeFolders()
    {
        return $this->scanFolderNamesInDirectory($this->getThemeBase());
    }

    /**
     * Scans the Given $path to find the sub directory names.
     * 
     * @param string $path The path to a folder to scan.
     * 
     * @return array
     */
    private function scanFolderNamesInDirectory($path)
    {
        $finder = new Finder(); // Create a Finder
        // Find all Directories in the Folder
        $finder->directories()->in($path);

        $folders = array();

        // Find all the Directory names and store them
        foreach (iterator_to_array($finder) as $dir) {
            $directoryName = $dir->getRelativePathname();
            $folders[$directoryName] = $directoryName;
        }

        // Return the array of folder names
        return $folders;
    }

    /**
     * Finds the Theme Entity.
     * 
     * @return \ILP\BootstrapThemeBundle\Entity\Theme
     */
    private function getThemeEntity()
    {
        // Only load the ThemeEntity if we need to
        if ($this->themeEntity === null) {
            $this->themeEntity = $this->em->getRepository('ILPBootstrapThemeBundle:Theme')->Find(1);
        }

        return $this->themeEntity;
    }
    
    /**
     * Gets the Kernel Root Directory.
     * 
     * @return string
     */
    private function getKernelRootDir()
    {
        return $this->kernel->getRootDir();
    }
}