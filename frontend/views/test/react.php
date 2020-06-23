<?php
use yii\helpers\Html;
//\frontend\assets\ReactAsset::register($this); // register ReactAsset

//$this->registerJsFile('/js/vue/call-widget.js', [
//    'position' => \yii\web\View::POS_END,
//    'depends' => [
//        \frontend\assets\VueAsset::class
//    ]
//]);

?>


<!--<div id="root"></div>-->
<!--<script type="text/babel">-->
<!---->
<!--    const element = (-->
<!--        <h1 className="greeting">-->
<!--            Hello, world фффффф!-->
<!--        </h1>-->
<!--    );-->
<!---->
<!---->
<!--    ReactDOM.render(-->
<!--        element,-->
<!--        document.getElementById('root')-->
<!--    );-->
<!---->
<!--</script>-->

<div id="like_button_container"></div>


<script>
    // 'use strict';
    //
    // const e = React.createElement;
    //
    // class LikeButton extends React.Component {
    //     constructor(props) {
    //         super(props);
    //         this.state = { liked: false };
    //     }
    //
    //     render() {
    //         if (this.state.liked) {
    //             return 'You liked this.';
    //         }
    //
    //         return e(
    //             'button',
    //             { onClick: () => this.setState({ liked: true }) },
    //             'Like'
    //         );
    //     }
    // }
    //
    // const domContainer = document.querySelector('#like_button_container');
    // ReactDOM.render(e(LikeButton), domContainer);
</script>

<?= frontend\widgets\CallWidget::widget() ?>