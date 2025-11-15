<?php
include 'toastStore.php';
addToast("Welcome!", "Your dashboard loaded successfully.");
addToast("Update", "A new version is available.");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Toast Demo</title>
<link rel="stylesheet" href="toast-styles.css">
</head>
<body>

<div class="toast-container" id="toastContainer"></div>

<ul class="notification-list" id="notification-list"></ul>

<script>
const toastContainer = document.getElementById('toastContainer');
const notificationList = document.getElementById('notification-list');


const pendingToasts = <?php echo json_encode(getToasts()); ?>;
const notifications = <?php echo json_encode(getNotifications()); ?>;


function showToast(title, message, time = 'Just now') {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `
      <div class="toast-header">
        <span class="toast-icon">🔔</span>
        <strong class="toast-title">${title}</strong>
      </div>
      <p class="toast-message">${message}</p>
    `;
    toastContainer.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);

    // Panel
    const li = document.createElement('li');
    li.className = 'notification-item is-unread';
    li.innerHTML = `
      <div class="icon-wrap"><i class="fas fa-bell"></i></div>
      <div class="details">
        <p class="title">${title}</p>
        <p class="message">${message}</p>
      </div>
      <span class="time">${time}</span>
      <span class="unread-dot"></span>
    `;
    li.addEventListener('click', () => markRead(li));
    notificationList.prepend(li);
}

function markRead(el){
    el.classList.remove('is-unread');
    const dot = el.querySelector('.unread-dot');
    if(dot) dot.remove();
}

pendingToasts.forEach(t => showToast(t.title, t.message));
</script>

</body>
</html>
