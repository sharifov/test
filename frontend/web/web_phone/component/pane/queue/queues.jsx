function AllQueues(props) {
    return (
        <React.Fragment>
            {countProperties(props.active) > 0
                ? <QueueItem groups={props.active} name="Active Calls" type="active"/>
                : ''
            }
            {countProperties(props.direct) > 0
                ? <QueueItem groups={props.direct} name="Direct Line" type="direct"/>
                : ''
            }
            {countProperties(props.general) > 0 || props.countPriority > 0
                ? <QueueItem groups={props.general} name="General Line" type="general" countPriority={props.countPriority}/>
                : ''
            }
        </React.Fragment>
    );
}

function QueueItem(props) {
    return (
        <li className="queue-separator__item" data-queue-type={props.type}>
            {props.name
                ? <div className="queue-separator__name">{props.name}</div>
                : ''
            }
            {props.countPriority > 0
                ? <PriorityItem count={props.countPriority}/>
                : ''
            }
            <Groups groups={props.groups}/>
        </li>
    );
}

function countProperties(obj) {
    let count = 0;

    for (let prop in obj) {
        if (obj.hasOwnProperty(prop)) {
            ++count;
        }
    }

    return count;
}
