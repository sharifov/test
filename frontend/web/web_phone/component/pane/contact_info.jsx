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
                    <li>
                        <div className="d-flex align-items-center justify-content-between">
                            <small className="incoming-info__label" title="Active Leads / All Leads">Leads ({'countActiveLeads' in props ? props.countActiveLeads : 0} / {'countAllLeads' in props ? props.countAllLeads : 0})</small>
                            <CreateLeadButton {...props}/>
                        </div>
                        {props.leads.map((lead, i) => {
                            return (
                                <div className="d-flex align-items-center justify-content-between" style={{"marginTop": "3px"}} key={lead.id}>
                                    <span dangerouslySetInnerHTML={{__html: lead.formatHtml}} /> <span dangerouslySetInnerHTML={{__html: lead.status}} />
                                </div>
                            );
                        })}
                        {props.canContactDetails && props.id
                            ? <a href="#" data-client-id={props.id} data-is-client={props.isClient} className="cw-call-contact-info cw-btn-client-info"><span className="incoming-info__label">more...</span></a>
                            : ''
                        }
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
                ? <React.Fragment> &nbsp;/&nbsp; <a href="#" data-call-sid={props.callSid} className="cw-call-contact-info pw-btn-call-info"><span className="additional-info__header-title">Call info</span></a> </React.Fragment>
                : ''
            }
        </React.Fragment>
    );
}

function CreateLeadButton(props) {
    if (props.canCreateLead) {
        return (
            <a href="#" data-call-sid={props.callSid} className="cw-call-contact-info cw-btn-create-lead">
                <small className="incoming-info__label">Create Lead</small>
            </a>
        );
    }
    return ('');
}
