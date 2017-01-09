# Installation of LexxpavlovSpellingBundle

## Step 1 - Download

Require the bundle with Composer:

```bash
$ composer require lexxpavlov/spellingbundle
```
Composer will install the bundle to your project's 
`/vendor/lexxpavlov/spellingbundle` directory.

## Step 2 - Enable the bundle

Enable the bundle in the kernel:
```php
<?php
// app/AppKernel.php
 
public function registerBundles()
{
    $bundles = array(
        // ...
        new Lexxpavlov\SpellingBundle\LexxpavlovSpellingBundle(),
        // ...
    );
}
```

## Step 3 - Create your entities classes

Need to create two classes for entities `Spelling` and `SpellingSecurity`.

Entity Spelling saves information of pointed out error to database. Since the 
entity of Spelling should be linked to the entity of the user, to make this 
link in this bundle is not possible to establish this relationship, you should
do it. At the same time it creates a field for the primary key (for those who 
use is not `id` field as the primary key).

### 3.1. Create entity classes with annotation

Example of class for Spelling entity: 
```php
<?php
namespace AppBundle\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use Lexxpavlov\SpellingBundle\Entity\Spelling as BaseSpelling;
 
/**
 * @ORM\Table(name="spelling")
 * @ORM\Entity
 */
class Spelling extends BaseSpelling
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     */
    protected $creator;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="corrector_id", referencedColumnName="id")
     */
    protected $corrector;
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
```
Getters and setters of `$creator` and `$corrector` fields is exist already in
base class. If you need to use specific User class in this methods (not 
UserInterface), then you want to override these methods and specify the desired
class in PHPDoc.

Example of class for SpellingSecurity entity: 
```php
<?php
namespace AppBundle\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use Lexxpavlov\SpellingBundle\Entity\SpellingSecurity as BaseSpellingSecurity;
 
/**
 * @ORM\Table(name="spelling_security")
 * @ORM\Entity
 */
class SpellingSecurity extends BaseSpellingSecurity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
 
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
```

### 3.2. Create entity classes with Yaml

If you are using Yaml to create entities, the good news for you - in the base
class already have fields and getters/setters, you must create the field(s) 
of the primary key.

Example of class for Spelling entity: 
```php
<?php
namespace AppBundle\Entity;
 
use Lexxpavlov\SpellingBundle\Entity\Spelling as BaseSpelling;
 
class Spelling extends BaseSpelling
{
    /**
     * @var integer
     */
    protected $id;
 
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
```
Example of yaml-config of Spelling entity:
```yaml
# src/AppBundle/Resources/config/doctrine/Spelling.orm.yml
AppBundle\Entity\Spelling:
    type: entity
    table: spelling
    id:
        id:
            type: integer
            generator:
                strategy: AUTO
    manyToOne:
        creator:
            targetEntity: AppBundle\Entity\User
            joinColumn:
                name: creator_id
                referencedColumnName: id
        corrector:
            targetEntity: AppBundle\Entity\User
            joinColumn:
                name: corrector_id
                referencedColumnName: id
```

Example of class for SpellingSecurity entity: 
```php
<?php
namespace AppBundle\Entity;
 
use Lexxpavlov\SpellingBundle\Entity\SpellingSecurity as BaseSpellingSecurity;
 
class SpellingSecurity extends BaseSpellingSecurity
{
    /**
     * @var integer
     */
    protected $id;
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
```
Example of yaml-config of SpellingSecurity entity:
```yaml
# src/AppBundle/Resources/config/doctrine/SpellingSecurity.orm.yml
AppBundle\Entity\SpellingSecurity:
    type: entity
    table: spelling_security
    id:
        id:
            type: integer
            generator:
                strategy: AUTO
```

## Step 4 - Import routing files

Import routing files from LexxpavlovSpellingBundle:
```yaml
# app/config/routing.yml
lexxpavlov_spelling:
    resource: "@LexxpavlovSpellingBundle/Resources/config/routing/all.yml"
```

If you are not using the bundle SonataAdminBundle, then import only 
`@LexxpavlovSpellingBundle/Resources/config/routing/routing.yml`.

## Step 5 - Configure the bundle

