self.addEventListener("push", (event) => {
  const notify = event.data.json().notification;
  

  event.waitUntil(self.registration.showNotification(notify.title, {
    body: notify.body,
    icon: notify.icon,
    data: {
      url: notify.click_action
    }
  }));
});

self.addEventListener("notificationclick", (event) => {
  event.waitUntil(clients.openWindow(event.notification.data.url))
})