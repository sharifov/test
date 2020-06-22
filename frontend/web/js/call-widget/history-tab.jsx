
class HistoryTab extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            date: new Date(),
            error: null,
            isLoaded: false,
            items: []
        };
    }

    componentDidMount() {
        fetch("/call/react-init-call-widget")
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        isLoaded: true,
                        items: result.calls
                    });
                },
                (error) => {
                    this.setState({
                        isLoaded: true,
                        error
                    });
                }
            )
    }

    componentWillUnmount() {
    }

    render() {
        const { error, isLoaded, items } = this.state;

        if (error) {
            return <div>Error: {error.message}</div>;
        } else if (!isLoaded) {
            return <div>Loading...</div>;
        } else {
            return (
                <div>
                    <h3>Last 3 Calls</h3>
                <ul>
                    {items.map(item => (
                        <li key={item.c_id}>
                            {item.c_id} {item.c_from}
                        </li>
                    ))}
                </ul>
                </div>
            );
        }


    }
}