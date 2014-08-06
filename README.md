Bootstrap Theme Bundle
======================

Boostrap Theme Bundle provided the ability to manage themes/templates of Bootstrap based Symfony2 projects.
You can integrate the live editor to allow your themes to be edited live and the changes to a theme saved.

There is also a service which allows you to compile the changes to the theme and get information about the current theme.

For example usage see (Corvus)[https://github.com/ilikeprograms/corvus]

This project makes use of (Cluckles)[https://github.com/ilikeprograms/Cluckles] for the Live editing features.

## Getting Started

To get started using BootstrapThemeBundle, add the project and its dependencies to your app's composer.json file.

```json
{
    "require": {
        "ilp/bootstrap-theme-bundle": "~0.1.0",
        "oyejorge/less.php": "~1.5"
    }
}
```

Then enable the Bundle in your AppKernel

```php
$bundles = array(
// ... Other Dependencies
    new ILP\BootstrapThemeBundle\ILPBootstrapThemeBundle()
);
```

Then provide the folders which hold your theme/template folders which you want the bundle to manage (config.yml),
aswell as the Bundle which holds the Resources (in the format below, Vendor and Bundle Name without "Bundle")

```yml
ilp_bootstrap_theme:
    theme_base: 'src/Corvus/FrontendBundle/Resources/public/css'
    template_base: 'src/Corvus/FrontendBundle/Resources/views'
    bundle: CorvusFrontend # Not case sensitive (Corvus = Vendor, Frontend = Bundle name)
```

These folders expect that `public/css` and `Resources/views` will have folders with the name of the theme/templates.
The `Resources/views/*` folders will be expected to hold the twig templates for the current theme.
The `public/css` folder will be expected to hold folders with the theme files for the current theme.

## Using Themes

By default if you have a folder in the `public/css` folder, it will be treated as a Theme directory, if it has no theme.css file, one will be automatically created for it.
This theme.css file will have the generic base Twitter Bootstrap styling.

To change this Theme, the BoostrapThemeBundle's editor can be used, there is an `themeEditor.html.twig` view which can be included in another template to provide the editor view.
This view can then be interacted with to customise the current theme.

Then you need to include the base `bootstrap.less` so that it can be modified live

```twig
# theme_editor.bootstrap path returns the path to the Custom bootstrap.less file included with Cluckles
<link type="text/css" href="{{ asset('theme_editor.bootstrap_path') }}" rel="stylesheet/less" />
```

Finally you need to include the theme-editor js and initialise an instance, this will allow you to edit and save the changes

```javascript
{% include 'ILPBootstrapThemeBundle:Default:editor-js.html.twig' %}

<script>
    var themeEditor = new ThemeEditor(less, {
        theme: {
            // theme_manager.getCurrentThemeJsonPath returns the web path to the current theme's theme.json file
            src: '{{ asset(theme_manager.getCurrentThemeJsonPath) }}'
        },
        export: {
            // Attach Export buttons to this element
            target: '#download-panel-footer',

            // Provide Export buttons for Css and JSON Formats
            css: {},
            json: {},

            save: {
                // Save Css and Json
                formats: ['css', 'json'],
                append: "#download-panel-footer",

                // Send the Modifications to here
                url: '{{ path('ilp_bootstrap_theme_EditorSaveModifications') }}',

                // Optional Callback
                callback: function () {
                    alert('Theme Changes have been Saved');
                }
            }
        }
    });
</script>
```

A download/save link will be in the `themeEditor.html.twig` view which will enable the changes to be saved. They will be posted to the `theme-editor/save` url (relative to the project url) and the `theme_manager`
service will then compile and save the changes.