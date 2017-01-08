# Дополнительная проверка ошибки

При принятии ошибки от пользователя возникает событие 
`lexxpavlov_spelling.new_error`. Вы можете создать свой обработчик этого 
события. 

Обратите внимание, что обработчик проверки IP-адреса имеет приоритет 100, а 
обработчик проверки роли пользователя имеет приоритет 50. Таким образом, если
вам нужно, чтобы ваш обработчик выполнялся до проверки IP-адреса, то ваш
обработчик должен иметь приоритет больше, чем 100.

## Пример создания обработчика события

Создадим обработчик, который разрешит суперадминистратору создавать множество
запросов без проверки его IP-адреса.

### Создание класса обработчика

Конструктор класса принимает ссылки на `TokenStorage` и `AuthorizationChecker`.
В этом классе всего два публичных метода - конструктор, принимающий нужные ссылки, и
метод, который будет вызываться при создании события. Название метода может 
быть любым.
```php
<?php
// src/AppBundle/EventListener/SuperAdminListener.php
 
namespace AppBundle\EventListener;
 
use Lexxpavlov\SpellingBundle\Event\SpellingNewErrorEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
 
class SuperAdminListener
{
    /** @var TokenStorageInterface */
    private $tokenStorage;
 
    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;
 
    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }
 
    public function onNewError(SpellingNewErrorEvent $event)
    {
        if ($this->isSuperAdmin()) {
            $event->stopPropagation();
        }
    }
    
    private function isSuperAdmin()
    {
        $token = $this->tokenStorage->getToken();
        if (null !== $token) {
            $user = $token->getUser();
            if (is_object($user) && $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN', $user)) {
                return true;
            }
        }
        return false;
    }
}
```
Метод `onNewError` проверяет, является ли пользователь суперадминистратором. 
Если да, то отменить остальные проверки.

### Создание сервиса

Аргументами сервиса должны быть ссылки на системные сервисы `TokenStorage` и
`AuthorizationChecker`. Тип сервиса - обработчик события 
`lexxpavlov_spelling.new_error`. Приоритет обработчика должен быть больше 100 -
больше, чем приоритет обработчика проверки IP-адреса. 
```yaml
services:
    app.listener.super_admin:
        class: AppBundle\EventListener\SuperAdminListener
        arguments: 
          - "@security.token_storage"
          - "@security.authorization_checker"
        tags:
            - name: kernel.event_listener
              event: lexxpavlov_spelling.new_error
              method: onNewError
              priority: 250
```

### Всё готово!

Создание обработчика завершено, теперь суперадминистратор может создавать
ошибки без проверки IP-админа.

Интересно, что этот же класс может быть обработчиком других событий. Например, 
можно суперадминистратору добавить элементы в боковое меню в SonataAdminBundle. 
Если интересно, можно посмотреть код обработчика `MenuBuilderListener` в этом 
бандле.
