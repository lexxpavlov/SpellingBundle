# Using SonataAdminBundle

Management of obtained errors is done using SonataAdminBundle. In the admin 
panel is added the group of elements `Spelling` - the list of new errors, a 
list of previously fixed errors and a list of correctors - users fixed mistakes.

The error list shows the url, which was an error, and shows a link to a page
that allows to edit the resource text. Link to correction displayed if fixing
route is defined. For example, a bundle SonataAdminBundle creates a route 
`admin_article_edit` with article editing form.

## Adding the number of uncorrected errors in the menu

If there are uncorrected errors, it displays the number of errors in the
administrative panel in the sidebar. This is to ensure that administrators have
always seen these errors.

Bundle SonataAdminBundle can not display badges on the menu, so you have to do
it manually (by extending `sonata_menu.html.twig` template). For this to work,
you need to set the template in SonataAdminBundle settings:
```yaml
# app/consfig/config.yml
sonata_admin:
    templates:
        knp_menu_template: LexxpavlovSpellingBundle:Sonata:sonata_menu.html.twig
```

## Setting the admin classes

Search correction resource reference is complicated by one thing. Usually 
editing routes accept the resource id. But in the website the resource address
of the page has no `id` parameter. That is, if route is like "/article/{id}" or
"/article/{id}-{slug}", then find of editing route of this article easily. But 
more often resource routes include the title (most often used `slug` field - 
"urlified" title). Therefore, we need a tool that allows you to define the 
resource id from another field. This bundle is proposed to add an action in the
admin class, this action is called "find".

You need to add action "find" to all admin-classes, which text can be a mistake,
and have no `id` parameter in their route. To add of a custom action is need to 
do three things: 1)&nbsp;to set up the action in the admin class, 2)&nbsp;add a
method to CRUDController and 3)&nbsp;set up the admin class to the controller
with this method.

### 1) Setting the action in the admin class

Adding an action takes place in the method `configureRoutes` of admin class:
```php
use Sonata\AdminBundle\Route\RouteCollection;
 
class ArticleAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('find', $this->getRouterIdParameter() . '/find');
    }
    
    // ...
}
```

Instead of manually changing of admin-class, you can use the extension -  it 
will add action in all selected admin classes. It is necessary to include this
extension and configure themselves admin classes do not need:
```yaml
services:
    app.admin.extension.find:
        class: Lexxpavlov\SpellingBundle\Admin\Extension\FindExtension
        tags:
            - { name: sonata.admin.extension, target: app.admin.article }
            - { name: sonata.admin.extension, target: app.admin.news }
```

### 2) Adding a method to the `CRUDController`

If you are not using a custom controller in admin class, use controller 
`LexxpavlovSpellingBundle:CRUDController`. If you are using a custom controller,
then add the trait `FindTrait` in it:
```php
use Lexxpavlov\SpellingBundle\Controller\FindTrait;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
 
class CRUDController extends Controller
{
    use FindTrait;
    
    // ...
}
```

### 3) Setting the controller of admin class

It remains to configure the admin class to use the desired controller. This can 
be done in the service's settings, the third argument:
```yaml
services:
    app.admin.article:
        class: AppBundle\Admin\ArticleAdmin
        tags:
            - { name: sonata.admin, manager_type: orm, group: "Content", label: "Articles", label_catalogue: "messages" }
        arguments:
            - ~
            - AppBundle\Entity\Article
            - LexxpavlovSpellingBundle:CRUDController
```
