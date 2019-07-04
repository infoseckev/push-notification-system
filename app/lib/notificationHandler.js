var notificationHandler = (function () {
    'use strict';

    var module = {
        applicationServerKey:
            'BN1szjBQjrtB0R0l-PtmreFmomlHvVP0ZAvB1lPS0ePlyZQnizJUci5yiWQKJUFDbBztqXCsyYkeP0qrXpKiFns',

        isPushEnabled: false,

        init: function () {

            navigator.serviceWorker.register('serviceWorker.js').then(
                () => {
                    console.log('[SW] Service worker has been registered');
                    this.push_updateSubscription();
                },
                e => {
                    console.error('[SW] Service worker registration failed', e);
                    //changePushButtonState('incompatible');
                }
            );
        },

        push_updateSubscription: function () {
            navigator.serviceWorker.ready
                .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
                .then(subscription => {
                    //changePushButtonState('disabled');

                    if (!subscription) {
                        this.push_subscribe();

                        return;
                    }

                    // Keep server in sync with the latest endpoint
                    return this.push_sendSubscriptionToServer(subscription, 'PUT');
                })
                .then(subscription => subscription && this.changePushButtonState('enabled')) // Set your UI to show they have subscribed for push messages
                .catch(e => {
                    console.error('Error when updating the subscription', e);
                });
        },
    };

    return module;

}());