If you have created entities as described in Step 3, then you do not have the
required parameters in config. Otherwise, specify the full class of entities. 
You can also specify the field of user entity, which you call users (for 
example, the field `name` or `email`).

```yaml
lexxpavlov_spelling:
    entity_class:         AppBundle\Entity\Spelling
    user_field:           username
    security:
        entity_class:     AppBundle\Entity\SpellingSecurity
```

Security settings can be found in the relevant sections of the documentation:
[User restriction](auth-check.md) and [IP-address restriction](flood-control.md).

Information of full configuration settings, refer to 
[Full configuration of bundle](full-configuration.md).
 
## Step 6 - Update your database schema

Run the following command in console:
```bash
$ php bin/console doctrine:schema:update --dump-sql
```
and run the shown commands in your database, or ask Doctrine to update your db:
```bash
$ php bin/console doctrine:schema:update --force
```

> **Note.** Before you run the command `--force`, it is recommended to check, 
> how queries will perform by Doctrine.

## Step 7 - Setup of template

### 7.1. Add javascript-scripts and css-styles

Required to add a link to the scripts and stylesheet file 
`/app/Resources/views/base.html.twig`. Style file needed if you are using the
error window from the bundle (see section 7.2).

If you use `assetic`, then add the scripts to the tag` javascripts`, and styles
into the tag `stylesheets`: 
```twig
{% block stylesheets %}
{% stylesheets filter='cssrewrite' 
    ...
    '@LexxpavlovSpellingBundle/Resources/public/css/spelling.css'
%}
    <link rel="stylesheet" href="{{ asset_url }}" />
{% endstylesheets %}
{% endblock %}
...
{% block javascripts %}
{% javascripts
    ...
    '@LexxpavlovSpellingBundle/Resources/public/js/*'
%}
<script src="{{ asset_url }}"></script>
{% endjavascripts %}
{% endblock %}
```

If you do not use `assetic`, add the following tag before the closing tag 
`</body>`. Also add a link to a file css-styles, add it to the `stylesheets`
block inside of the tag `<head>`:
```twig
{% block stylesheets %}
...
<link rel="stylesheet" href="{{ asset('bundles/lexxpavlovspelling/css/spelling.css') }}">
{% endblock %}
...
{% block javascripts %}
...
<script src="{{ asset('bundles/lexxpavlovspelling/js/rangy-core.js') }}></script>
<script src="{{ asset('bundles/lexxpavlovspelling/js/spelling.js') }}></script>
{% endblock %}
```

### 7.2. Setting the error window

The next step is to add a window that appears when user press `Ctrl+Enter`. You
may use window code from bundle, but you can change it, or you can get your 
solution. For details, see [Setting the error window](error-window.md).

To add a window, add this code before the closing tag `</body>`:
```twig
{% include 'LexxpavlovSpellingBundle::spelling.html.twig' %}
```

### 7.3. Setting the logo button (optional)

You can display information to users, that they may point out mistake in the 
text. You can display this in the footer, and/or in the title of the article.

The bundle has a logo button showing information about the possibility of an 
error pointing. For details, see [Setting the logo button](logo-button.md).

Add the button (88x31px) with the text "Error? Ctrl+Enter" can be as follows:
```twig
<a href="{{ path('spelling_info') }}" title="How to fix the error?">
    {% include 'LexxpavlovSpellingBundle:Logo:two.html.twig' %}
</a>
```
Add the button (120x76px) with the text "Error? Select the error with mouse and
press Ctrl+Enter" can be as follows:
```twig
<a href="{{ path('spelling_info') }}" title="How to fix the error?">
    {% include 'LexxpavlovSpellingBundle:Logo:three.html.twig' with {width: '120px', height: '76px'} %}
</a>
```
> **Note** Page with route "spelling_info" is not available in the bundle, if
> you need it, add it yourself.

## Step 8. Setting the SonataAdminBundle (optional)

For details, see [Using SonataAdminBundle](sonata-admin.md)

## Next steps

Basic installation and configuration of  bundle is complete. Then you can see
description of the [technology of bundle work](technology.md), read the [full
configuration bundle](full-configuration.md), or see the [content of the 
documentation](index.md).
