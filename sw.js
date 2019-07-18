self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    var sendNotification = function(message, icon, image, url, data, title) {

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
    }
});

self.addEventListener('notificationclick', function (event) {
    // fix http://crbug.com/463146
    event.notification.close();
    console.log(event);
    event.waitUntil(clients.openWindow(event.notification.data.click_url));

    var json =
        {"ep": event.notification.data.ep};
    $.ajax({
        type:"POST",
        contentType: 'application/json',
        data: JSON.stringify({json}), //{json: JSON.stringify(info)},
        url:"https://blackops.f5ads.com/Notifications2019/app/endpoints/notification.php",
        success : function(data) {
            console.log(data);

        },
        error : function() {
            console.log("error")
        }
    });







    fetch("https://blackops.f5ads.com/Notifications2019/app/endpoints/tracking.php", {
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


