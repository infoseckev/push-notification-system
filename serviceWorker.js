/*self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const sendNotification = body => {
        // you could refresh a notification badge here with postMessage API
        const title = "Notification!";
        const options = {
            body: body

        };
        return self.registration.showNotification(title, {
            body
        });
    };

    if (event.data) {
        const message = event.data.text();
        event.waitUntil(sendNotification(message));
    }
});*/

self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    var sendNotification = function(message, icon, image, url, data, title) {
        // on actualise la page des notifications ou/et le compteur de notifications
        //self.refreshNotifications();

        const notificationOptions = {
            body: message,
            icon: icon,
            data: {
                click_url: url,
                ep : data
            },
            image: image
        };

        /*var title = title || "No Title!",
            icon = 'uploads/icon.jpg',
            message = message || 'No Message!',
            url = url || '',
            image =  'uploads/image.jpg';*/

        return self.registration.showNotification(title,
            notificationOptions);
    };

    if (event.data) {

        //const message = event.data.text();
        //event.waitUntil(sendNotification(message));


        var data = event.data.json();
        event.waitUntil(
            sendNotification(data.body, data.icon, data.image, data.url, data.data, data.title)
        );
    } /*else {
        event.waitUntil(
            self.registration.pushManager.getSubscription().then(function(subscription) {
                if (!subscription) {
                    return;
                }

                return fetch('api/notifications/last?endpoint=' + encodeURIComponent(subscription.endpoint)).then(function (response) {
                    if (response.status !== 200) {
                        throw new Error();
                    }

                    // Examine the text in the response
                    return response.json().then(function (data) {
                        if (data.error || !data.notification) {
                            throw new Error();
                        }

                        return sendNotification(data.notification.message);
                    });
                }).catch(function () {
                    return sendNotification();
                });
            })
        );
    }*/
});
/*

self.refreshNotifications = function(clientList) {
    if (clientList == undefined) {
        clients.matchAll({ type: "window" }).then(function (clientList) {
            self.refreshNotifications(clientList);
        });
    } else {
        for (var i = 0; i < clientList.length; i++) {
            var client = clientList[i];
            if (client.url.search(/notifications/i) >= 0) {
                // si la page des notifications est ouverte on la recharge
                client.postMessage('reload');
            }

            // si on n'est pas sur la page des notifications on recharge le compteur
            client.postMessage('refreshNotifications');
        }
    }
};*/

self.addEventListener('notificationclick', function (event) {
    // fix http://crbug.com/463146
    event.notification.close();
    console.log(event);
    event.waitUntil(clients.openWindow(event.notification.data.click_url));


    var info =
        {"ep": event.notification.data.ep};

    var form_data = new FormData();
    let jsonres = JSON.stringify(info);

    form_data.append('json',jsonres );


    fetch("app/endpoints/tracking.php", {
        method: 'post',
        body: form_data
    })
        .then(function (data) {
            console.log('Request succeeded with JSON response', data);
        })
        .catch(function (error) {
            console.log('Request failed', error);
        });

});
/*
self.addEventListener('message', function (event) {
    var message = event.data;

    switch (message) {
        case 'dispatchRemoveNotifications':
            clients.matchAll({ type: "window" }).then(function (clientList) {
                for (var i = 0; i < clientList.length; i++) {
                    clientList[i].postMessage('removeNotifications');
                }
            });
            break;
        default:
            console.warn("Message '" + message + "' not handled.");
            break;
    }
});

*/


