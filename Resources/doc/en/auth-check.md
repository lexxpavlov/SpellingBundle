# User restriction

By default, all users can point out errors in the texts. If you want to restrict
this, it is necessary to specify `allowed_role` parameter in config bundle. If 
you want to allow access only to authorized users, you can specify a role 
`IS_AUTHENTICATED_REMEMBERED` or `IS_AUTHENTICATED_FULLY` (see more 
[Symfony documentation](http://symfony.com/doc/current/security.html#checking-to-see-if-a-user-is-logged-in-is-authenticated-fully))).
If you want to restrict to not all authorized users, you must specify a role 
(for example, the `ROLE_ADMIN` or `ROLE_SPELLING`).

> **Note**. Check of user's role occurs after checking the IP-address. 

The ability to restrict users running as handling event 
`lexxpavlov_spelling.new_error`. See more in [Additional check of error](custom-event-listener.md)
