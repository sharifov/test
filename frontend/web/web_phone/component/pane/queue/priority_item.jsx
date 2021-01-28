class PriorityItem extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            isSentAcceptCallRequestState: false
        };
    }

    componentDidMount() {
        window.phoneWidget.eventDispatcher.addListener(window.phoneWidget.events.priorityCallUpdate, this.priorityCallUpdateHandler());
    }

    componentWillUnmount() {
        window.phoneWidget.eventDispatcher.removeListener(window.phoneWidget.events.priorityCallUpdate, this.priorityCallUpdateHandler());
    }

    priorityCallUpdateHandler() {
        let self = this;
        return function (event) {
            //only for minify version
            window.phoneWidget.emptyFunc('PriorityItem.callUpdateHandler');

            self.setState({
                isSentAcceptCallRequestState: event.isSentAcceptCallRequestState
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
                                <li className="call-info-list__item"><span className="call-info-list__number">Priority call</span> </li>
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