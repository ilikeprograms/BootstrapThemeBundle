<?php

// vendor/ILP/BootstrapThemeBundle/Entity/Theme.php
namespace ILP\BootstrapThemeBundle\Entity;


/**
 * ILP\BootstrapThemeBundle\Entity\Theme
 */
class Theme
{
    /**
     * @var integer $id
     */
    private $id;
    
    /**
     * @var string $template_choice
     */
    private $template_choice;

    /**
     * @var string $theme_choice
     */
    private $theme_choice;

    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set template_choice
     *
     * @param string $templateChoice
     */
    public function setTemplateChoice($templateChoice)
    {
        $this->template_choice = $templateChoice;
    }

    /**
     * Get template_choice
     *
     * @return string
     */
    public function getTemplateChoice()
    {
        return $this->template_choice;
    }

    /**
     * Set theme_choice
     *
     * @param string $themeChoice
     */
    public function setThemeChoice($themeChoice)
    {
        $this->theme_choice = $themeChoice;
    }

    /**
     * Get theme_choice
     *
     * @return string
     */
    public function getThemeChoice()
    {
        return $this->theme_choice;
    }
}