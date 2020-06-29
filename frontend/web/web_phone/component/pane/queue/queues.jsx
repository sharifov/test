function AllQueues(props) {
    return (
        <React.Fragment>
            {props.queues.active.count() > 0
                ? <QueueItem groups={props.queues.active.all()} name="Active" type="active"/>
                : ''
            }
            {props.queues.hold.count() > 0
                ? <QueueItem groups={props.queues.hold.all()} name="On Hold" type="hold"/>
                : ''
            }
            {props.queues.direct.count() > 0
                ? <QueueItem groups={props.queues.direct.all()} name="Direct Calls" type="direct"/>
                : ''
            }
            {props.queues.general.count() > 0
                ? <QueueItem groups={props.queues.general.all()} name="General Lines" type="general"/>
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
            <Groups groups={props.groups}/>
        </li>
    );
}
