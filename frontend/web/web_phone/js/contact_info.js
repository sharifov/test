var PhoneWidgetContactInfo = function () {

    let $pane = $('.contact-info');

    function render(data) {
        let html = '';
        let template = contactTpl;
        $.each(data, function (k, v) {
            html = template.split('{{' + k + '}}').join(v);
            template = html;
        });
        return html;
    }

    /*
        data = {
            name
        }
     */
    function load(data) {
        data.avatar = data.name.charAt(0);
        data.avatar.toUpperCase();
        let html = render(data);
        $pane.html(html);
    }

    function hide() {
        $pane.hide();
    }

    return {
        load: load,
        hide: hide
    }
}();
