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
                                <div className="contact-info-card__phone"> </div>
                            </div>
                        </div>
                        <div className="contact-info-card__line history-details">
                            <span className="contact-info-card__call-type">{call.phone}</span>
                        </div>
                    </div>
                </div>
            </React.Fragment>
        );
    }
}
