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

        return self.registration.showNotification(title,
            notificationOptions);
    };

    if (event.data) {
        var data = event.data.json();
        event.waitUntil(
            sendNotification(data.body, data.icon, data.image, data.url, data.data, data.title)
        );

        var info =
            {"ep": data.data['data'],
                "sent_id" : data.data['sent_id']};
        //console.log(event);
        var form_data = new FormData();
        let jsonres = JSON.stringify(info);

        form_data.append('json',jsonres );

        fetch("https://pusher.f5ads.com/app/endpoints/tracking2.php", {
            method: 'post',
            body: form_data
        })
            .then(function (data) {
                console.log('Request succeeded with JSON response', data);
            })
            .catch(function (error) {
                console.log('Request failed', error);
            });

    }
});

self.addEventListener('notificationclick', function (event) {
    // fix http://crbug.com/463146
    event.notification.close();
    console.log(event);
    event.waitUntil(clients.openWindow(event.notification.data.click_url));


    var info =
        {"ep": event.notification.data.ep['data'],
            "sent_id": event.notification.data.ep['sent_id']};

    var form_data = new FormData();
    let jsonres = JSON.stringify(info);

    form_data.append('json',jsonres );

    fetch("https://pusher.f5ads.com/app/endpoints/tracking.php", {
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


