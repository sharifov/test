class DesktopNotification extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        let items = this.props.notifications.map((item) => <NotificationItem key={item.key} item={item} source='desktop'/>);
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
        if (!(this.props.notification.isDeleted || this.props.notification.isNew)) {
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
                    <NotificationItem key={this.props.notification.key} item={this.props.notification} source='phone'/>
                </ul>
            </div>
        );
    }
}

function NotificationItem(props) {
    let item = props.item;

    let duration = item.duration || 0;
    if (item.timePushed) {
        duration =  Math.floor((Date.now() - parseInt(item.timePushed)) / 1000) + parseInt(duration);
    }

    let info = '';
    if (item.project && item.department) {
        info =
            <div className="incoming-notification__project">
                <b className="incoming-notification__project-name">{item.project}</b>
                <i>•</i>
                <span className="incoming-notification__position">{item.department}</span>
            </div>;
    } else if (item.project) {
        info =
            <div className="incoming-notification__project">
                <b className="incoming-notification__project-name">{item.project}</b>
            </div>;
    } else if (item.department) {
        info =
            <div className="incoming-notification__project">
                <span className="incoming-notification__position">{item.department}</span>
            </div>;
    }

    return (
        <li className={(item.isDeleted || item.isNew) ? 'phone-notifications__item' : 'phone-notifications__item phone-notifications__item--shown'}>
            <div className="incoming-notification">
                <i className="user-icn">{item.name[0]}</i>
                <div className="incoming-notification__inner">
                    <div className="incoming-notification__info">
                        <div className="incoming-notification__general-info">
                            <b className="incoming-notification__name">{item.name}</b>
                            <span className="incoming-notification__phone">{item.phone}</span>
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

                            {item.canCallInfo
                                ? <a href="#" data-call-sid={item.callSid} className="incoming-notification__action incoming-notification__action--info pw-btn-call-info">
                                     <i className="fa fa-info"> </i>
                                  </a>
                                : ''
                            }

                            <a href="#"
                               className="incoming-notification__action incoming-notification__action--phone">
                                <i className="fa fa-phone"> </i>
                            </a>
                        </div>
                        {props.source === 'phone'
                            ?   <a href="#" data-call-sid={item.key} className="incoming-notification__action incoming-notification__action--min pw-notification-hide">
                                    <i className="fa fa-long-arrow-alt-down"> </i>
                                </a>
                            :   <a href="#" data-call-sid={item.key} className="incoming-notification__action incoming-notification__action--max pw-notification-hide">
                                    <i className="fa fa-long-arrow-down"> </i>
                                </a>
                        }
                    </div>
                </div>
            </div>
        </li>
    );
}
