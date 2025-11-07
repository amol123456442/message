self.addEventListener("push", function (event) {
  const data = event.data.json();
  const title = data.title || "New Message!";
  const options = {
    body: data.body || "",
    icon: "https://cdn-icons-png.flaticon.com/512/733/733585.png",
    badge: "https://cdn-icons-png.flaticon.com/512/733/733585.png",
  };
  event.waitUntil(self.registration.showNotification(title, options));
});
