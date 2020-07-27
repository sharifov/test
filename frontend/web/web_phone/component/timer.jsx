class PhoneWidgetTimer extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            duration: 0,
            timeStart: 0
        };
    }

    componentDidMount() {
        this.startTimer();
    }

    componentDidUpdate(prevProps) {
        if (this.state.timeStart !== this.props.timeStart) {
            this.startTimer();
        }
    }

    componentWillUnmount() {
        clearInterval(this.timer);
    }

    startTimer() {
        clearInterval(this.timer);
        this.setState({
            duration: this.props.duration,
            timeStart: this.props.timeStart
        });
        this.timer = setInterval(() => this.setState({
            duration: this.state.duration + 1
        }), 1000);
    }

    formatDuration(duration) {
        let out = '';
        let hours = Math.floor(duration / 60 / 60);
        let minutes = Math.floor(duration / 60) - (hours * 60);
        let seconds = duration % 60;
        if (hours > 0) {
            out = hours.toString().padStart(2, '0') + ':';
        }
        out += minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
        return out;
    }

    render() {
        if (this.props.styleClass && this.state.duration > 3599) {
            return <span className={this.props.styleClass}>{this.formatDuration(this.state.duration)}</span>;
        }
        return this.formatDuration(this.state.duration);
    }
}
