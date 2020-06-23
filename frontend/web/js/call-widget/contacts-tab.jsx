
class ContactsTab extends React.Component {
    constructor(props) {
        super(props);
        this.state = {date: new Date()};
    }

    componentDidMount() {
    }

    componentWillUnmount() {
    }

    render() {
        return (
            <div>
                <h1>Contacts Tab init!</h1>
                <h2>Now {this.state.date.toLocaleTimeString()}.</h2>
            </div>
        );
    }
}