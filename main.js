const applicationServerPublicKey = 'BOebiYnIpJlnmDtYzp2_iHPtnGDVUrlRzUdNTX4rYQP9MXENXHpVw1QqDCHJwfA91WUYY5sqvVqn6hm_CewkhZ4';

function subscribeUserToPush() {
    return navigator.serviceWorker.register('worker/worker.js')
        .then(function(registration) {
            const applicationServerKey = urlB64ToUint8Array(applicationServerPublicKey);
            var subscribeOptions = {
                userVisibleOnly: true,
                applicationServerKey: applicationServerKey

            };

            return registration.pushManager.subscribe(subscribeOptions);
        })
        .then(function(pushSubscription) {
            //console.log('PushSubscription: ', JSON.stringify(pushSubscription));
            return pushSubscription;
        });
}

function urlB64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}
