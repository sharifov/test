class AcceptedPane extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            call: props.call
        }
    }

    render() {
        let call = this.state.call;
        return (
            <React.Fragment>
                <CallInfo project={call.project} source={call.source}/>
                <div className="contact-info-card">
                    <div className="contact-info-card__details">
                        <div className="contact-info-card__line history-details">
                            <span className="contact-info-card__label">{call.type}</span>
                            <div className="credential">
                                <div className="contact-info-card__name">
                                    <i className="user-icon fa fa-user"> </i>&nbsp;
                                    <strong>{call.contact.name}</strong>
                                </div>
                                <div className="contact-info-card__phone">
                                    <span> </span>
                                </div>
                            </div>
                        </div>
                        <div className="contact-info-card__line history-details">
                            <span className="contact-info-card__call-type">{call.contact.phone}</span>
                        </div>
                    </div>
                </div>
            </React.Fragment>
        );
    }
}
