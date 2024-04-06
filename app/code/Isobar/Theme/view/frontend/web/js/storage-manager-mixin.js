define([
    'mageUtils'
], function(utils) {
    'use strict';
    
    return function (target) {
        return target.extend({
            /**
             * Sends request to server to gets data
             *
             * @param {String} name - storage name
             * @param {Object} data - ids
             */
            sendRequest: function (name, data) {
                var params  = utils.copy(this.storagesConfiguration[name].requestConfig),
                    url = params.syncUrl,
                    typeId = params.typeId;

                if (this.requestSent || !~~this.storagesConfiguration[name].allowToSendRequest) {
                    return;
                }

                delete params.typeId;
                delete params.url;
                this.requestSent = 1;

                return utils.ajaxSubmit({
                    url: url,
                    data: {
                        ids: data,
                        'type_id': typeId
                    }
                }, params).done(this.requestHandler.bind(this, name));
            }
        })
    };
});