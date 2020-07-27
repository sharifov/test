<?php
//\frontend\assets\TimerAsset::register($this);
\frontend\assets\CallWidgetAsset::register($this);
?>

<div id="call-widget-block"></div>

<script type = "text/babel">

    class CallWidget extends React.Component {
        render() {
            return (
                <div className="col-md-6">
                    <CallTab/>
                    <hr/>
                    <ContactsTab/>
                    <hr/>
                    <HistoryTab/>
                </div>
            );
        }
    }

    ReactDOM.render(
        <CallWidget />,
        document.getElementById('call-widget-block')
    );
</script>