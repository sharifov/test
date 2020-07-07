class ConferencePane extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            call: props.call,
            conference: props.conference
        };
    }

    componentDidMount() {
        window.phoneWidget.eventDispatcher.addListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
    }

    componentWillUnmount() {
        window.phoneWidget.eventDispatcher.removeListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
    }

    callUpdateHandler() {
        let self = this;
        return function (event) {
            //conference
            self.setState({
                call: event.call
            });
        }
    }

    render() {
        let call = this.state.call;
        let conference = this.state.conference;
        let countParticipants = conference.getCountParticipants();

        return (
            <React.Fragment>

                <div className="call-details">
                    <div className="call-details__col">
                            <span className="call-details__participants"><i
                                className="call-details__participants-len">{countParticipants}</i> Participants</span>
                    </div>
                    <div className="call-details__col">
                        <a href="#" className="call-details__nav-btn call-details__nav-btn--more">Details</a>
                    </div>
                </div>

                <CallInfo project={call.data.project} source={call.data.source}/>

                <ParticipantShortList conference={conference}/>

                <div className="actions-container">

                    <CallBtns call={call}/>

                    <SoundIndication/>

                </div>

                <ul className="in-call-controls">
                    <ButtonHold call={call} controls={this.props.controls}/>
                    <ButtonTransfer call={call} controls={this.props.controls}/>
                    <ButtonAddPerson  call={call} controls={this.props.controls}/>
                    <ButtonDialpad  call={call} controls={this.props.controls}/>
                    <ButtonAddNote  call={call} controls={this.props.controls}/>
                </ul>

                <div className="conference-call-details">

                    <div className="call-details">
                        <div className="call-details__col">
                            <a href="#" className="call-details__nav-btn call-details__nav-btn--back">Back</a>
                        </div>
                        <div className="call-details__col">
                            <span className="call-details__participants"><i
                                className="fa fa-users"> </i> {countParticipants}</span>
                        </div>
                        <div className="call-details__col">
                            <span className="call-details__time"><PhoneWidgetTimer duration={conference.getDuration()} timeStart={Date.now()}/></span>
                        </div>
                    </div>

                    <CallInfo project={call.data.project} source={call.data.source}/>

                    <ParticipantList conference={conference}/>

                </div>

            </React.Fragment>
        );
    }
}

function ParticipantList(props) {
    let conference = props.conference;
    let participants = [];
    for (let participant of conference.getParticipants()) {
        participants.push(<ParticipantItem key={participant.data.callSid} participant={participant}/>);
    }
    return (
        <ul className="conference-call__detailed participant-list scrollable-block">
            {participants}
        </ul>
    );
}

function ParticipantItem(props) {
    let participant = props.participant;

    let className = 'participant-list__item participant';
    if (participant.data.type === 'coaching') {
        className += ' participant-list__item--coach';
    }

    return (
        <li className={className}>
            <div className="participant__avatar">
                <span>{participant.data.avatar}</span>
            </div>
            <div className="participant__info">
                <span className="participant__name">{participant.data.name}</span>
                <span className="participant__phone">{participant.data.phone}</span>
                {participant.data.type === 'coaching'
                    ? <span className="participant__status"><i className="fa fa-headphones-alt"> </i> coaching</span>
                    : ''
                }
            </div>
            <div className="participant__action">
                <b className="participant__timer"><PhoneWidgetTimer duration={participant.getDuration()} timeStart={Date.now()}/></b>
                {/* {<a href="#" className="participant__more-action"><i className="fa fa-ellipsis-h"> </i></a> */}
            </div>
        </li>
    );
}

function ParticipantShortList(props) {
    let conference = props.conference;
    let participants = [];
    let countParticipants = conference.getCountParticipants();
    if (countParticipants > 10) {
        let count = 0;
        for (let participant of conference.getParticipants()) {
            participants.push(<ParticipantShortItem key={participant.data.callSid} participant={participant}/>);
            count++;
            if (count === 9) {
                break;
            }
        }
    } else {
        for (let participant of conference.getParticipants()) {
            participants.push(<ParticipantShortItem key={participant.data.callSid} participant={participant}/>);
        }
    }

    return (
        <div className="conference-call">
            <ul className="conference-call__list">
                {participants}
                {countParticipants > 10
                    ? <li>
                        <div className="conference-call__collapsed">
                            <span className="conference-call__name">and <i>{countParticipants - 9}</i> more...</span>
                        </div>
                    </li>
                    : ''
                }
            </ul>
        </div>
    );
}

function ParticipantShortItem(props) {
    let participant = props.participant;

    let className = 'conference-call__thumbnail';
    if (participant.data.type === 'coaching') {
        className += ' conference-call__thumbnail--coaching';
    }

    return (
        <li>
            <div className={className}>
                <div className="conference-call__avatar">{participant.data.avatar}</div>
                <span className="conference-call__name">{participant.data.name}</span>
                {participant.data.type === 'coaching'
                    ? <i className="conference-call__icon"><i className="fa fa-headphones-alt"> </i></i>
                    : ''
                }
            </div>
        </li>
    );
}
