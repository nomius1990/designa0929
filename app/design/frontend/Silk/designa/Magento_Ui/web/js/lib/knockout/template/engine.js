/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'ko',
    'underscore',
    './observable_source',
    './renderer'
], function (ko, _, Source, renderer) {
    'use strict';

    var RemoteTemplateEngine,
        NativeTemplateEngine = ko.nativeTemplateEngine,
        sources = {};

    RemoteTemplateEngine = function () {};

    function createTemplateIdentifier(templateName) {
        return templateName;
    }

    RemoteTemplateEngine.prototype = new NativeTemplateEngine;
    RemoteTemplateEngine.prototype.constructor = RemoteTemplateEngine;
    RemoteTemplateEngine.prototype.makeTemplateSource = function (template, templateDocument, options, bindingContext) {
        var source,
            templateId;

        if (typeof template === 'string') {
            templateId = createTemplateIdentifier(template);
            source = sources[templateId];

            if (!source) {
                source = new Source(template);
                source.requestedBy = bindingContext.$data.name;
                sources[templateId] = source;

                renderer.render(template).done(function (rendered) {
                    source.nodes(rendered);
                }).fail(function () {});
            }

            if (source.requestedBy !== bindingContext.$data.name) {}

            return source;
        } else if (template.nodeType === 1 || template.nodeType === 8) {
            source = new ko.templateSources.anonymousTemplate(template);

            return source;
        }

        throw new Error('Unknown template type: ' + template);
    };

    RemoteTemplateEngine.prototype.renderTemplateSource = function (templateSource) {
        var nodes = templateSource.nodes();

        return ko.utils.cloneNodes(nodes);
    };

    RemoteTemplateEngine.prototype.renderTemplate = function (template, bindingContext, options, templateDocument) {
        var templateSource = this.makeTemplateSource(template, templateDocument, options, bindingContext);

        return this.renderTemplateSource(templateSource, bindingContext, options);
    };

    return new RemoteTemplateEngine;
});
