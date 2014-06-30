<?php

// vendor/ILP/BootstrapThemeBundle/Form/Type/TemplatingType.php
namespace ILP\BootstrapThemeBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    
    ILP\BootstrapThemeBundle\Services\ThemeManager;

/**
 * Form type which provides a form which can be used to set the current Template/Theme choice.
 * Injects the \ThemeManager service to access the Template/Theme choices.
 * 
 * @see \ILP\BootstrapThemeBundle\Services\ThemeManager
 * 
 * @author Thomas Coleman <tom@ilikeprograms.com>
 */
class TemplatingType extends AbstractType
{
    protected $themeManager;

    /**
     * Constructs the Class an Injects the Dependencies.
     * 
     * @param \ILP\BootstrapThemeBundle\Services\ThemeManager $themeManager
     */
    public function __construct(ThemeManager $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('template_choice', 'choice', array(
            'label' => 'Template Choice',
            'choices'   => $this->themeManager->getTemplateFolders(),
            'label_attr' => array(
                'class' => 'fontBold',
            ),
        ));
        $builder->add('theme_choice', 'choice', array(
            'label' => 'Theme Choice',
            'choices'   => $this->themeManager->getThemeFolders(),
            'label_attr' => array(
                'class' => 'fontBold',
            ),
        ));
        $builder->add('generate_button', 'submit');
    }
    
    /**
     * @inheritDoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ILP\BootstrapThemeBundle\Entity\Theme',
        ));
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'templating';
    }
}
