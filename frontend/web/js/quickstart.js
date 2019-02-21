"use strict";

var device;
var connection;

var speakerDevices = document.getElementById('speaker-devices');
var ringtoneDevices = document.getElementById('ringtone-devices');
var outputVolumeBar = document.getElementById('output-volume');
var inputVolumeBar = document.getElementById('input-volume');
var volumeIndicators = document.getElementById('volume-indicators');

if (!Array.prototype.inArray) {
    Array.prototype.inArray = function (element) {
        return this.indexOf(element) > -1;
    };
}

// Bind button to make call
document.getElementById('button-call').onclick = function () {
    // get the phone number to connect the call to
    var params = {
        To: document.getElementById('phone-number').value,
        FromAgentPhone: tw_configs.FromAgentPhone
    };

    console.log('Calling ' + params.To + '...');
    if (device) {
        device.connect(params);
    }
};

// Bind button to hangup call
document.getElementById('button-hangup').onclick = function () {
    log('Hanging up...');
    if (device) {
        device.disconnectAll();
    }
};

document.getElementById('get-devices').onclick = function () {
    navigator.mediaDevices.getUserMedia({audio: true})
        .then(updateAllDevices.bind(device));
}

speakerDevices.addEventListener('change', function () {
    var selectedDevices = [].slice.call(speakerDevices.children)
        .filter(function (node) {
            return node.selected;
        })
        .map(function (node) {
            return node.getAttribute('data-id');
        });

    device.audio.speakerDevices.set(selectedDevices);
});

ringtoneDevices.addEventListener('change', function () {
    var selectedDevices = [].slice.call(ringtoneDevices.children)
        .filter(function (node) {
            return node.selected;
        })
        .map(function (node) {
            return node.getAttribute('data-id');
        });

    device.audio.ringtoneDevices.set(selectedDevices);
});

function bindVolumeIndicators(connection) {
    connection.on('volume', function (inputVolume, outputVolume) {
        var inputColor = 'red';
        if (inputVolume < .50) {
            inputColor = 'green';
        } else if (inputVolume < .75) {
            inputColor = 'yellow';
        }

        inputVolumeBar.style.width = Math.floor(inputVolume * 300) + 'px';
        inputVolumeBar.style.background = inputColor;

        var outputColor = 'red';
        if (outputVolume < .50) {
            outputColor = 'green';
        } else if (outputVolume < .75) {
            outputColor = 'yellow';
        }

        outputVolumeBar.style.width = Math.floor(outputVolume * 300) + 'px';
        outputVolumeBar.style.background = outputColor;
    });
}

function updateAllDevices() {
    updateDevices(speakerDevices, device.audio.speakerDevices.get());
    updateDevices(ringtoneDevices, device.audio.ringtoneDevices.get());

    // updateDevices(speakerDevices, );
    // updateDevices(ringtoneDevices, device);
}

// Update the available ringtone and speaker devices
function updateDevices(selectEl, selectedDevices) {
    selectEl.innerHTML = '';

    device.audio.availableOutputDevices.forEach(function (device, id) {
        var isActive = (selectedDevices.size === 0 && id === 'default');
        selectedDevices.forEach(function (device) {
            if (device.deviceId === id) {
                isActive = true;
            }
        });

        var option = document.createElement('option');
        option.label = device.label;
        option.setAttribute('data-id', id);
        if (isActive) {
            option.setAttribute('selected', 'selected');
        }
        selectEl.appendChild(option);
    });
}

// Activity log
function log(message) {
    var logDiv = document.getElementById('log');
    logDiv.innerHTML += '<p>&gt;&nbsp;' + message + '</p>';
    logDiv.scrollTop = logDiv.scrollHeight;
}


function clearLog() {
    var logDiv = document.getElementById('log');
    logDiv.innerHTML = '';
    logDiv.scrollTop = logDiv.scrollHeight;
}

// Set the client name in the UI
function setClientNameUI(clientName) {
    var div = document.getElementById('client-name');
    div.innerHTML = 'Your client name: <strong>' + clientName +
        '</strong>';
}

function renewTwDevice() {
    console.log(device.status());
    if (!device) {
        initDevice();
    } else {
        var status = device.status();
        var refrash_device_statuses = ['pending', 'closed', 'offline', 'error'];
        if (refrash_device_statuses.inArray(status)) {
            initDevice();
        }
    }
}

document.getElementById('button-answer').onclick = function () {
    if (connection) {
        connection.accept();
        document.getElementById('call-controls').style.display = 'block';
        document.getElementById('call-controls2').style.display = 'none';
    }
};

document.getElementById('button-reject').onclick = function () {
    if (connection) {
        connection.reject();
        document.getElementById('call-controls').style.display = 'block';
        document.getElementById('call-controls2').style.display = 'none';
    }
};


function initDevice() {

    clearLog();
    log('Requesting Capability Token...');
    $.getJSON('/phone/get-token')
        .then(function (data_res) {
            var data = data_res.data;
            log('Got a token.');
            console.log('Token: ' + data.token);
            // Setup Twilio.Device
            device = new Twilio.Device(data.token, {debug: true});

            //console.log([data, device]);
            device.on('ready', function (device) {
                log('Twilio.Device Ready!');
                document.getElementById('call-controls').style.display = 'block';
            });

            device.on('error', function (error) {
                log('Twilio.Device Error: ' + error.message);
            });

            device.on('connect', function (conn) {
                log('Successfully established call!');
                document.getElementById('button-call').style.display = 'none';
                document.getElementById('button-hangup').style.display = 'inline';
                volumeIndicators.style.display = 'block';
                bindVolumeIndicators(conn);
            });

            device.on('disconnect', function (conn) {
                log('Call ended.');
                document.getElementById('button-call').style.display = 'inline';
                document.getElementById('button-hangup').style.display = 'none';
                volumeIndicators.style.display = 'none';
            });

            device.on('incoming', function (conn) {
                connection = conn;
                log('Incoming connection from ' + conn.parameters.From);
                var archEnemyPhoneNumber = tw_configs.client;
                document.getElementById('call-controls').style.display = 'none';
                document.getElementById('call-controls2').style.display = 'block';
                /*
                if (conn.parameters.From === archEnemyPhoneNumber || conn.parameters.From === 'client:' + archEnemyPhoneNumber) {
                    conn.reject();
                    log('It\'s your nemesis. Rejected call.');
                } else {
                    // accept the incoming connection and start two-way audio
                    if(!confirm('Incoming call... Answer?')) {
                        conn.reject();
                    } else {
                        conn.accept();
                    }
                }*/
            });


            device.on('cancel', function (conn) {
                connection = conn;
                log('Cancel call.');
                document.getElementById('call-controls').style.display = 'block';
                document.getElementById('call-controls2').style.display = 'none';
            });



            setClientNameUI(data.client);
            device.audio.on('deviceChange', updateAllDevices.bind(device));
            // Show audio selection UI if it is supported by the browser.
            if (device.audio.isOutputSelectionSupported) {
                document.getElementById('output-selection').style.display = 'block';
            }
        })
        .catch(function (err) {
            console.log(err);
            log('Could not get a token from server!');
        });
}

$(function () {
    console.log(tw_configs);
    initDevice();
    setInterval('renewTwDevice();', 50000);
});