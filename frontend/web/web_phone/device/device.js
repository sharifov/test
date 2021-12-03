(function (global, factory) {
    if (typeof define === "function" && define.amd) {
        define(['module', 'exports'], factory);
    } else if (typeof exports !== "undefined") {
        factory(module, exports);
    } else {
        var mod = {
            exports: {}
        };
        factory(mod, mod.exports);
        global.remote = mod.exports;
    }
})(this, function (module, exports) {
    'use strict';

    Object.defineProperty(exports, "__esModule", {
        value: true
    });

    var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) {
        return typeof obj;
    } : function (obj) {
        return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };

    var win = window;

    if (!win) {
        throw new Error('Plugin for browser usage only');
    }

    var CIRCULAR_ERROR_MESSAGE = void 0;

    // https://github.com/nodejs/node/blob/master/lib/util.js
    function tryStringify(arg) {
        try {
            return JSON.stringify(arg);
        } catch (error) {
            // Populate the circular error message lazily
            if (!CIRCULAR_ERROR_MESSAGE) {
                try {
                    var a = {};
                    a.a = a;
                    JSON.stringify(a);
                } catch (circular) {
                    CIRCULAR_ERROR_MESSAGE = circular.message;
                }
            }
            if (error.message === CIRCULAR_ERROR_MESSAGE) {
                return '[Circular]';
            }
            throw error;
        }
    }

    function getConstructorName(obj) {
        if (!Object.getOwnPropertyDescriptor || !Object.getPrototypeOf) {
            return Object.prototype.toString.call(obj).slice(8, -1);
        }

        // https://github.com/nodejs/node/blob/master/lib/internal/util.js
        while (obj) {
            var descriptor = Object.getOwnPropertyDescriptor(obj, 'constructor');
            if (descriptor !== undefined && typeof descriptor.value === 'function' && descriptor.value.name !== '') {
                return descriptor.value.name;
            }

            obj = Object.getPrototypeOf(obj);
        }

        return '';
    }

    function interpolate(array) {
        var result = '';
        var index = 0;

        if (array.length > 1 && typeof array[0] === 'string') {
            result = array[0].replace(/(%?)(%([sdjo]))/g, function (match, escaped, ptn, flag) {
                if (!escaped) {
                    index += 1;
                    var arg = array[index];
                    var a = '';
                    switch (flag) {
                        case 's':
                            a += arg;
                            break;
                        case 'd':
                            a += +arg;
                            break;
                        case 'j':
                            a = tryStringify(arg);
                            break;
                        case 'o':
                        {
                            var obj = tryStringify(arg);
                            if (obj[0] !== '{' && obj[0] !== '[') {
                                obj = '<' + obj + '>';
                            }
                            a = getConstructorName(arg) + obj;
                            break;
                        }
                    }
                    return a;
                }
                return match;
            });

            // update escaped %% values
            result = result.replace(/%{2,2}/g, '%');

            index += 1;
        }

        // arguments remaining after formatting
        if (array.length > index) {
            if (result) result += ' ';
            result += array.slice(index).join(' ');
        }

        return result;
    }

    var hasOwnProperty = Object.prototype.hasOwnProperty;


    // Light deep Object.assign({}, ...sources)
    function assign() {
        var target = {};
        for (var s = 0; s < arguments.length; s += 1) {
            var source = Object(arguments[s]);
            for (var key in source) {
                if (hasOwnProperty.call(source, key)) {
                    target[key] = _typeof(source[key]) === 'object' && !Array.isArray(source[key]) ? assign(target[key], source[key]) : source[key];
                }
            }
        }
        return target;
    }

    function getStacktrace() {
        try {
            throw new Error();
        } catch (trace) {
            return trace.stack;
        }
    }

    function Queue(capacity) {
        var _this = this;

        var queue = [];
        var sent = [];

        this.length = function () {
            return queue.length;
        };
        this.sent = function () {
            return sent.length;
        };

        this.push = function (message) {
            queue.push(message);
            if (queue.length > capacity) {
                queue.shift();
            }
        };

        this.send = function () {
            if (!sent.length) {
                sent = queue;
                queue = [];
            }
            return sent;
        };

        this.confirm = function () {
            sent = [];
            _this.content = '';
        };

        this.fail = function () {
            var overflow = 1 + queue.length + sent.length - capacity;

            if (overflow > 0) {
                sent.splice(0, overflow);
                queue = sent.concat(queue);
                _this.confirm();
            }
            // if (queue.length + sent.length >= capacity) this.confirm();
        };
    }

    var hasStacktraceSupport = !!getStacktrace();

    var loglevel = void 0;
    var originalFactory = void 0;
    var pluginFactory = void 0;

    function plain(log) {
        return '[' + log.timestamp + '] ' + log.level.label.toUpperCase() + (log.logger ? ' (' + log.logger + ')' : '') + ': ' + log.message + (log.stacktrace ? '\n' + log.stacktrace : '');
    }

    function json(log) {
        log.level = log.level.label;
        return log;
    }

    function setToken() {
        throw new Error("You can't set token for a not appled plugin");
    }

    var save = win.remote;

    var defaultCapacity = 500;
    var defaults = {
        url: '/logger',
        method: 'POST',
        headers: {},
        token: '',
        onUnauthorized: function onUnauthorized() {},
        timeout: 0,
        interval: 1000,
        level: 'trace',
        backoff: {
            multiplier: 2,
            jitter: 0.1,
            limit: 30000
        },
        capacity: 0,
        stacktrace: {
            levels: ['trace', 'warn', 'error'],
            depth: 3,
            excess: 0
        },
        timestamp: function timestamp() {
            return new Date().toISOString();
        },
        format: plain
    };

    var remote = {
        noConflict: function noConflict() {
            if (win.remote === remote) {
                win.remote = save;
            }
            return remote;
        },

        plain: plain,
        json: json,
        apply: function apply(logger, options) {
            // if (!logger || !logger.getLogger) {
            //     throw new TypeError('Argument is not a root loglevel object');
            // }
            //
            // if (loglevel) {
            //     throw new Error('You can assign a plugin only one time');
            // }

            if (!win.XMLHttpRequest) return logger;

            loglevel = logger;

            var config = assign(defaults, options);

            config.capacity = config.capacity || defaultCapacity;

            var backoff = config.backoff;


            var backoffFunc = (typeof backoff === 'undefined' ? 'undefined' : _typeof(backoff)) === 'object' ? function (duration) {
                var next = duration * backoff.multiplier;
                if (next > backoff.limit) next = backoff.limit;
                next += next * backoff.jitter * Math.random();
                return next;
            } : backoff;

            var interval = config.interval;

            var contentType = void 0;
            var isJSON = true;
            var isSending = false;
            var isSuspended = false;

            var queue = new Queue(config.capacity);

            function send() {
                if (isSuspended || isSending || config.token === undefined) {
                    return;
                }

                if (!queue.sent()) {
                    if (!queue.length()) {
                        return;
                    }

                    var logs = queue.send();

                    queue.content = isJSON ? '{"logs":[' + logs.join(',') + ']}' : logs.join('\n');
                }

                isSending = true;

                var xhr = new win.XMLHttpRequest();
                xhr.open(config.method, config.url, true);
                xhr.setRequestHeader('Content-Type', contentType);
                if (config.token) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + config.token);
                }

                var headers = config.headers;

                for (var header in headers) {
                    if (hasOwnProperty.call(headers, header)) {
                        var value = headers[header];
                        if (value) {
                            xhr.setRequestHeader(header, value);
                        }
                    }
                }

                function suspend(successful) {
                    if (!successful) {
                        // interval = config.backoff(interval || 1);
                        interval = backoffFunc(interval || 1);
                        queue.fail();
                    }

                    isSuspended = true;
                    win.setTimeout(function () {
                        isSuspended = false;
                        send();
                    }, interval);
                }

                var timeout = void 0;
                if (config.timeout) {
                    timeout = win.setTimeout(function () {
                        isSending = false;
                        xhr.abort();
                        suspend();
                    }, config.timeout);
                }

                xhr.onreadystatechange = function () {
                    if (xhr.readyState !== 4) {
                        return;
                    }

                    isSending = false;
                    win.clearTimeout(timeout);

                    if (xhr.status === 200) {
                        // eslint-disable-next-line prefer-destructuring
                        interval = config.interval;
                        queue.confirm();
                        suspend(true);
                    } else {
                        if (xhr.status === 401) {
                            var token = config.token;

                            config.token = undefined;
                            config.onUnauthorized(token);
                        }
                        suspend();
                    }
                };

                xhr.send(queue.content);
            }

            originalFactory = logger.methodFactory;

            pluginFactory = function remoteMethodFactory(methodName, logLevel, loggerName) {
                var rawMethod = originalFactory(methodName, logLevel, loggerName);
                var needStack = hasStacktraceSupport && config.stacktrace.levels.some(function (level) {
                    return level === methodName;
                });
                var levelVal = loglevel.levels[methodName.toUpperCase()];
                var needLog = levelVal >= loglevel.levels[config.level.toUpperCase()];


                return function () {
                    for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
                        args[_key] = arguments[_key];
                    }

                    if (needLog) {
                        var timestamp = config.timestamp();

                        var stacktrace = needStack ? getStacktrace() : '';
                        if (stacktrace) {
                            var lines = stacktrace.split('\n');
                            lines.splice(0, config.stacktrace.excess + 3);
                            var depth = config.stacktrace.depth;

                            if (depth && lines.length !== depth + 1) {
                                var shrink = lines.splice(0, depth);
                                stacktrace = shrink.join('\n');
                                if (lines.length) stacktrace += '\n    and ' + lines.length + ' more';
                            } else {
                                stacktrace = lines.join('\n');
                            }
                        }

                        var log = config.format({
                            message: interpolate(args),
                            level: {
                                label: methodName,
                                value: levelVal
                            },
                            logger: loggerName || '',
                            timestamp: timestamp,
                            stacktrace: stacktrace
                        });



                        if (isJSON === undefined) {
                            isJSON = typeof log !== 'string';
                            contentType = isJSON ? 'application/json' : 'text/plain';
                        }

                        var content = '';
                        if (isJSON) {
                            try {
                                content += JSON.stringify(log);
                            } catch (error) {
                                rawMethod.apply(undefined, args);
                                loglevel.getLogger('logger').error(error);
                                return;
                            }
                        } else {
                            content += log;
                        }

                        queue.push(content);
                        send();
                    }

                    rawMethod.apply(undefined, args);
                };
            };

            logger.methodFactory = pluginFactory;
            logger.setLevel(logger.getLevel());

            remote.setToken = function (token) {
                config.token = token;
                send();
            };

            return logger;
        },
        disable: function disable() {
            if (!loglevel) {
                throw new Error("You can't disable a not appled plugin");
            }

            if (pluginFactory !== loglevel.methodFactory) {
                throw new Error("You can't disable a plugin after appling another plugin");
            }

            loglevel.methodFactory = originalFactory;
            loglevel.setLevel(loglevel.getLevel());
            loglevel = undefined;
            remote.setToken = setToken;
        },

        setToken: setToken
    };

    exports.default = remote;
    module.exports = exports['default'];
});

