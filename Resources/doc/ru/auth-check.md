# Ограничение пользователей

По умолчанию все пользователи могут указывать ошибки в текстах. Если нужно
ограничить эту возможность, то нужно указать параметр `allowed_role` в конфиге
бандла. Если нужно разрешить доступ только авторизованным пользователям, то
можно указать роль `IS_AUTHENTICATED_REMEMBERED` или `IS_AUTHENTICATED_FULLY`
(см. подробнее [документалию Symfony](http://symfony.com/doc/current/security.html#checking-to-see-if-a-user-is-logged-in-is-authenticated-fully)),
если нужно ограничить не всем авторизованным пользователям, то необходимо 
указать роль (например, `ROLE_ADMIN` или `ROLE_SPELLING`).

> Замечание. Проверка роли пользователя происходит после проверки IP-адреса. 

Возможность ограничения пользователей работает как обработка события 
`lexxpavlov_spelling.new_error`. Подробнее см. в разделе [Дополнительная 
проверка ошибки](custom-event-listener.md)
