function GroupItem(props) {
    return (
        <li className="calls-separator__list-item">
            <div className="static-number-indicator">
                {props.group.projectName
                    ? <span className="static-number-indicator__label">{props.group.projectName}</span>
                    : ''
                }
                {props.group.projectName && props.group.departmentName
                    ? <i className="static-number-indicator__separator"> </i>
                    : ''
                }
                {props.group.departmentName
                    ? <span className="static-number-indicator__name">{props.group.departmentName}</span>
                    : ''
                }
            </div>
            <ul className="call-in-progress">
                {props.group.calls.map((call) =>
                    <ListItem key={call.callId} call={call}/>
                )}
            </ul>
        </li>
    );
}
