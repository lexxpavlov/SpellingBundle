# Использование SonataAdminBundle

Управление полученными ошибками происходит с помощью SonataAdminBundle. В 
административную панель добавляется группа элементов Орфография - список новых
ошибок, список ранее исправленных ошибок, а также список корректоров - 
пользователей, исправивших ошибки.

В списке ошибок показывается url, в которой была ошибка, а также показывается
ссылка на страницу, которая даёт редактировать текст ресурса. Ссылка на исправление
показывается, если определён роут этой страницы исправления. Например, бандл 
SonataAdminBundle создаёт роут `admin_article_edit` для страницы, имеющей форму
редактирования статьи.

## Добавление количества неисправленных ошибок в меню

Если есть неисправленные ошибки, то в административной панели в боковом меню 
показывается количество ошибок. Это нужно для того, чтобы администраторы всегда
видели эти ошибки.

Бандл SonataAdminBundle не умеет показывать бэджи в меню, поэтому приходится
делать вручную (расширением шаблона `sonata_menu.html.twig`). Чтобы это
расширение заработало, нужно подключить шаблон в настройки бандла 
SonataAdminBundle:
```yaml
# app/consfig/config.yml
sonata_admin:
    templates:
        knp_menu_template: LexxpavlovSpellingBundle:Sonata:sonata_menu.html.twig
```

## Настройка админ-классов

Поиск ссылки исправления ресурса осложнён одной особенностью. Обычно роуты
страниц редактирования принимают id ресурса. Но на самом сайте адрес страницы 
просмотра ресурса не имеет параметра `id`. То есть, если роут имеет вид 
"/article/{id}" или "/article/{id}-{slug}", то найти роут редактирования этой 
статьи легко. Но чаще роуты ресурсов включают заголовок (чаще всего, 
используется поле `slug` - "урлифицированный" заголовок). Поэтому, нужен
инструмент, позволяющий определить `id` ресурса по его другому полю. В этом 
бандле предлагается добавить действие (action) в админ-класс, это действие
называется "find".

Действие "find" нужно добавить во все админ-классы, имеющие текст, который 
может иметь ошибку, и не имеют параметр `id` в их роуте. Для добавления
кастомного действия нужно сделать три вещи: 1)&nbsp;настроить действие в 
админ-классе, 2)&nbsp;добавить метод в `CRUDController` и 3)&nbsp;настроить
админ-класс на правильный контроллер.

### 1) Настройка действия в админ-классе

Добавление действия происходит в методе `configureRoutes` админ-класса:
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

Вместо ручной настройки админ-класса,  можно использовать расширение 
(extension) - оно добавит действие во всех выбранных админ-классах. Нужно
включить это расширение, и настраивать сами админ-классы не нужно:
```yaml
services:
    app.admin.extension.find:
        class: Lexxpavlov\SpellingBundle\Admin\Extension\FindExtension
        tags:
            - { name: sonata.admin.extension, target: app.admin.article }
            - { name: sonata.admin.extension, target: app.admin.news }
```

### 2) Добавление метода в `CRUDController`

Если вы не используете кастомный контроллер у админ-класса, то используйте
контроллер `LexxpavlovSpellingBundle:CRUDController`. Если кастомный контроллер
вы используете, то добавьте в него трейт (trait) `FindTrait`:
```php
use Lexxpavlov\SpellingBundle\Controller\FindTrait;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
 
class CRUDController extends Controller
{
    use FindTrait;
    
    // ...
}
```

### 3) Настройка контроллера админ-класса

Осталось настроить админ-класс на использование нужного контроллера. Это можно 
сделать в настройках сервиса, третьим аргументом:
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
