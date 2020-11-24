function CallInfo(props) {
    return (
        <div className="calling-from-info">
            <div className="static-number-indicator">
                {props.project
                    ? <span className="static-number-indicator__label">{props.project}</span>
                    : ''
                }
                {props.project && props.source
                    ? <i className="static-number-indicator__separator"> </i>
                    : ''
                }
                {props.source
                    ? <span className="static-number-indicator__name">{props.source}</span>
                    : ''
                }
            </div>
        </div>
    );
}
