(function () {

    function Collection() {

        this.notifications = [];

        this.add = function (key, data) {
            if (this.getIndex(key) !== null) {
                return false;
            }
            data.key = key;
            data.timePushed = Date.now();
            this.notifications.unshift(data);
            return data;
        };

        this.remove = function (key) {
            let index = this.getIndex(key);
            if (index !== null) {
                this.notifications.splice(index, 1);
                return true;
            }
            return false;
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

        this.add = function (key, notification) {
            if (this.notifications.add(key, notification) === false) {
                return;
            }

            notification.isNew = true;
            this.notifiers.desktop.notify(this.notifications.all());
            this.notifiers.phone.notify(notification);

            notification.isNew = false;
            let self = this;
            setTimeout(function () {
                self.notifiers.desktop.notify(self.notifications.all());
                self.notifiers.phone.notify(notification);
            }, 50);
        };

        this.remove = function (key) {
            let notification = this.notifications.one(key);
            if (notification === null) {
                return;
            }
            notification.isDeleted = true;
            this.notifiers.desktop.notify(this.notifications.all());
            if (this.notifiers.phone.isEqual(notification.key)) {
                this.notifiers.phone.notify(notification);
            }

            this.notifications.remove(key);
            let self = this;
            setTimeout(function () {
                self.notifiers.desktop.notify(self.notifications.all());
                if (self.notifiers.phone.isEqual(notification.key)) {
                    self.notifiers.phone.reset();
                }
                if (self.notifications.count() === 0) {
                    self.notifiers.desktop.reset();
                }
            }, 400);
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
        };

        this.isEqual = function (key) {
            return this.notificationKey === key;
        };
    }

    window.phoneWidget.notifier = new Notifier();
})();
