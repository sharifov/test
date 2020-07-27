var PhoneWidgetContactInfo = function () {

    let containerId = 'contact-info';
    let $container = $('#contact-info');
    let $reactContainer = document.getElementById(containerId);

    /*
        data = {
            name
        }
     */
    function load(data) {
        data.avatar = data.name.charAt(0);
        data.avatar.toUpperCase();

        ReactDOM.render(React.createElement(ContactInfo, data), $reactContainer);
    }

    function hide() {
        $container.hide();
    }

    return {
        load: load,
        hide: hide
    }
}();
