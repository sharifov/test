class DesktopNotification extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        let items = this.props.notifications.map((notification) => <NotificationItem key={notification.key} notification={notification} source='desktop'/>);
        return (
            <div className="phone-notifications">
                <ul className="phone-notifications__list">
                    {items}
                </ul>
            </div>
        );
    }
}

class PhoneWidgetNotification extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        let className = 'phone-notifications';
        if (!(this.props.notification.isDeleted || this.props.notification.isNew || this.props.notification.willHide)) {
            className = className + ' phone-notifications--shown';
        }
        if (this.props.notification.queue === 'general') {
            className = className + ' phone-notifications--general-calls';
        } else if (this.props.notification.queue === 'direct') {
            className = className + ' phone-notifications--direct-calls';
        } else if (this.props.notification.queue === 'active' || this.props.notification.queue === 'hold') {
            className = className + ' phone-notifications--active-calls';
        }

        return (
            <div className={className}>
                <ul className="phone-notifications__list">
                    <NotificationItem key={this.props.notification.key} notification={this.props.notification} source='phone'/>
                </ul>
            </div>
        );
    }
}

function NotificationItem(props) {
    if (props.notification.type === window.phoneWidget.notifier.types.incomingCall) {
        return <IncomingCallNotificationItem notification={props.notification} source={props.source}/>
    } else if (props.notification.type === window.phoneWidget.notifier.types.priorityCall) {
        return <PriorityCallNotificationItem notification={props.notification} source={props.source}/>
    }
    return null;
}

class PriorityCallNotificationItem extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            notification: props.notification
        };
    }

    componentDidMount() {
        if (this.props.source === 'phone') {
            window.phoneWidget.eventDispatcher.addListener(this.state.notification.eventName, this.phoneUpdateHandler());
        } else {
            window.phoneWidget.eventDispatcher.addListener(this.state.notification.eventName, this.desktopUpdateHandler());
        }
    }

    componentWillUnmount() {
        if (this.props.source === 'phone') {
            window.phoneWidget.eventDispatcher.removeListener(this.state.notification.eventName, this.phoneUpdateHandler());
        } else {
            window.phoneWidget.eventDispatcher.removeListener(this.state.notification.eventName, this.desktopUpdateHandler());
        }
    }

    phoneUpdateHandler() {
        let self = this;
        return function (event) {
            //only for minify version
            window.phoneWidget.emptyFunc('PriorityCallNotificationItem.phoneUpdateHandler');

            let notification = self.state.notification;

            notification.isSentAcceptCallRequestState = event.isSentAcceptCallRequestState;

            self.setState({
                notification: notification
            });
        }
    }

    desktopUpdateHandler() {
        let self = this;
        return function (event) {
            //only for minify version
            window.phoneWidget.emptyFunc('PriorityCallNotificationItem.desktopUpdateHandler');

            let notification = self.state.notification;

            notification.isSentAcceptCallRequestState = event.isSentAcceptCallRequestState;

            self.setState({
                notification: notification
            });
        }
    }

    render () {
        let notification = this.state.notification;

        let info =
            <div className="incoming-notification__project">
                <b className="incoming-notification__position" style={{'fontSize':'14px'}}>Priority call </b>
            </div>;

        return (
            <li className={(notification.isDeleted || notification.isNew || notification.willHide) ? 'phone-notifications__item' : 'phone-notifications__item phone-notifications__item--shown'}>
                <div className="incoming-notification">
                    <div className="incoming-notification__inner">
                        <div className="incoming-notification__info">
                            <div className="incoming-notification__general-info">
                                <b className="incoming-notification__name">&nbsp;</b>
                                {info}
                                <span className="incoming-notification__phone">&nbsp;</span>
                            </div>
                        </div>
                        <div className="incoming-notification__action-list">
                            <div className="incoming-notification__dynamic">
                                 <a href="#" className="incoming-notification__action incoming-notification__action--phone btn-item-call-priority">
                                     {notification.isSentAcceptCallRequestState
                                        ? < i className="fa fa-spinner fa-spin"> </i>
                                        : < i className="fa fa-phone"> </i>
                                     }
                                </a>
                            </div>
                            {this.props.source === 'phone'
                                ? <a href="#" data-key={notification.key}
                                     className="incoming-notification__action incoming-notification__action--min pw-notification-hide">
                                    <i className="fa fa-long-arrow-alt-down"> </i>
                                </a>
                                : <a href="#" data-key={notification.key}
                                     className="incoming-notification__action incoming-notification__action--max pw-notification-hide">
                                    <i className="fa fa-long-arrow-down"> </i>
                                </a>
                            }
                        </div>
                    </div>
                </div>
            </li>
        );
    }
}

