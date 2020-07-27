function GroupItem(props) {
    return (
        <li className="calls-separator__list-item">
            <div className="static-number-indicator">
                {props.group.project
                    ? <span className="static-number-indicator__label">{props.group.project}</span>
                    : ''
                }
                {props.group.project && props.group.department
                    ? <i className="static-number-indicator__separator"> </i>
                    : ''
                }
                {props.group.department
                    ? <span className="static-number-indicator__name">{props.group.department}</span>
                    : ''
                }
            </div>
            <ul className="call-in-progress">
                {props.group.calls.map((call) =>
                    <ListItem key={call.data.callSid} call={call}/>
                )}
            </ul>
        </li>
    );
}