(function () {
    function Init() {
        if (initiated) {
            console.log('device already initiated');
            return;
        }

        initiated = true;

        // console.log("Requesting Twilio Access Token...");
        PhoneWidget.addLog("Requesting Twilio Access Token...");
        $.getJSON('/phone/get-token')
            .then(function (response) {
                // console.log("Got a Twilio Access token.");
                PhoneWidget.addLog("Got a Twilio Access token.");
                initDevice({"token": response.data.token, "refreshTime": response.data.refreshTime});
            })
            .catch(function (err) {
                PhoneWidget.addLog("Get Twilio Access token error. Reload page!");
                console.log(err);
                createNotify('Twilio Token error!', 'Could not get a token from server! Please reload page!', 'error');
            });

        function initDevice(token) {
            // console.log("Init Twilio Device...");
            PhoneWidget.addLog("Init Twilio Device...");

            const logger = Twilio.Logger;
            // const originalFactory = logger.methodFactory;
            // logger.methodFactory = function (methodName, logLevel, loggerName) {
            //     const method = originalFactory(methodName, logLevel, loggerName);
            //     return function (message) {
            //         const prefix = '[My Application]';
            //         method(prefix + message);
            //     };
            // };
            logger.setLevel(3);

            remote.apply(logger, {url: '/voip/log'});


            console.log(logger);

            const device = new Twilio.Device("token.token", {
               // logLevel: 4,
                //edge: 'ashburn',
                closeProtection: true,
                codecPreferences: ["opus", "pcmu"]
            });

            const speakerDevices = document.getElementById("speaker-devices");
            const ringtoneDevices = document.getElementById("ringtone-devices");
            const microphoneDevices = document.getElementById("microphone-devices");

            speakerDevices.addEventListener("change", updateOutputDevice);
            ringtoneDevices.addEventListener("change", updateRingtoneDevice);

            function updateOutputDevice() {
                const selectedDevices = Array.from(speakerDevices.children)
                    .filter((node) => node.selected)
                    .map((node) => node.getAttribute("data-id"));

                device.audio.speakerDevices.set(selectedDevices);
            }

            function updateRingtoneDevice() {
                const selectedDevices = Array.from(ringtoneDevices.children)
                    .filter((node) => node.selected)
                    .map((node) => node.getAttribute("data-id"));

                device.audio.ringtoneDevices.set(selectedDevices);
            }

            function updateOutputDevices(selectEl, selectedDevices) {
                selectEl.innerHTML = '';
                device.audio.availableOutputDevices.forEach(function (device, id) {
                    let isActive = (selectedDevices.size === 0 && id === 'default');
                    selectedDevices.forEach(function (device) {
                        if (device.deviceId === id) {
                            isActive = true;
                        }
                    });
                    let option = document.createElement('option');
                    option.label = device.label;
                    option.setAttribute('data-id', id);
                    if (isActive) {
                        option.setAttribute('selected', 'selected');
                    }
                    selectEl.appendChild(option);
                });
            }

            const updateInputDevice = function () {
                microphoneDevices.innerHTML = '';

                if (device.audio.availableInputDevices.size < 1) {
                    createNotify('Phone widget', 'Microphone device not found.', 'error')
                    PhoneWidget.addLog('Not found Microphone device');
                    PhoneWidget.getDeviceStatus().microphoneUnselected();
                    return;
                }

                let isSelected = false;
                device.audio.availableInputDevices.forEach(device => {
                    const option = document.createElement('option');
                    option.label = device.label;
                    option.value = device.deviceId;
                    option.innerText = device.label;
                    if (isSelected === false) {
                        option.setAttribute('selected', 'selected');
                        isSelected = true;
                    }
                    microphoneDevices.appendChild(option);
                });
                PhoneWidget.getDeviceStatus().microphoneSelected();
            }

            function updateAllAudioDevices() {
                updateOutputDevices(speakerDevices, this.audio.speakerDevices.get());
                updateOutputDevices(ringtoneDevices, this.audio.ringtoneDevices.get());
            }

            const updateToken = () => {
                PhoneWidget.addLog("Update Twilio Access Token...");
                $.getJSON('/phone/get-token')
                    .then(function (response) {
                        //console.log("Got a Twilio Access token.");
                        PhoneWidget.addLog("Got a Twilio Access token.");
                        device.updateToken(response.data.token);
                        setTimeout(async () => updateToken(), response.data.refreshTime * 1000);
                    })
                    .catch(function (err) {
                        PhoneWidget.addLog("Get Twilio Access token error. Reload page!", '#f41b1b');
                        console.log(err);
                        createNotify('Twilio Token error!', 'Could not get a token from server! Please reload page!', 'error');
                    });
            };

           setTimeout(async () => updateToken(), token.refreshTime * 1000);

            device.on('registering', () => {
                //console.log("Twilio.Device Registering...");
                PhoneWidget.addLog("Twilio.Device Registering...");
            });

            const incomingCallHandler = (call) => {
                PhoneWidget.incomingSoundOff();

                call.on('accept', call => {
                    PhoneWidget.removeTwilioInternalIncomingConnection();
                    //console.log('The incoming call was accepted.');
                    PhoneWidget.freeDialButton();
                    PhoneWidget.setActiveCall(call);
                    PhoneWidget.incomingSoundOff();

                    call.on("volume", function (inputVolume, outputVolume) {
                        PhoneWidget.volumeIndicatorsChange(inputVolume, outputVolume)
                    });
                    PhoneWidget.soundConnect();
                });
                call.on('cancel', () => {
                    //console.log('The call has been canceled.');
                    PhoneWidget.freeDialButton();
                    PhoneWidget.removeTwilioInternalIncomingConnection();
                    PhoneWidget.incomingSoundOff();
                });
                call.on('disconnect', call => {
                    try {
                        device.audio.unsetInputDevice();
                    } catch (error) {
                        console.log(error);
                    }
                    //console.log('The call has been disconnected.');
                    PhoneWidget.freeDialButton();
                    PhoneWidget.removeTwilioInternalIncomingConnection();
                    PhoneWidget.soundDisconnect();
                    PhoneWidget.incomingSoundOff();
                    window.sendCommandUpdatePhoneWidgetCurrentCalls(call.parameters.CallSid, window.userId, window.generalLinePriorityIsEnabled, true);
                });
                call.on('error', error => {
                    createNotify('Call error', 'More info in logs panel', 'error');
                    console.log('An error has occurred: ', error);
                    PhoneWidget.addLog(error);
                });

                let autoAccept = null;
                let isInternal = null;
                let requestCall = null;

                call.customParameters.forEach(function (value, key) {
                    if (key === 'autoAccept' && value === 'false') {
                        autoAccept = false;
                    } else if (key === 'isInternal' && value === 'true') {
                        isInternal = true;
                    } else if (key === 'requestCall') {
                        requestCall = value;
                    }
                });

                if (autoAccept === false) {
                    if (isInternal === true && requestCall !== null) {
                        let callObj = JSON.parse(atob(requestCall), function (k, v) {
                            if (v === 'false') {
                                return false;
                            } else if (v === 'true') {
                                return true;
                            }
                            return v;
                        });
                        callObj.callSid = call.parameters.CallSid;
                        PhoneWidget.setTwilioInternalIncomingConnection(call);
                        PhoneWidget.refreshCallStatus(callObj);
                    }
                } else {
                    device.audio.setInputDevice(microphoneDevices.value)
                        .then(() => {
                            call.accept();
                            //console.log("Accepted incoming call.");
                        })
                        .catch(error => {
                            console.log(error);
                            createNotify('Accept incoming connection', error.message, 'error')
                        });
                }
            };

            device.on("registered", () => {
                //console.log("Twilio.Device Ready!");

                PhoneWidget.getDeviceStatus().deviceRegister();

                if (device.audio.speakerDevices.get().size > 0) {
                    PhoneWidget.getDeviceStatus().speakerSelected();
                } else {
                    PhoneWidget.getDeviceStatus().speakerUnselected();
                }

                if (device.audio.ringtoneDevices.get().size > 0) {
                    PhoneWidget.getDeviceStatus().ringtoneSelected();
                } else {
                    PhoneWidget.getDeviceStatus().ringtoneUnselected();
                }

                device.audio.removeListener('deviceChange', updateInputDevice);
                device.audio.addListener('deviceChange', updateInputDevice);
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then((stream) => {
                        updateInputDevice();
                        stream.getTracks().forEach(track => track.stop());
                    }).catch(error => {
                        console.log(error);
                        error.comment = 'Microphone error';
                        PhoneWidget.addLog(error);
                        PhoneWidget.getDeviceStatus().microphoneUnselected();
                    });

                device.removeListener("incoming", incomingCallHandler);
                device.addListener("incoming", incomingCallHandler);
            });

            device.on('unregistered', function () {
                //console.log("Twilio.Device unregistered!");
                PhoneWidget.getDeviceStatus().reset();
                PhoneWidget.incomingSoundOff();
                PhoneWidget.getDeviceStatus().deviceUnregister();
            });

            device.on('error', (twilioError, call) => {
                if (twilioError.code === 20104) {
                    //console.log('Twilio JWT Token Expired');
                    PhoneWidget.addLog('Twilio JWT Token Expired', '#f41b1b');
                    //console.log("Requesting New Twilio Access Token...");
                    PhoneWidget.addLog("Requesting New Twilio Access Token...");
                    updateToken();
                    return;
                }
                console.log('An error has occurred: ', twilioError);
                PhoneWidget.freeDialButton();
                PhoneWidget.addLog(twilioError);
                PhoneWidget.incomingSoundOff();
                createNotify(twilioError.description, twilioError.explanation, 'error');
            });

           device.audio.on("deviceChange", updateAllAudioDevices.bind(device));

            if (device.audio.isOutputSelectionSupported) {
                $('#output-selection').show();
            } else {
                $(document).find('.phone-widget__additional-bar .tabs__nav.tab-nav .wp-tab-device').hide();
                $(document).find('.phone-widget__additional-bar .wp-devices-tab-log').addClass('active-tab');
                $(document).find('.phone-widget__additional-bar #tab-device').hide();
                $(document).find('.phone-widget__additional-bar #tab-logs').show();
            }

            device.register();
        }
    }

    let initiated = false;

    window.phoneWidget.device.initialize = {
        Init: Init
    }
})();