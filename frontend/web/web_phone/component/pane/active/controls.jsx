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
        <li className="in-call-controls__item" data-mode={props.call.data.isHold ? 'hold' : 'unhold'} id="wg-hold-call"
             data-call-sid={props.call.data.callSid} data-active={props.controls.hold.active}>
            <a href="#" className="in-call-controls__action">
                {props.call.isSentHoldUnHoldRequestState()
                    ?  <i className="fa fa-spinner fa-spin"> </i>
                    : props.call.data.isHold
                        ? <i className="fa fa-play"> </i>
                        : <i className="fa fa-pause"> </i>
                }
                <span>{props.call.data.isHold ? 'UnHold' : 'Hold'}</span>
            </a>
        </li>
    );
}

function ButtonTransfer(props) {
    return (
        <li className="in-call-controls__item wg-transfer-call" data-call-sid={props.call.data.callSid} data-active={props.controls.transfer.active}>
            <a href="#" className="in-call-controls__action">
                <i className="fa fa-random"> </i>
                <span>Transfer Call</span>
            </a>
        </li>
    );
}

function ButtonAddPerson(props) {
    return (
        <li className="in-call-controls__item" id="wg-add-person" data-active={props.controls.addPerson.active}>
            <a href="#" className="in-call-controls__action js-add-to-conference" data-toggle-tab="tab-contacts">
                <i className="fa fa-plus"> </i>
                <span>Add Person</span>
            </a>
        </li>
    );
}

function ButtonDialpad(props) {
    return (
        <li className="in-call-controls__item" id="wg-dialpad" data-active={props.controls.dialpad.active}>
            <a href="#" className="in-call-controls__action js-toggle-dial">
                <i className="fa fa-th"> </i>
                <span>Dialpad</span>
            </a>
        </li>
    );
}

function ButtonAddNote(props) {
    return(
        <li className="in-call-controls__item" id="wg-add-note">
            <a href="#" className="in-call-controls__action add-note-action">
                <i className="fa fa-newspaper-o"> </i>
                <span>Add Note</span>
            </a>
        </li>
    );
}