class PriorityItem extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            isSentAcceptCallRequestState: false,
            count: props.count
        };
    }

    componentDidMount() {
        window.phoneWidget.eventDispatcher.addListener(window.phoneWidget.events.priorityQueueAccepted, this.priorityQueueAcceptedHandler());
        window.phoneWidget.eventDispatcher.addListener(window.phoneWidget.events.priorityQueueCounterChanged, this.priorityQueueCounterChangedHandler());
    }

    componentWillUnmount() {
        window.phoneWidget.eventDispatcher.removeListener(window.phoneWidget.events.priorityQueueAccepted, this.priorityQueueAcceptedHandler());
        window.phoneWidget.eventDispatcher.removeListener(window.phoneWidget.events.priorityQueueCounterChanged, this.priorityQueueCounterChangedHandler());
    }

    priorityQueueAcceptedHandler() {
        let self = this;
        return function (event) {
            //only for minify version
            window.phoneWidget.emptyFunc('PriorityItem.callUpdateHandler');

            self.setState({
                isSentAcceptCallRequestState: event.isSentAcceptCallRequestState
            });
        }
    }

    priorityQueueCounterChangedHandler() {
        let self = this;
        return function (event) {
            //only for minify version
            window.phoneWidget.emptyFunc('PriorityItem.priorityQueueCounterChangedHandler');

            self.setState({
                count: event.count
            });
        }
    }

    render() {
        return (
            <ul className="call-in-progress" style={{'marginBottom' : '15px'}}>
                <li className="call-in-progress__list-item">
                    <div className="call-in-progress__call-item call-list-item" >
                        <div className="call-list-item__info">
                            <ul className="call-list-item__info-list call-info-list">
                                <li className="call-info-list__item"><span className="call-info-list__number">General line call ({this.state.count})</span> </li>
                            </ul>
                            {/*<div className="call-list-item__info-action call-info-action">
                                <span className="call-info-action__timer">
                                    <PhoneWidgetTimer duration={0} timeStart={Date.now()}/>
                                </span>
                            </div>*/}
                        </div>
                        <div className="call-list-item__main-action">
                            <a href="#" className="call-list-item__main-action-trigger btn-item-call-priority">
                                {this.state.isSentAcceptCallRequestState
                                    ? <i className="fa fa-spinner fa-spin"/>
                                    : <React.Fragment><i className="phone-icon phone-icon--start fa fa-phone"/> <i
                                        className="phone-icon phone-icon--end fa fa-phone-slash"/> </React.Fragment>
                                }
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        );
    }
}