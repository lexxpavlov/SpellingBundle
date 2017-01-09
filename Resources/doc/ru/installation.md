# Инсталляция LexxpavlovSpellingBundle

## Шаг 1 - Загрузка

Установить с помощью Composer:

```bash
$ composer require lexxpavlov/spellingbundle
```
Composer установит бандл в папку `/vendor/lexxpavlov/spellingbundle`.

## Шаг 2 - Включение бандла

Включить бандл в kernel:
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

## Шаг 3 - Создание сущностей

Создать два класса сущностей `Spelling` и `SpellingSecurity`.

Сущность Spelling сохраняет в базу данных информацию об созданной ошибке, 
указанной пользователем. Так как сущность Spelling должна быть связана с 
сущностью пользователя, то сделать эту связь в текущем бандле невозможно, эту
связь установить должны вы. Заодно создаётся поле для первичного ключа (для
тех, кто использует в качестве первичного ключа не поле id).

### 3.1. Создание сущностей с помощью аннотаций

Пример создания класса сущности Spelling: 
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
Геттеры и сеттеры полей `$creator` и `$corrector` уже существуют в базовом 
классе. Если нужно, чтобы методы использовали конкретный класс User, а не 
UserInterface, то требуется переопределить эти методы и указать нужный класс в
PHPDoc.

Пример создания класса сущности SpellingSecurity: 
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

### 3.2. Создание сущностей с помощью Yaml

Если вы используете Yaml для создания сущностей, то для вас хорошая новость - в
базовом классе уже есть поля и геттеры/сеттеры создателя и корректора, остаётся
только создать поле (поля) первичного ключа. 

Пример создания класса сущности Spelling: 
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
Пример yaml-конфига создания сущности Spelling:
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

Пример создания класса сущности SpellingSecurity: 
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
Пример yaml-конфига создания сущности SpellingSecurity:
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

## Шаг 4 - Настройка роутинга

Импортировать настройки роутинга из бандла:
```yaml
# app/config/routing.yml
lexxpavlov_spelling:
    resource: "@LexxpavlovSpellingBundle/Resources/config/routing/all.yml"
```

Если вы не используете бандл SonataAdminBundle, то импортируйте только 
`@LexxpavlovSpellingBundle/Resources/config/routing/routing.yml`.

## Шаг 5 - Настройка бандла

Если вы создали сущности, как описано в шаге 3, то для вас нет обязательных
параметров конфига. Иначе укажите полный класс сущностей. Также вы можете 
указать, каким полем сущности пользователя вы называете пользователей 
(например, поле `name`).
```yaml
lexxpavlov_spelling:
    entity_class:         AppBundle\Entity\Spelling
    user_field:           username
    security:
        entity_class:     AppBundle\Entity\SpellingSecurity
```

Настройки безопасности вы можете прочитать в соответствующих разделах 
документации: 
[Ограничение пользователей](auth-check.md) и 
[Ограничение IP-адресов](flood-control.md).

Полная настройка конфигурации указана в разделе 
[Полная настройка бандла](full-configuration.md).
 
## Шаг 6 - Обновление базы данных

Запустить в консоли команду:
```bash
$ php bin/console doctrine:schema:update --dump-sql
```
и запустите показанные команды в вашей СУБД, или попросите Doctrine изменить 
вашу БД:
```bash
$ php bin/console doctrine:schema:update --force
```

> **Заметка.** Прежде чем выполнить команду `--force`, рекомендуется проверить,
> какие запросы Doctrine планирует выполнять.

## Шаг 7 - Настройка шаблона

### 7.1. Добавление javascript-скриптов и css-стилей

Требуется добавить ссылку на скрипты и на файл стилей в файле 
`/app/Resources/views/base.html.twig`. Файл стилей нужен, если вы используете
окно ошибок из бандла (см. раздел 7.2).
 
Если вы используете `assetic`, то добавьте скрипты в тег `javascripts`, а стили
в тег `stylesheets`:
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

Если вы не используете `assetic`, то добавьте следующие теги перед закрывающим
тегом `</body>`. Также добавьте ссылку на файл css-стилей. Добавьте его в блоке
`stylesheets` внутри тега `<head>`:
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

### 7.2. Настройка окна ошибки

Следующим шагом является добавление окна, которое появляется при нажатии клавиш
`Ctrl+Enter`. Вы можете использовать готовый код окна из бандла, можете 
изменить его, а можете взять своё решение. Подробнее смотрите раздел [Настройка 
окна ошибки](error-window.md).

Для добавления окна добавьте этот код перед закрывающим тегом `</body>`:
```twig
{% include 'LexxpavlovSpellingBundle::spelling.html.twig' %}
```

### 7.3. Настройка логотипа (необязательно)

Вы можете показать пользователям информацию, что они могут отметить ошибку в 
тексте. Можно указать в футере страницы, а можно указать в заголовке статьи.

В бандле есть кнопка логотипа, показывающая информацию о возможности отметить
ошибку. Подробнее смотрите в разделе [Настройка логотипа](logo-button.md).

Добавить кнопку размером 88x31px и с текстом "Ошибка? Ctrl+Enter" можно таким 
образом:
```twig
<a href="{{ path('spelling_info') }}" title="Как исправить ошибку?">
    {% include 'LexxpavlovSpellingBundle:Logo:two.html.twig' %}
</a>
```
Добавить кнопку размером 120x76px и с текстом "Ошибка? Выделите ошибку мышкой и
нажмите Ctrl+Enter" можно так:
```twig
<a href="{{ path('spelling_info') }}" title="Как исправить ошибку?">
    {% include 'LexxpavlovSpellingBundle:Logo:three.html.twig' with {width: '120px', height: '76px'} %}
</a>
```

> **Замечание.** Страницы с роутом "spelling_info" в бандле нет, если она вам
> нужна, добавьте её самостоятельно.

## Шаг 8. Настройка SonataAdminBundle (необязательно)

Подробнее смотрите [Использование SonataAdminBundle](sonata-admin.md)

## Следующие шаги

Базовая установка и настройка бандла завершена. Дальше вы можете увидеть 
описание [технологии работы бандла](technology.md), прочитать [полную настройку
бандла](full-configuration.md), или увидеть [содержание документации](index.md). 
