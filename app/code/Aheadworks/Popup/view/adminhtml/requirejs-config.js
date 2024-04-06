var config = {
    map: {
        '*': {
            awPopupManagerFieldset: 'Aheadworks_Popup/js/managerFieldset',
            awPopupMagnific: 'Aheadworks_Popup/js/jquery.magnific-popup',
            awPopupManager: 'Aheadworks_Popup/js/popupManager',
            awPopupButton: 'Aheadworks_Popup/js/form/component/button',
            awPopupEditForm: 'Aheadworks_Popup/js/form/edit-form'
        }
    },
    config: {
        mixins: {
            'mage/backend/validation' : {
                'Aheadworks_Popup/js/backend/validation-mixin': true
            }
        }
    }
};
