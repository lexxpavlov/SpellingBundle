# Setting the error window

Error window opens to user after selecting the incorrect text and pressing keys
Ctrl+Enter. In general, the window code should contains 7 elements, they are 
shown in the table:

Name of parameter  | Default selector       | Description of element
-------------------|------------------------|-------------------------------
containerSelector  | .js-spelling-container | Container of window
errorSelector      | .js-spelling-error     | Element of error text
commentSelector    | .js-spelling-comment   | Comment input
submitSelector     | .js-spelling-send      | Send button
loadingSelector    | .js-spelling-loading   | Element of loading icon
errorsListSelector | .js-spelling-errors    | List of errors
closeBtnSelector   | .js-spelling-close     | Close button

You can create a html-code of window yourself by creating the necessary 
elements (elements `loadingSelector` and `errorsListSelector` are optional). 
Css-classes of elements can come up as you like, enough to specify that the
selector element to the relevant parameters when creating an object of 
`Lexxpavlov.Spelling`.

## Error window code, made in bundle

You can use ready-made window code from the bundle. You can modify it 
inheriting your template from it.

Add this code before the closing tag `</body>`:
```twig
{% include 'LexxpavlovSpellingBundle::spelling.html.twig' %}
```
