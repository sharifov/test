function ContactInfo(props) {
    return (
        <React.Fragment>
            <div className="additional-info__header">
                <div className="agent-text-avatar"><span>{props.avatar}</span></div>
                <ContactInfoHeader {...props}/>
                <a href="#" className="additional-info__close"><i className="fas fa-times"> </i></a>
            </div>
            <div className="additional-info__body scrollable-block">
                <ul className="info-listing incoming-info">
                    <li>
                        <small className="incoming-info__label">Name</small>
                        <span className="incoming-info__value">{props.name}</span>
                    </li>
                </ul>
            </div>
        </React.Fragment>
    );
}

function ContactInfoHeader(props) {
    if (!props.canContactDetails && !props.canCallInfo) {
        return (
            <span className="additional-info__header-title">{props.isClient ? 'Client details': 'Contact info'}</span>
        );
    }

    return (
        <React.Fragment>
            {props.canContactDetails && props.id
                ? <a href="#" data-client-id={props.id} data-is-client={props.isClient} className="cw-call-contact-info cw-btn-client-info"><span className="additional-info__header-title">{props.isClient ? 'Client details': 'Contact info'}</span></a>
                : <span className="additional-info__header-title">{props.isClient ? 'Client details': 'Contact info'}</span>
            }
            {props.canCallInfo
                ? <React.Fragment> &nbsp;/&nbsp; <a href="#" data-call-sid={props.callSid} className="cw-call-contact-info cw-btn-call-info"><span className="additional-info__header-title">Call info</span></a> </React.Fragment>
                : ''
            }
        </React.Fragment>
    );
}
