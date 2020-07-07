class AddNote extends  React.Component {
    constructor(props) {
        super(props);
        this.state = {
            call: props.call
        }
    }

    componentDidMount() {
        window.phoneWidget.eventDispatcher.addListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
    }

    componentWillUnmount() {
        window.phoneWidget.eventDispatcher.removeListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
    }

    callUpdateHandler() {
        let self = this;
        return function(event) {
            //add note
            self.setState({
                call: event.call
            });
        }
    }

    render() {
        let call = this.state.call;
        return (
            <React.Fragment>
                <div className="additional-info__header">
                    <span className="additional-info__header-title">Add Note</span>
                    <a href="#" className="additional-info__close">
                        <i className="fas fa-times"> </i>
                    </a>
                </div>
                <div className="additional-info__body scrollable-block add-note-block">
                    <textarea id="active_call_add_note" cols="30" rows="10" placeholder="Enter Note..." disabled={call.isSentAddNoteRequestState()} defaultValue="" />
                    <button id="active_call_add_note_submit" data-call-sid={call.data.callSid}
                            disabled={call.isSentAddNoteRequestState()}>
                        {call.isSentAddNoteRequestState()
                            ? <React.Fragment><i className="fa fa-spinner fa-spin"> </i> Save</React.Fragment>
                            : 'Save'
                        }

                    </button>
                </div>
            </React.Fragment>
        );
    }
}
