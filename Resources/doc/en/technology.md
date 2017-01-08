# Technology of work

Common scheme of the work is as follows:
1. User [gets to know](logo-button.md) about the possibility of pointing out to 
errors in the text.
2. User selects some of the text in which the error (or the error itself, or 
the word that contains the error), and presses the keys Ctrl+Enter.
3. The system displays a window to send an error. The window shows a text in 
which the error highlighted. The user can enter a comment (for example, own 
version of error correction), and clicks the "Send" button (or Ctrl+Enter).
4. The system receives an error and the comment, checks the user 
([role](auth-check.md)) and [IP-address](flood-control.md) (perhaps performs 
[additional checks](custom-event-listener.md)), and stores it in the database.
If occurs any errors, error code and error text is returned to the frontend.
5. If during the error sending no errors occurred in the backend, the error 
window is closed, otherwise the error text is displayed to the user.

## Working of script spelling.js

`spelling.js` script processes the selected part of the text (using the
[Rangy](https://github.com/timdown/rangy) library), controls the displaying of
windows errors, sends an error in the backend. The script uses the jQuery.

The script runs automatically with default settings. If you want to configure
the startup options, then you must create your new object, in this case 
automatic creating doesn't occur:
```html
<script>
new Lexxpavlov.Spelling({cssErrorClass: 'mistake', callbackUrl: '/send-error/'});
</script>
```

### Script parameters

1. `callbackUrl`: path to backend, default:&nbsp;/spelling/new-error
2. `cssErrorClass`: css-class for error text, default:&nbsp;spelling-error
3. `dataDelimiter`: delimiter of resource url and service information, 
default:&nbsp;#@
4. `spelledSelector`: selector for parent root element of resource 
(see&nbsp;[below](#resource-url)), default:&nbsp;.js-spelling-check
5. `containerSelector`: selector for container of error window, 
default:&nbsp;.js-spelling-container
6. `errorSelector`: selector for element, which shows the error in the error 
window, default:&nbsp;.js-spelling-error
7. `commentSelector`: selector for comment input, default:&nbsp;.js-spelling-comment
8. `submitSelector`: selector for send button, default:&nbsp;.js-spelling-send
9. `loadingSelector`: selector for element of loading icon, 
default:&nbsp;.js-spelling-loading
10. `errorsListSelector`: selector for errors list, default:&nbsp;.js-spelling-errors
11. `closeBtnSelector`: selector for close button, 
default:&nbsp;.js-spelling-close
12. `createCallback`: callback function of window creation. If callback will 
return `false`, then error window will not appear.
13. `buttonCallback`: callback function of key press. It used to change the 
control key instead of `Ctrl+Enter`.
14. `validateCallback`: callback function of data validation. It can be used to
modify data before sending. If the callback will return `false`, the data does
not go.  
15. `errorsCallback`: callback function to be run when returning error from
backend.
16. `getUrlCallback`: callback function that defines url error.
See&nbsp;[below](#resource-url).

Selectors are used to the use of a custom error window. See more details in 
[Setting error window](error-window.md).

### Resource URL

There may be several different texts on the page, that require user 
check (these texts are called resources hereinafter). In this case, each text
can have a different address, then to find it for error fixing. Various 
resources are placed to various elements (containers) marked css-class selected
in the parameter `spelledSelector`. 

Identify the specific resource in several ways:

**First way** - to specify the type and the resource id

```html
<ul>
    <li class="js-spelling-check" data-id="1" data-type="article">
        Article 1
    </li>
    <li class="js-spelling-check" data-id="2" data-type="news" data-prefix="resources">
        News 2
    </li>
</ul>
```
In this case, you need to specify the id of the resource, its type and prefix, 
where the type can be found in route, who will edit this resource. For example,
Article 1 will changed by route `admin_article_edit`, and news 2 - route 
`resources_news_edit`. (Bundle SonataAdminBundle creates routes with the names 
like `admin_{$type}_edit`.)

In this case, in the url of the page will add additional information - 
`data Delimiter`, type and resource id:
```
/articles/#@article=1
/articles/#@news=2=resources
```

**Second way** - to create a link to page with resource

The first link placed in container, which has the href attribute, contains a
link to the page in which this resource is alone, without other resources (as
if the user was on the page).

```html
<ul>
    <li class="js-spelling-check">
        <a href="/article/1">Title of article 1</a>
        <div class="hidden">
            Article 1
        </div>
    </li>
    <li class="js-spelling-check">
        <a href="/article/2">Title of article 2</a>
        <div class="hidden">
            Article 2 
        </div>
    </li>
</ul>
```

**Third way** - your way

You can pass a parameter `getUrlCallback` function obtained the root element of
the resource, and returns the address of the page.
