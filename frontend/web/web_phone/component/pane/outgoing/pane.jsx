function OutgoingPane(props) {
    let call = props.call;
    return (
        <React.Fragment>
            <CallInfo project={call.data.project} source={call.data.source}/>
            <div className="incall-group">
                <div className="contact-info-card">
                    <div className="contact-info-card__details">
                        <div className="contact-info-card__line history-details">
                            <span className="contact-info-card__label">{call.data.type}</span>
                            <div className="contact-info-card__name">
                                <button className="call-pane__info">
                                    <i className="user-icon fa fa-user"> </i>
                                    <i className="info-icon fa fa-info"> </i>
                                </button>
                                <strong>{call.data.contact.name}</strong>
                            </div>
                        </div>
                        <div className="contact-info-card__line history-details">
                            <span className="contact-info-card__call-type">{call.data.contact.phone}</span>
                        </div>
                    </div>
                </div>
                <div className="call-pane__call-btns is-pending">
                    <button className="call-pane__start-call calling-state-block">
                        <div className="call-in-action">
                            <span className="call-in-action__text">{call.data.status}</span>
                            <CallActionTimer duration={call.getDuration()} timeStart={Date.now()}/>
                        </div>
                        <i className="fas fa-phone"> </i>
                    </button>
                    <button className="call-pane__end-call" id="cancel-outgoing-call" data-call-sid={call.data.callSid}>
                        <i className="fa fa-phone-slash"> </i>
                    </button>
                </div>
            </div>
        </React.Fragment>
    );
}
