(function (){
    function Names() {
        this.phone = function (userId) {
            return 'PhoneDeviceStatus' + userId;
        };

        this.twilio = function (userId) {
            return 'PhoneDeviceTwilioStatus' + userId;
        };

        this.speaker = function(userId) {
            return 'PhoneDeviceSpeakerStatus' + userId;
        };

        this.microphone = function (userId) {
            return 'PhoneDeviceMicrophoneStatus' + userId;
        };
    }

    window.phoneWidget.device.state.localNames = new Names();
})();
