const { series, src, dest } = require('gulp');
const concat = require('gulp-concat');
const sass = require('gulp-sass');
const uglify = require('gulp-uglify');
const minify = require('gulp-minify');
const babel = require('gulp-babel');
const cleanCSS = require('gulp-clean-css');
// const rename = require('gulp-rename');


// gulp.task('watch', function(){
//     gulp.watch('frontend/web/js/test/*.js', ['min-js']);
//     // Other watchers
// });


function clean(cb) {
    // body omitted
    cb();
}

function build_phone_widget(cb) {
    src([

       'frontend/web/web_phone/js/init.js',
       'frontend/web/web_phone/js/event_dispatcher.js',
       'frontend/web/web_phone/js/events.js',
       'frontend/web/web_phone/js/call_object.js',
       'frontend/web/web_phone/js/conference_object.js',
       'frontend/web/web_phone/js/requesters.js',
       'frontend/web/web_phone/js/old_widget.js',

       'frontend/web/web_phone/component/timer.jsx',
       'frontend/web/web_phone/component/pane/call_action_timer.jsx',
       'frontend/web/web_phone/component/pane/active/pane.jsx',
       'frontend/web/web_phone/component/pane/incoming/pane.jsx',
       'frontend/web/web_phone/component/pane/active/controls.jsx',
       'frontend/web/web_phone/component/pane/call_info.jsx',
       'frontend/web/web_phone/component/pane/queue/list_item.jsx',
       'frontend/web/web_phone/component/pane/queue/groups.jsx',
       'frontend/web/web_phone/component/pane/queue/group_item.jsx',
       'frontend/web/web_phone/component/pane/queue/queues.jsx',
       'frontend/web/web_phone/component/pane/outgoing/pane.jsx',
       'frontend/web/web_phone/component/pane/contact_info.jsx',
       'frontend/web/web_phone/component/pane/conference/pane.jsx',
       'frontend/web/web_phone/component/pane/add_note.jsx',
       'frontend/web/web_phone/component/notification/notifications.jsx',

       'frontend/web/web_phone/js/contact_info.js',
       'frontend/web/web_phone/js/dialpad.js',

       'frontend/web/web_phone/js/pane/active/btn/btn.js',
       'frontend/web/web_phone/js/pane/active/btn/hold.js',
       'frontend/web/web_phone/js/pane/active/btn/mute.js',

       'frontend/web/web_phone/js/pane/active/pane.js',
       'frontend/web/web_phone/js/pane/incoming/pane.js',
       'frontend/web/web_phone/js/pane/outgoing/pane.js',
       'frontend/web/web_phone/js/pane/queue/pane.js',

       'frontend/web/web_phone/js/queue/queue.js',
       'frontend/web/web_phone/js/storage/conference.js',

       'frontend/web/web_phone/js/notifier/notifier.js',

       'frontend/web/web_phone/js/phone-widget.js',
		
	   'frontend/web/web_phone/js/status.js',
	   'frontend/web/web_phone/js/call.js',
	   'frontend/web/web_phone/js/sms.js',
	   'frontend/web/web_phone/js/contacts.js',
	   'frontend/web/web_phone/js/email.js',
    ])
        // The gulp-uglify plugin won't update the filename
        .pipe(concat('widget.js'))
        .pipe(babel({
            plugins: ['transform-react-jsx']
        }))
        .pipe(minify())
        // .pipe(uglify().on('error', function(err) { console.error(err)}))
        // So use gulp-rename to change the extension
        // .pipe(rename({ extname: '.min.js' }))
        .pipe(dest('frontend/web/web_phone'));

    src([
       'frontend/web/css/style-web-phone-new.css',
       'frontend/web/css/additional-styles.css',
    ])
        .pipe(concat('widget.css'))
        .pipe(cleanCSS())
        .pipe(dest('frontend/web/web_phone'));

    cb();
}

exports.build_phone_widget = build_phone_widget;
exports.default = series(clean, build_phone_widget);