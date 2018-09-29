define([
    'jquery',
    'ko',
    'uiRegistry',
    'module',
    '../template/renderer',
    'mage/translate',
    'mage/apply/main'
], function ($, ko, registry, module, renderer, $t, mage) {
    'use strict';
    var captionPlaceholder = {},
        optgroupTmpl = '<optgroup label="${ $.label }"></optgroup>',
        nbspRe = /&nbsp;/g,
        optionsText,
        optionsValue,
        optionTitle,
        locations = {
            'legend': 'Caption for the fieldset element',
            'label': 'Label for an input element.',
            'button': 'Push button',
            'a': 'Link label',
            'b': 'Bold text',
            'strong': 'Strong emphasized text',
            'i': 'Italic text',
            'em': 'Emphasized text',
            'u': 'Underlined text',
            'sup': 'Superscript text',
            'sub': 'Subscript text',
            'span': 'Span element',
            'small': 'Smaller text',
            'big': 'Bigger text',
            'address': 'Contact information',
            'blockquote': 'Long quotation',
            'q': 'Short quotation',
            'cite': 'Citation',
            'caption': 'Table caption',
            'abbr': 'Abbreviated phrase',
            'acronym': 'An acronym',
            'var': 'Variable part of a text',
            'dfn': 'Term',
            'strike': 'Strikethrough text',
            'del': 'Deleted text',
            'ins': 'Inserted text',
            'h1': 'Heading level 1',
            'h2': 'Heading level 2',
            'h3': 'Heading level 3',
            'h4': 'Heading level 4',
            'h5': 'Heading level 5',
            'h6': 'Heading level 6',
            'center': 'Centered text',
            'select': 'List options',
            'img': 'Image',
            'input': 'Form element'
        },
        composeTranslateAttr = function (translationData, location) {
            var obj = [{
                'shown': translationData.shown,
                'translated': translationData.translated,
                'original': translationData.original,
                'location': locations[location] || 'Text'
            }];

            return JSON.stringify(obj);
        },
        setText = function (el, text) {
            $(el).text(text);
        },
        setTranslateProp = function (el, original) {
            var location = $(el).prop('tagName').toLowerCase(),
                translated = $.mage.__(original),
                translationData = {
                    shown: translated,
                    translated: translated,
                    original: original
                },
                translateAttr = composeTranslateAttr(translationData, location);

            $(el).attr('data-translate', translateAttr);

            setText(el, translationData.shown);
        },
        isVirtualElement = function (node) {
            return node.nodeType === 8;
        },
        getRealElement = function (el, isUpdate) {
            if (isVirtualElement(el)) {
                if (isUpdate) {
                    return $(el).next('span');
                }

                return $('<span/>').insertAfter(el);
            }

            return el;
        },
        execute = function (element, valueAccessor, isUpdate) {
            var original = ko.unwrap(valueAccessor() || ''),
                el = getRealElement(element, isUpdate),
                inlineTranslation = (module.config() || {}).inlineTranslation;

            if (inlineTranslation) {
                setTranslateProp(el, original);
            } else {
                setText(el, $.mage.__(original));
            }
    };

    function applyComponents(el, bindingContext, promise, component) {
        promise.resolve();
        component = bindingContext.createChildContext(component);

        ko.utils.extend(component, { $t: $t});
        ko.utils.arrayForEach(el.childNodes, ko.cleanNode);
        ko.applyBindingsToDescendants(component, el);
    }

    ko.bindingHandlers.scope = {
        init: function () {
            return {
                controlsDescendantBindings: true
            };
        },

        update: function (el, valueAccessor, allBindings, viewModel, bindingContext) {
            var component = valueAccessor(),
                promise = $.Deferred(),
                apply = applyComponents.bind(this, el, bindingContext, promise);

            if (typeof component === 'string') {
                registry.get(component, apply);
            } else if (typeof component === 'function') {
                component(apply);
            }
        }
    };

    ko.bindingHandlers.afterRender = {
        init: function (element, valueAccessor, allBindings, viewModel) {
            var callback = valueAccessor();

            if (typeof callback === 'function') {
                callback.call(viewModel, element, viewModel);
            }
        }
    };

    ko.bindingHandlers.fadeVisible = {
        init: function (element, valueAccessor) {
            var value = valueAccessor();

            $(element).toggle(ko.unwrap(value));
        },

        update: function (element, valueAccessor) {
            var value = valueAccessor();

            ko.unwrap(value) ? $(element).fadeIn() : $(element).fadeOut();
        }
    };

    ko.bindingHandlers.i18n = {
        init: function (element, valueAccessor) {
            execute(element, valueAccessor);
        },
        update: function (element, valueAccessor) {
            execute(element, valueAccessor, true);
        }
    };

    ko.bindingHandlers.mageInit = {
        init: function (el, valueAccessor) {
            var data = valueAccessor();

            _.each(data, function (config, component) {
                mage.applyFor(el, config, component);
            });
        }
    };

    ko.bindingHandlers.optgroup = {
        init: function (element) {
            if (ko.utils.tagNameLower(element) !== 'select') {
                throw new Error('options binding applies only to SELECT elements');
            }

            while (element.length > 0) {
                element.remove(0);
            }

            return {
                'controlsDescendantBindings': true
            };
        },
        update: function (element, valueAccessor, allBindings) {
            var selectWasPreviouslyEmpty = element.length === 0,
                previousScrollTop = !selectWasPreviouslyEmpty && element.multiple ? element.scrollTop : null,
                includeDestroyed = allBindings.get('optionsIncludeDestroyed'),
                arrayToDomNodeChildrenOptions = {},
                captionValue,
                unwrappedArray = ko.utils.unwrapObservable(valueAccessor()),
                filteredArray,
                previousSelectedValues,
                itemUpdate = false,
                callback = setSelectionCallback,
                nestedOptionsLevel = -1;

            optionsText = ko.utils.unwrapObservable(allBindings.get('optionsText')) || 'text';
            optionsValue = ko.utils.unwrapObservable(allBindings.get('optionsValue')) || 'value';
            optionTitle = optionsText + 'title';

            if (element.multiple) {
                previousSelectedValues = ko.utils.arrayMap(
                    selectedOptions(),
                    ko.selectExtensions.readValue
                );
            } else {
                previousSelectedValues = element.selectedIndex >= 0 ?
                    [ko.selectExtensions.readValue(element.options[element.selectedIndex])] :
                    [];
            }

            if (unwrappedArray) {
                if (typeof unwrappedArray.length === 'undefined') {
                    unwrappedArray = [unwrappedArray];
                }

                filteredArray = ko.utils.arrayFilter(unwrappedArray, function (item) {
                    if (item && !item.label) {
                        return false;
                    }

                    return includeDestroyed ||
                        item === undefined ||
                        item === null ||
                        !ko.utils.unwrapObservable(item._destroy);
                });
                filteredArray.map(recursivePathBuilder, null);
            }

            arrayToDomNodeChildrenOptions.beforeRemove = function (option) {
                element.removeChild(option);
            };

            if (allBindings.has('optionsAfterRender')) {
                callback = function (arrayEntry, newOptions) {
                    setSelectionCallback(arrayEntry, newOptions);
                    ko.dependencyDetection.ignore(
                        allBindings.get('optionsAfterRender'),
                        null,
                        [newOptions[0],
                        arrayEntry !== captionPlaceholder ? arrayEntry : undefined]
                    );
                };
            }

            filteredArray = formatOptions(filteredArray);
            ko.utils.setDomNodeChildrenFromArrayMapping(
                element,
                filteredArray,
                optionNodeFromArray,
                arrayToDomNodeChildrenOptions,
                callback
            );

            ko.dependencyDetection.ignore(function () {
                var selectionChanged;

                if (allBindings.get('valueAllowUnset') && allBindings.has('value')) {
                    ko.selectExtensions.writeValue(
                        element,
                        ko.utils.unwrapObservable(allBindings.get('value')),
                        true
                    );
                } else {
                    if (element.multiple) {
                        selectionChanged = previousSelectedValues.length &&
                            selectedOptions().length < previousSelectedValues.length;
                    } else {
                        selectionChanged = previousSelectedValues.length && element.selectedIndex >= 0 ?
                            ko.selectExtensions.readValue(element.options[element.selectedIndex]) !==
                            previousSelectedValues[0] : previousSelectedValues.length || element.selectedIndex >= 0;
                    }

                    if (selectionChanged) {
                        ko.utils.triggerEvent(element, 'change');
                    }
                }
            });

            if (previousScrollTop && Math.abs(previousScrollTop - element.scrollTop) > 20) {
                element.scrollTop = previousScrollTop;
            }

            function selectedOptions() {
                return ko.utils.arrayFilter(element.options, function (node) {
                    return node.selected;
                });
            }

            function applyToObject(object, predicate, defaultValue) {
                var predicateType = typeof predicate;

                if (predicateType === 'function') {
                    return predicate(object);
                } else if (predicateType === 'string') {
                    return object[predicate];
                }

                return defaultValue;
            }

            function recursivePathBuilder(obj) {

                obj[optionTitle] = (this && this[optionTitle] ? this[optionTitle] + '/' : '') + obj[optionsText].trim();

                if (Array.isArray(obj[optionsValue])) {
                    obj[optionsValue].map(recursivePathBuilder, obj);
                }
            }

            function optionNodeFromArray(arrayEntry, oldOptions) {
                var option;

                if (oldOptions.length) {
                    previousSelectedValues = oldOptions[0].selected ?
                        [ko.selectExtensions.readValue(oldOptions[0])] : [];
                    itemUpdate = true;
                }

                if (arrayEntry === captionPlaceholder) { 
                    option = element.ownerDocument.createElement('option');
                    ko.utils.setTextContent(option, allBindings.get('optionsCaption'));
                    ko.selectExtensions.writeValue(option, undefined);
                } else if (typeof arrayEntry[optionsValue] === 'undefined') {
                    option = utils.template(optgroupTmpl, {
                        label: arrayEntry[optionsText],
                        title: arrayEntry[optionsText + 'title']
                    });
                    option = ko.utils.parseHtmlFragment(option)[0];

                } else {
                    option = element.ownerDocument.createElement('option');
                    option.setAttribute('data-title', arrayEntry[optionsText + 'title']);
                    ko.selectExtensions.writeValue(option, arrayEntry[optionsValue]);
                    ko.utils.setTextContent(option, arrayEntry[optionsText]);
                }

                return [option];
            }

            function setSelectionCallback(newOptions) {
                var isSelected;
                if (previousSelectedValues.length) {
                    isSelected = ko.utils.arrayIndexOf(
                        previousSelectedValues,
                        ko.selectExtensions.readValue(newOptions.value)
                    ) >= 0;

                    ko.utils.setOptionNodeSelectionState(newOptions.value, isSelected);

                    if (itemUpdate && !isSelected) {
                        ko.dependencyDetection.ignore(ko.utils.triggerEvent, null, [element, 'change']);
                    }
                }
            }

            function strPad(string, times) {
                return (new Array(times + 1)).join(string);
            }

            function formatOptions(options) {
                var res = [];
                nestedOptionsLevel++;

                if (!nestedOptionsLevel) {
                    if (allBindings.has('optionsCaption')) {
                        captionValue = ko.utils.unwrapObservable(allBindings.get('optionsCaption'));
                        if (captionValue !== null && captionValue !== undefined && captionValue !== false) {
                            res.push(captionPlaceholder);
                        }
                    }
                }

                ko.utils.arrayForEach(options, function (option) {
                    var value = applyToObject(option, optionsValue, option),
                        label = applyToObject(option, optionsText, value) || '',
                        disabled = applyToObject(option, 'disabled', false) || false,
                        obj = {},
                        space = '\u2007\u2007\u2007';

                    obj[optionTitle] = applyToObject(option, optionsText + 'title', value);

                    if (disabled) {
                        obj.disabled = disabled;
                    }

                    label = label.replace(nbspRe, '').trim();

                    if (Array.isArray(value)) {
                        obj[optionsText] = strPad('&nbsp;', nestedOptionsLevel * 4) + label;
                        res.push(obj);
                        res = res.concat(formatOptions(value));
                    } else {
                        obj[optionsText] = strPad(space, nestedOptionsLevel * 2) + label;
                        obj[optionsValue] = value;
                        res.push(obj);
                    }
                });
                nestedOptionsLevel--;

                return res;
            }
        }
    };

    ko.virtualElements.allowedBindings.scope = true;
    ko.virtualElements.allowedBindings.i18n = true;
    ko.bindingHandlers.selectedOptions.after.push('optgroup')

    renderer
        .addNode('scope')
        .addNode('translate', {
            binding: 'i18n'
        })
        .addAttribute('scope', {
            name: 'ko-scope'
        })
        .addAttribute('translate', {
            binding: 'i18n'
        })
        .addAttribute('afterRender');
});
