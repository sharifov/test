function CallInfo(props) {
    return (
        <div className="calling-from-info">
            <div className="static-number-indicator">
                {props.projectName
                    ? <span className="static-number-indicator__label">{props.projectName}</span>
                    : ''
                }
                {props.projectName && props.sourceName
                    ? <i className="static-number-indicator__separator"> </i>
                    : ''
                }
                {props.sourceName
                    ? <span className="static-number-indicator__name">{props.sourceName}</span>
                    : ''
                }
            </div>
        </div>
    );
}
