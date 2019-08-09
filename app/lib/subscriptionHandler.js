var subscriptionHandler = (function () {
    'use strict';

    var module = {
        applicationServerKey:
            'BN1szjBQjrtB0R0l-PtmreFmomlHvVP0ZAvB1lPS0ePlyZQnizJUci5yiWQKJUFDbBztqXCsyYkeP0qrXpKiFns',

        isPushEnabled: false,

        init: function () {
            if (!('serviceWorker' in navigator)) {
                console.warn('Service workers are not supported by this browser');
                //changePushButtonState('incompatible');
                return;
            }

            if (!('PushManager' in window)) {
                console.warn('Push notifications are not supported by this browser');
                //changePushButtonState('incompatible');
                return;
            }

            if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
                console.warn('Notifications are not supported by this browser');
                //changePushButtonState('incompatible');
                return;
            }

            // Check the current Notification permission.
            // If its denied, the button should appears as such, until the user changes the permission manually
            if (Notification.permission === 'denied') {
                console.warn('Notifications are denied by the user');
                //changePushButtonState('incompatible');
                return;
            }
            navigator.serviceWorker.register('/serviceWorker.js').then(
                () => {
                    console.log('[SW] Service worker has been registered');
                    this.push_updateSubscription();
                },
                e => {indexedDB
                    console.error('[SW] Service worker registration failed', e);
                    //changePushButtonState('incompatible');
                }
            );
        },


        urlBase64ToUint8Array: function (base64String) {
            const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        },

        push_sendSubscriptionToServer: function (subscription, method) {
            const key = subscription.getKey('p256dh');
            const token = subscription.getKey('auth');
            const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

            var e = myfctn.init();
            var ipaddr = ip;

            var browserInfo = {
                'ip': ipaddr, 'site': window.location.toString(), 'osname': e.os.name, 'osversion': e.os.version,
                'browsername': e.browser.name, 'browserversion': e.browser.version,
                'useragent': navigator.userAgent, 'appversion': navigator.appVersion,
                'platform': navigator.platform, 'vendor': navigator.vendor
            };//TODO
            //return fetch('http://localhost/fluffy-octo-couscous/app/endpoints/subscription.php', {
            return fetch('https://blackops.f5ads.com/Notifications2019/app/endpoints/subscription.php', {
                method,
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                    authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
                    browser: browserInfo,
                    contentEncoding,
                }),
            }).then(() => subscription);
        },

        checkNotificationPermission: function () {
            return new Promise((resolve, reject) => {
                if (Notification.permission === 'denied') {
                    return reject(new Error('Push messages are blocked.'));
                }

                if (Notification.permission === 'granted') {
                    return resolve();
                }

                if (Notification.permission === 'default') {
                    return Notification.requestPermission().then(result => {
                        if (result !== 'granted') {
                            reject(new Error('Bad permission result'));
                        }

                        resolve();
                    });
                }
            });
        },

        push_subscribe: function () {

            return this.checkNotificationPermission()
                .then(() => navigator.serviceWorker.ready)
                .then(serviceWorkerRegistration =>
                    serviceWorkerRegistration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: this.urlBase64ToUint8Array(this.applicationServerKey),
                    })
                )
                .then(subscription => {
                    // Subscription was successful
                    // create subscription on your server
                    return this.push_sendSubscriptionToServer(subscription, 'POST');
                })
                .then(subscription => subscription && this.changePushButtonState('enabled')) // update your UI
                .catch(e => {
                    if (Notification.permission === 'denied') {
                        // The user denied the notification permission which
                        // means we failed to subscribe and the user will need
                        // to manually change the notification permission to
                        // subscribe to push messages
                        console.warn('Notifications are denied by the user.');
                        //changePushButtonState('incompatible');
                    } else {
                        // A problem occurred with the subscription; common reasons
                        // include network errors or the user skipped the permission
                        console.error('Impossible to subscribe to push notifications', e);
                        //changePushButtonState('disabled');
                    }
                });
        },


        push_unsubscribe: function () {
            //changePushButtonState('computing');

            // To unsubscribe from push messaging, you need to get the subscription object
            navigator.serviceWorker.ready
                .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
                .then(subscription => {
                    // Check that we have a subscription to unsubscribe
                    if (!subscription) {
                        // No subscription object, so set the state
                        // to allow the user to subscribe to push
                        //changePushButtonState('disabled');
                        return;
                    }

                    // We have a subscription, unsubscribe
                    // Remove push subscription from server
                    return this.push_sendSubscriptionToServer(subscription, 'DELETE');
                })
                .then(subscription => subscription.unsubscribe())
                .then(() => this.changePushButtonState('disabled'))
                .catch(e => {
                    // We failed to unsubscribe, this can lead to
                    // an unusual state, so  it may be best to remove
                    // the users data from your data store and
                    // inform the user that you have done so
                    console.error('Error when unsubscribing the user', e);
                    //changePushButtonState('disabled');
                });
        },
        changePushButtonState: function (state) {
            switch (state) {
                case 'enabled':
                    //pushButton.disabled = false;
                    //pushButton.textContent = 'Disable Push notifications';
                    this.isPushEnabled = true;
                    break;
                case 'disabled':
                    //pushButton.disabled = false;
                    //pushButton.textContent = 'Enable Push notifications';
                    this.isPushEnabled = false;
                    break;
                case 'computing':
                    //pushButton.disabled = true;
                    //pushButton.textContent = 'Loading...';
                    break;
                case 'incompatible':
                    //pushButton.disabled = true;
                    //pushButton.textContent = 'Push notifications are not compatible with this browser';
                    break;
                default:
                    console.error('Unhandled push button state', state);
                    break;
            }
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