class IncomingCallNotificationItem extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            notification: props.notification
        };
    }

    componentDidMount() {
        if (this.props.source === 'phone') {
            window.phoneWidget.eventDispatcher.addListener(this.state.notification.eventName, this.phoneUpdateHandler());
        } else {
            window.phoneWidget.eventDispatcher.addListener(this.state.notification.eventName, this.desktopUpdateHandler());
        }
    }

    componentWillUnmount() {
        if (this.props.source === 'phone') {
            window.phoneWidget.eventDispatcher.removeListener(this.state.notification.eventName, this.phoneUpdateHandler());
        } else {
            window.phoneWidget.eventDispatcher.removeListener(this.state.notification.eventName, this.desktopUpdateHandler());
        }
    }

    phoneUpdateHandler() {
        let self = this;
        return function (event) {
            //only for minify version
            window.phoneWidget.emptyFunc('IncomingCallNotificationItem.phoneUpdateHandler');

            let call = event.call;
            let notification = self.state.notification;

            notification.isSentAcceptCallRequestState = call.isSentAcceptCallRequestState();
            notification.isSentReturnHoldCallRequestState = call.isSentReturnHoldCallRequestState();

            self.setState({
                notification: notification
            });
        }
    }

    desktopUpdateHandler() {
        let self = this;
        return function (event) {
            //only for minify version
            window.phoneWidget.emptyFunc('IncomingCallNotificationItem.desktopUpdateHandler');

            let call = event.call;
            let notification = self.state.notification;

            notification.isSentAcceptCallRequestState = call.isSentAcceptCallRequestState();
            notification.isSentReturnHoldCallRequestState = call.isSentReturnHoldCallRequestState();

            self.setState({
                notification: notification
            });
        }
    }

    render () {
        let notification = this.state.notification;

        let duration = notification.duration || 0;
        if (notification.timePushed) {
            duration = Math.floor((Date.now() - parseInt(notification.timePushed)) / 1000) + parseInt(duration);
        }

        let info = '';
        if (notification.project && notification.department) {
            info =
                <div className="incoming-notification__project">
                    <b className="incoming-notification__project-name">{notification.project}</b>
                    <i>•</i>
                    <span className="incoming-notification__position">{notification.department}</span>
                </div>;
        } else if (notification.project) {
            info =
                <div className="incoming-notification__project">
                    <b className="incoming-notification__project-name">{notification.project}</b>
                </div>;
        } else if (notification.department) {
            info =
                <div className="incoming-notification__project">
                    <span className="incoming-notification__position">{notification.department}</span>
                </div>;
        } else if (notification.isInternal) {
            info =
                <div className="incoming-notification__project">
                    <span className="incoming-notification__position">Internal</span>
                </div>;
        }

        return (
            <li className={(notification.isDeleted || notification.isNew || notification.willHide) ? 'phone-notifications__item' : 'phone-notifications__item phone-notifications__item--shown'}>
                <div className="incoming-notification">
                    <i className="user-icn">{notification.name[0]}</i>
                    <div className="incoming-notification__inner">
                        <div className="incoming-notification__info">
                            <div className="incoming-notification__general-info">
                                <b className="incoming-notification__name">{notification.name}</b>
                                <span className="incoming-notification__phone">{notification.phone}</span>
                                {info}
                            </div>
                            <div className="incoming-notification__timer">
                                <span><PhoneWidgetTimer duration={duration} timeStart={Date.now()}/></span>
                            </div>
                        </div>
                        <div className="incoming-notification__action-list">
                            <div className="incoming-notification__dynamic">
                                {/* <a href="#"
                               className="incoming-notification__action incoming-notification__action--line">
                                <i className="fa fa-random"> </i>
                                 </a> */}

                                {/*notification.canCallInfo
                                    ? <a href="#" data-call-sid={notification.callSid}
                                         className="incoming-notification__action incoming-notification__action--info pw-btn-call-info">
                                        <i className="fa fa-info"> </i>
                                    </a>
                                    : ''
                                */}
                                 <a href="#"
                                    className="incoming-notification__action incoming-notification__action--phone btn-item-call-queue"
                                    data-type-action={notification.queue === 'hold' ? 'return' : (notification.isInternal ? 'acceptInternal' :'accept')}
                                    data-call-sid={notification.callSid}
                                    data-from-internal={notification.fromInternal}
                                 >
                                     {notification.isSentAcceptCallRequestState || notification.isSentReturnHoldCallRequestState
                                        ? < i className="fa fa-spinner fa-spin"> </i>
                                        : < i className="fa fa-phone"> </i>
                                     }
                                </a>
                            </div>
                            {this.props.source === 'phone'
                                ? <a href="#" data-key={notification.key}
                                     className="incoming-notification__action incoming-notification__action--min pw-notification-hide">
                                    <i className="fa fa-long-arrow-alt-down"> </i>
                                </a>
                                : <a href="#" data-key={notification.key}
                                     className="incoming-notification__action incoming-notification__action--max pw-notification-hide">
                                    <i className="fa fa-long-arrow-down"> </i>
                                </a>
                            }
                        </div>
                    </div>
                </div>
            </li>
        );
    }
}
