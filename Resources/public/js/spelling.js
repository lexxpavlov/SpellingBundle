(function($) {

    'use strict';

    function Spelling(options) {

        var spelling = this;
        configure(options);

        this.windowShown = false;
        this.$container  = $(this.options.containerSelector);
        this.$error      = $(this.options.errorSelector);
        this.$comment    = $(this.options.commentSelector);
        this.$submit     = $(this.options.submitSelector);
        this.$loading    = $(this.options.loadingSelector);
        this.$errorsList = $(this.options.errorsListSelector);

        this.$container.on('click', this.options.closeBtnSelector, function(e){ e.preventDefault(); spelling.hideWindow(); });
        this.$container.click(function(e){ if ($(e.target).is(spelling.options.containerSelector)) { spelling.hideWindow(); }});
        this.$comment.keydown(function(e){ if (e.ctrlKey && e.which === 13) { spelling.$submit.click(); }});

        this.$submit.click(function(){
            spelling.$errorsList.empty();

            var data = spelling.$container.data('text-data');
            data.comment = spelling.$comment.val();
            data.url = spelling.$container.data('url') || location.pathname;

            if (spelling.options.validateCallback(data)) {
                spelling.$loading.show();
                $.post(spelling.options.callbackUrl, data, function(result) {
                    spelling.$loading.hide();
                    if (result === true) {
                        spelling.hideWindow();
                    } else {
                        spelling.options.errorsCallback(result, spelling.$errorsList);
                    }
                }, 'json');
            }
        });

        $(document).keydown(function (e) {
            if (!spelling.windowShown && spelling.options.buttonCallback(e)) {
                if (window.getSelection && window.getSelection().toString().length == 0
                 || document.selection && document.selection.type == 'None') {
                    return;
                }
                var selection = rangy.getSelection();
                var $node = $(selection.getRangeAt(0).startContainer)
                    .closest(spelling.options.spelledSelector);
                var textData = spelling.getErrorText(selection);
                var url = spelling.options.getUrlCallback($node);
                if (spelling.options.createCallback(textData, url, $node)) {
                    spelling.$container.data('url', url);
                    spelling.showWindow(textData);
                }
            }
            if (spelling.windowShown && e.which === 27) { // 27 - esc
                spelling.hideWindow();
            }
        });

        function configure(options) {
            var defaults = {
                callbackUrl:        '/spelling/new-error',
                cssErrorClass:      'spelling-error',
                dataDelimiter:      '#@',
                spelledSelector:    '.js-spelling-check',
                containerSelector:  '.js-spelling-container',
                errorSelector:      '.js-spelling-error',
                commentSelector:    '.js-spelling-comment',
                submitSelector:     '.js-spelling-send',
                loadingSelector:    '.js-spelling-loading',
                errorsListSelector: '.js-spelling-errors',
                closeBtnSelector:   '.js-spelling-close',
                buttonCallback:     function (e) {
                    return e.ctrlKey && e.which === 13; // 13 - Enter
                },
                createCallback:     function (textData, url, $node) {
                    return true;
                },
                validateCallback:   function (data) {
                    if (!data.url || data.url.length == 0) {
                        return false;
                    }
                    if (data.url[0] === '#') {
                        data.url = location.pathname + data.url;
                    }
                    return true;
                },
                errorsCallback:     function (errors, $errorsList) {
                    for (var id in errors) {
                        if (errors.hasOwnProperty(id)) {
                            $errorsList.append('<li data-code="' + id + '">' + errors[id] + '</li>');
                        }
                    }
                },
                getUrlCallback:     function ($node) {
                    var $item;
                    if ($node.length === 0) return null;
                    if (($item = $node.find('[data-type][data-id]')) && $item.length > 0) {
                        return location.href + this.dataDelimiter + $item.data('type') + '=' + $item.data('id');
                    }
                    if (($item = $node.find('a[href]:first-child')) && $item.length > 0) {
                        return $item.attr('href');
                    }
                    return null;
                }
            };

            spelling.options = $.extend({}, defaults, options);
            Lexxpavlov.Spelling.loaded = true;
        }
    }

    Spelling.prototype.showWindow = function(textData) {
        this.$error.html(textData.prefix + '<span class="' + this.options.cssErrorClass+ '">' + textData.error + '</span>' + textData.suffix);
        this.$container.data('text-data', textData).show();
        this.$comment.focus();
        this.windowShown = true;
    };

    Spelling.prototype.hideWindow = function() {
        this.$container.hide();
        this.windowShown = false;
        this.$comment.val('');
        this.$errorsList.empty();
        this.$container.data('url', null);
    };

    Spelling.prototype.getErrorText = function(selection) {
        var characterOffset = 60;
        var range = selection.getRangeAt(0);
        var startContainer = range.startContainer;
        var endContainer   = range.endContainer;
        var delimiterRegex = /(\.[^\d])|[!?]/;

        var startText = startContainer.textContent.substring(0, range.startOffset),
            errorText = range.toString(),
            endText = endContainer.textContent.substr(range.endOffset, characterOffset);
        if (startText.length > characterOffset) {
            var startIndex = regexLastIndexOf(startText, delimiterRegex);
            if (startIndex >=0 && startIndex < (startText.length - characterOffset)) {
                startIndex = startText.indexOf(' ', startText.length - characterOffset) + 1;
            } else startIndex += 2;
            startText = startText.substring(startIndex);
        }
        if (endText.length > 0) {
            var endIndex = endText.search(delimiterRegex);
            if (endIndex === -1) {
                endIndex = endText.lastIndexOf(' ');
            } else endIndex += 1;
            endText = endText.substring(0, endIndex);
        }

        if (range.startOffset < characterOffset && startContainer.parentNode.firstChild.textContent.length === 1) {
            startText = startContainer.parentNode.firstChild.textContent + startText;
        }

        if (/^\s+/.test(errorText)) {
            errorText = errorText.replace(/^\s+/, '');
            startText = startText + ' ';
        }
        if (/\s+$/.test(errorText)) {
            errorText = errorText.replace(/\s+$/, '');
            endText = ' ' + endText;
        }
        return {prefix: startText, error: errorText, suffix: endText};

        function regexLastIndexOf(str, regex) {
            regex = (regex.global) ? regex : new RegExp(regex.source, "g" + (regex.ignoreCase ? "i" : "") + (regex.multiLine ? "m" : ""));
            var lastIndexOf = -1, nextStop = 0, result;
            while(null !== (result = regex.exec(str))) {
                lastIndexOf = result.index;
                regex.lastIndex = ++nextStop;
            }
            return lastIndexOf;
        }
    };

    window.Lexxpavlov = window.Lexxpavlov || {};
    Lexxpavlov.Spelling = Spelling;

    $(function() {
        if (!Lexxpavlov.Spelling.loaded) {
            new Lexxpavlov.Spelling();
        }
    });

})(jQuery);
