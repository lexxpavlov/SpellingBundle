# Additional check of error

When creating an error from the user occurs `lexxpavlov_spelling.new_error` 
event. You can create your event handler.

Note that the handler of IP-address verification has priority 100, and the 
handler of user role verification has priority 50. So, if you want your handler 
executed to check the IP-address, your handler should be prioritized more than 
100.

## Example of creating an event handler

Create a handler that allow super administrator to create multiple queries 
without checking its IP-address.

### Create a handler class

The class constructor takes a reference to `TokenStorage` and 
`AuthorizationChecker`. In this class, only two public methods - a constructor 
that takes the necessary references, and the method that will be called when 
creating event. A method name can be anything.
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
`onNewError` method checks whether the user is super administrator. If so, 
cancel the rest of the checks.

### Creating a service

Arguments of service must be references to system services and `TokenStorage` 
`AuthorizationChecker`. Service type - handler of 
`lexxpavlov_spelling.new_error` event. Priority of handler must be greater than 
100 - more than the priority handler, that checks IP-address.
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

### That's all!

Create a handler is completed, and now super-admin can create error without 
checking the IP-admin. 

It is interesting that the same class can be a handler of other events. For 
example, you can add items for super administrator to the sidebar menu in 
SonataAdminBundle. If interested, you can see the code of `MenuBuilderListener` 
handler in this bundle.
