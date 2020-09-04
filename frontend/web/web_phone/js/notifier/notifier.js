(function () {

    let types = {
        incomingCall: 'incomingCall'
    };

    function Collection() {

        this.notifications = [];

        this.add = function (key, type, notification) {
            if (this.getIndex(key) !== null) {
                return false;
            }
            notification.key = key;
            notification.type = type;
            notification.timePushed = Date.now();
            this.notifications.unshift(notification);
            return notification;
        };

        this.remove = function (key) {
            let index = this.getIndex(key);
            if (index === null) {
                return false;
            }
            this.notifications.splice(index, 1);
            return true;
        };

        this.getIndex = function (key) {
            let index = null;
            this.notifications.forEach(function (notification, i) {
                if (notification.key === key) {
                    index = i;
                }
            });
            return index;
        };

        this.getLast = function () {
            let notification = null;
            for (let i in this.notifications) {
                if (i === 'inArray') {
                    continue;
                }
                notification = this.notifications[i];
            }
            if (typeof notification == 'undefined' || notification === null) {
                return null;
            }
            return notification;
        };

        this.getFirst = function () {
            let notification = null;
            for (let i in this.notifications) {
                if (i === 'inArray') {
                    continue;
                }
                notification = this.notifications[i];
                break;
            }
            if (typeof notification == 'undefined' || notification === null) {
                return null;
            }
            return notification;
        };

        this.one = function (key) {
            let index = this.getIndex(key);
            if (index !== null) {
                return this.notifications[index];
            }
            return null;
        };

        this.count = function () {
            return this.notifications.length;
        };

        this.all = function () {
            return this.notifications;
        };

        this.showAll = function () {
            this.notifications.forEach(function (notification) {
                console.log(notification);
            });
        };

        this.reset = function () {
            this.notifications = [];
        };
    }

    function Notifier() {
        this.notifications = new Collection();
        this.notifiers = {
            'desktop': new DesktopNotifier(),
            'phone': new PhoneWidgetNotifier(),
        };
        this.isOn = true;
        this.offKey = null;

        this.getVisibleNotifications = function () {
            let notifications = [];
            this.notifications.all().forEach(function (notification) {
                if (notification.isShow) {
                    notifications.push(notification);
                }
            });
            return notifications;
        };

        this.add = function (key, type, notification) {
            if (this.notifications.add(key, type, notification) === false) {
                return false;
            }

            if (!notification.isShow) {
                return true;
            }

            notification.isNew = true;
            this.notifiers.desktop.notify(this.getVisibleNotifications());
            this.notifiers.phone.notify(notification);

            notification.isNew = false;
            let self = this;
            setTimeout(function () {
                self.notifiers.desktop.notify(self.getVisibleNotifications());
                self.notifiers.phone.notify(notification);
            }, 50);
            return true;
        };

        this.addAndShowOnlyDesktop = function (key, type, notification) {
            if (this.notifications.add(key, type, notification) === false) {
                return false;
            }

            if (!notification.isShow) {
                return true;
            }

            notification.isNew = true;
            this.notifiers.desktop.notify(this.getVisibleNotifications());

            notification.isNew = false;
            let self = this;
            setTimeout(function () {
                self.notifiers.desktop.notify(self.getVisibleNotifications());
            }, 50);
            return true;
        };

        this.remove = function (key) {
            let notification = this.notifications.one(key);
            if (notification === null) {
                return false;
            }

            if (!notification.isShow) {
                this.notifications.remove(key);
                return true;
            }

            notification.isDeleted = true;
            this.notifiers.desktop.notify(this.getVisibleNotifications());
            if (this.notifiers.phone.isEqual(notification.key)) {
                this.notifiers.phone.notify(notification);
            }

            this.notifications.remove(key);
            let self = this;
            setTimeout(function () {
                let notifications = self.getVisibleNotifications();
                self.notifiers.desktop.notify(notifications);
                if (self.notifiers.phone.isEqual(notification.key)) {
                    self.notifiers.phone.reset();
                }
                if (notifications.length === 0) {
                    self.notifiers.desktop.reset();
                }
            }, 400);
            return true;
        };

        this.hide = function (key) {
            let notification = this.notifications.one(key);
            if (notification === null) {
                return false;
            }
            if (!notification.isShow) {
                return true;
            }

            notification.willHide = true;
            this.notifiers.desktop.notify(this.getVisibleNotifications());
            if (this.notifiers.phone.isEqual(notification.key)) {
                this.notifiers.phone.notify(notification);
            }
            notification.willHide = false;

            notification.isShow = false;
            let self = this;
            setTimeout(function () {
                let notifications = self.getVisibleNotifications();
                self.notifiers.desktop.notify(notifications);
                if (self.notifiers.phone.isEqual(notification.key)) {
                    self.notifiers.phone.reset();
                }
                if (notifications.length === 0) {
                    self.notifiers.desktop.reset();
                }
            }, 400);
            return true;
        };

        this.refresh = function () {
            if (!this.isOn) {
                return false;
            }

            this.notifications.all().forEach(function (notification) {
                notification.isShow = true;
                notification.isNew = true;
            });
            this.notifiers.desktop.notify(this.getVisibleNotifications());

            this.notifications.all().forEach(function (notification) {
                notification.isNew = false;
            });

            let self = this;
            setTimeout(function () {
                self.notifiers.desktop.notify(self.getVisibleNotifications());
            }, 50);

            this.notifiers.phone.reset();
            return true;
        };

        this.on = function (key) {
            if (this.isOff() && this.offKey !== key) {
                return;
            }
            this.isOn = true;
        };

        this.off = function (key) {
            this.isOn = false;
            this.offKey = key;
        };

        this.isOff = function () {
            return this.isOn === false;
        };

        this.reset = function () {
            this.notifiers.desktop.reset();
            this.notifiers.phone.reset();
        };
    }

    function DesktopNotifier() {
        this.$container = document.getElementById('desktop-phone-notifications');

        this.notify = function (notifications) {
            ReactDOM.render(React.createElement(DesktopNotification, {notifications: notifications}), this.$container);
        };

        this.reset = function () {
            ReactDOM.unmountComponentAtNode(this.$container);
            this.$container.innerHTML = '';
        }
    }

    function PhoneWidgetNotifier() {
        this.$container = document.getElementById('widget-phone-notifications');
        this.notificationKey = null;

        this.notify = function (notification) {
            this.notificationKey = notification.key;
            ReactDOM.render(React.createElement(PhoneWidgetNotification, {notification: notification}), this.$container);
        };

        this.reset = function () {
            ReactDOM.unmountComponentAtNode(this.$container);
            this.$container.innerHTML = '';
            this.notificationKey = null;
        };

        this.isEqual = function (key) {
            return this.notificationKey === key;
        };
    }

    window.phoneWidget.notifier = new Notifier();
    window.phoneWidget.notifier.types = types;
})();
