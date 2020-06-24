function ActivePaneControls(props) {
    return (
        <ul className="in-call-controls">
            <ButtonHold {...props}/>
            <ButtonTransfer {...props}/>
            <ButtonAddPerson {...props}/>
            <ButtonDialpad {...props}/>
        </ul>
    );
}

function ButtonHold(props) {
    return (
        <li className="in-call-controls__item" data-mode={props.isHold ? 'hold' : 'unhold'} id="wg-hold-call"
            data-call-id={props.callId} data-active={props.activeControls}>
            <a href="#" className="in-call-controls__action">
                <i className="fa fa-pause"> </i>
                <span>Hold</span>
            </a>
        </li>
    );
}

function ButtonTransfer(props) {
    return (
        <li className="in-call-controls__item" id="wg-transfer-call" data-active={props.activeControls}>
            <a href="#" className="in-call-controls__action">
                <i className="fa fa-random"> </i>
                <span>Transfer Call</span>
            </a>
        </li>
    );
}

function ButtonAddPerson(props) {
    return (
        <li className="in-call-controls__item" id="wg-add-person" data-active={props.activeControls}>
            <a href="#" className="in-call-controls__action js-add-to-conference" data-toggle-tab="tab-contacts">
                <i className="fa fa-plus"> </i>
                <span>Add Person</span>
            </a>
        </li>
    );
}

function ButtonDialpad(props) {
    return (
        <li className="in-call-controls__item" id="wg-dialpad" data-active={props.activeControls}>
            <a href="#" className="in-call-controls__action js-toggle-dial">
                <i className="fa fa-th"> </i>
                <span>Dialpad</span>
            </a>
        </li>
    );
}
