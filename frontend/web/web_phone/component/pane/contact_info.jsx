function ContactInfo(props) {
    return (
        <React.Fragment>
            <div className="additional-info__header">
                <div className="agent-text-avatar"><span>{props.avatar}</span></div>
                <span className="additional-info__header-title">Contact Info</span>
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
