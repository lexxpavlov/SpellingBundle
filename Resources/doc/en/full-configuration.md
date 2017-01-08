# Full configuration of bundle

This document lists all settings bundle. 
```yaml
# app/config/config.yml
lexxpavlov_spelling:
    entity_class:         AppBundle\Entity\Spelling
    user_field:           username    # field of user entity, that identified this user
    find_by:              slug
    data_delimiter:       '#@'
    error_trans_domain:   LexxpavlovSpellingBundle
    security:
        entity_class:     AppBundle\Entity\SpellingSecurity
        allowed_role:     null
        query_interval:   5           # in seconds, default 5
        ban_period:       86400       # in seconds, default one day (86400 seconds)
        ban_check_period: 86400       # in seconds, default one day (86400 seconds)
        ban_count:        10
```

All parameters are optional, so if you are satisfied with the default values, 
in the `config.yml` indicate bundle section is not required.

If you specify `~`, all settings will be the default: 
```yaml
# app/config/config.yml
lexxpavlov_spelling: ~
```

## Description of parameters

### entity_class
Default value: AppBundle\Entity\Spelling

Entity class that stores information about the error. 

### user_field
Default value: username

Field of User entity that identifies the user. Used in table of editors
(error creators) and correctors (that fix the error).

> Examples: `name`, `fullname`, `email`.

### find_by
Default value: slug

Field of resource (articles, news, etc.), used to search for a resource 
identifier in determining of route to correct the error.

For details, see [Using SonataAdminBundle](sonata-admin.md).

### data_delimiter
Default value: #@

Separator resource url and service information. This information is used to
determine the entity storing text, for example, in cases where multiple 
entities placed in one page.

If you specify a value other than the default, then the same value is need to
be specified in the front-end part (spelling.js).

For details, see [Technology of work](technology.md).

### error_trans_domain
Default value: LexxpavlovSpellingBundle

Domain of errors translations in the backend.

## Security settings

### entity_class
Default value: AppBundle\Entity\SpellingSecurity

The class of entity stored information about the user's IP-address. Used to
limit the number of requests from the same user (Flood control).

### allowed_role
Default value: null

The role of users that can create errors. By default, everyone can create 
errors, including anonymous users. In order to allow only authorized users can
specify the role `IS_AUTHENTICATED_REMEMBERED` or `IS_AUTHENTICATED_FULLY`.

For details, see [User restriction](auth-check.md).

### query_interval
Default value: 5

Minimum interval of error requests (in seconds). Used to limit the number of
requests from the same user (Flood control). If a user sends a request for 
errors creation before `query_interval` seconds elapsed since the last request, 
he gets one violation.

A value of 0 disables checking the IP-address.

For details, see [IP-address restriction](flood-control.md).

### ban_period
Default value: 86400 (one day)

The period (in seconds) for which the IP-address is blocked (banned). 

### ban_check_period
Default value: 86400 (one day)

Interval (in seconds), which is considered the number of violations to block
IP-address. After a specified time, the number of violations is reset.

### ban_count
Default value: 10

The number of violations for blocking IP-address.
