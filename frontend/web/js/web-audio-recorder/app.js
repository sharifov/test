;( function ($, window) {
    'use strict';
    $.fn.webAudioRecorder = function (options) {
        var URL = window.URL || window.webkitURL;

        var gumStream;
        var recorder;
        var input;
        var encodingType = 'mp3';
        var encodeAfterRecord = true;

        var AudioContext = window.AudioContext || window.webkitAudioContext;
        var audioContext;
        var updateDateTime;

        var recordBtn, stopBtn, blobRecord, blobUrl, form, submitBtn;

        var format = document.createElement('div');
        format.id = 'format';

        var labelLog = document.createElement('label');
        labelLog.innerText = 'Log';
        labelLog.className = 'control-label';
        var log = document.createElement('pre');
        log.id = 'log';
        var recordLabel = document.createElement('label');
        recordLabel.innerText = 'Record';
        recordLabel.className = 'control-label';
        var recordView = document.createElement('div');
        recordView.id = 'record';
        recordView.className = 'd-flex justify-content-center align-items-center';
        var canvas = document.getElementById('visualizer');
        var canvasCtx = canvas.getContext("2d");
        var timerWrapper = $('#timer-wrapper');


        var defaults = {
            recordBtnSelector: '',
            stopBtnSelector: '',
            showFormatsInfo: true,
            showLog: true,
            blobUrl: ''
        };

        var _self = $(this);

        var settings = $.extend({}, defaults, options);

        var validate = function () {
            try {
                recordBtn = $(settings.recordBtnSelector);
                if (!recordBtn.length) {
                    throw new Error('Record button is not found');
                }
                stopBtn = $(settings.stopBtnSelector);
                if (!stopBtn.length) {
                    throw new Error('Stop button is not found');
                }
            } catch (Error) {
                _self.html('');
                console.log(Error.message);
                return false;
            }

            return true;
        }

        var minSecStr = function(n) {
            return (n < 10 ? "0" : "") + n;
        };

        updateDateTime = function() {
            var sec;

            if (recorder) {
                sec = recorder.recordingTime() | 0;
                $('#time-display').html("" + (minSecStr(sec / 60 | 0)) + ":" + (minSecStr(sec % 60)));
            }
        };

        window.setInterval(updateDateTime, 200);

        var visualize = function (stream) {
            if(!audioContext) {
                audioContext = new AudioContext();
            }

            let source = audioContext.createMediaStreamSource(stream);

            let analyser = audioContext.createAnalyser();
            analyser.fftSize = 2048;
            let bufferLength = analyser.frequencyBinCount;
            let dataArray = new Uint8Array(bufferLength);

            source.connect(analyser);

            draw();

            timerWrapper.show();

            function draw() {
                let WIDTH = canvas.width
                let HEIGHT = canvas.height;

                requestAnimationFrame(draw);

                analyser.getByteTimeDomainData(dataArray);

                canvasCtx.fillStyle = 'rgb(255,255,255)';
                canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);

                canvasCtx.lineWidth = 2;
                canvasCtx.strokeStyle = 'rgb(0, 0, 0)';

                canvasCtx.beginPath();

                let sliceWidth = WIDTH * 1.0 / bufferLength;
                let x = 0;


                for(let i = 0; i < bufferLength; i++) {

                    let v = dataArray[i] / 128.0;
                    let y = v * HEIGHT/2;

                    if(i === 0) {
                        canvasCtx.moveTo(x, y);
                    } else {
                        canvasCtx.lineTo(x, y);
                    }

                    x += sliceWidth;
                }

                canvasCtx.lineTo(canvas.width, canvas.height/2);
                canvasCtx.stroke();
            }
        }

        var startRecording = function () {
            console.log("startRecording() called");

            var constraints = { audio: true, video:false }

            navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
                __log("getUserMedia() success, stream created, initializing WebAudioRecorder...");

                audioContext = new AudioContext();

                if (settings.showFormatsInfo) {
                    format.innerHTML = "Format: mp3 @ "+audioContext.sampleRate/1000+"kHz";
                }

                gumStream = stream;

                input = audioContext.createMediaStreamSource(stream);

                visualize(gumStream);

                recorder = new WebAudioRecorder(input, {
                    workerDir: "/js/web-audio-recorder/", // must end with slash
                    encoding: encodingType,
                    numChannels:2, //2 is the default, mp3 encoding supports only 2
                    onEncoderLoading: function(recorder, encoding) {
                        // show "loading encoder..." display
                        __log("Loading "+encoding+" encoder...");
                    },
                    onEncoderLoaded: function(recorder, encoding) {
                        // hide "loading encoder..." display
                        __log(encoding+" encoder loaded");
                    }
                });

                recorder.onComplete = function(recorder, blob) {
                    __log("Encoding complete");
                    showRecord(blob);
                    blobRecord = bloblToFile(blob, 'user-voice-mail.mp3');
                    stopBtn.attr('disabled', true);
                    recordBtn.attr('disabled', false);
                    submitBtn.attr('disabled', false);
                    gumStream.getAudioTracks()[0].stop();
                }

                recorder.setOptions({
                    timeLimit:20,
                    encodeAfterRecord:encodeAfterRecord,
                    mp3: {bitRate: 160}
                });

                recorder.startRecording();

                __log("Recording started");

            }).catch(function(err) {
                _self.html(err.message);
                recordBtn.attr('disabled', false);
                submitBtn.attr('disabled', false);
                stopBtn.attr('disabled', true);
            });

            //disable the record button
            recordBtn.attr('disabled', true);
            submitBtn.attr('disabled', true);
            stopBtn.attr('disabled', false);
        }

        var stopRecording = function () {
            console.log("stopRecording() called");

            gumStream.getAudioTracks()[0].stop();
            stopBtn.attr('disabled', true);
            recordBtn.attr('disabled', false);
            submitBtn.attr('disabled', false);

            recorder.finishRecording();

            timerWrapper.hide();

            __log('Recording stopped');
        }

        var showRecord = function (blob) {
            var url;
            if (typeof blob === 'object') {
                url = URL.createObjectURL(blob);
            } else if (typeof blob === 'string') {
                url = blob;
            } else {
                throw new Error('Provided param neither object nor string');
            }
            var au = document.createElement('audio');
            au.controls = true;
            au.src = url;
            au.style.width = '100%';

            var removeBtn = document.createElement('button');
            var removeIcon = document.createElement('i');
            removeIcon.className = 'fa fa-trash';
            removeBtn.appendChild(removeIcon);
            removeBtn.className = 'btn btn-danger';
            removeBtn.style.marginLeft = '10px';

            $(removeBtn).on('click', function () {
                blobRecord = '';
                blobUrl = '';
                $(this).closest('div').html('');
            });


            blobUrl = url;
            recordView.innerHTML = '';
            recordView.appendChild(au);
            recordView.appendChild(removeBtn);
        }

        var __log = function (e, data) {
            if (settings.showLog) {
                log.innerHTML += "\n" + e + " " + (data || '');
            }
        }

        var bloblToFile = function (blob, fileName) {
            var file = new File([blob], fileName, {type:'audio/mpeg'});
            return file;
        }

        var decodeBlobFromUrl = function (blobUrl) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', blobUrl, true);
            xhr.responseType = 'blob';
            xhr.onload = function(e) {
                if (this.status === 200) {
                    blobRecord = bloblToFile(this.response, 'user-voice-mail.mp3');
                }
            };
            xhr.send();
        }

        this.init = function () {
            if (!validate()) {
                return false;
            }

            if (settings.showFormatsInfo) {
                this.append(format);
            }

            if (settings.showLog) {
                this.append(labelLog);
                this.append(log);
            }


            this.append(recordLabel);
            this.append(recordView);

            form = this.closest('form');
            submitBtn = form.find('[type="submit"]');

            recordBtn.on('click', startRecording);
            stopBtn.on('click', stopRecording);

            if (settings.blobUrl) {
                showRecord(settings.blobUrl);
                decodeBlobFromUrl(settings.blobUrl);
            }

            return this;
        }

        this.getRecord = function () {
            if (blobRecord) {
                return blobRecord;
            }
            return '';
        }

        this.getBlobUrl = function () {
            if (blobUrl) {
                return blobUrl;
            }
            return '';
        }

        return this.init();
    };
})(jQuery, window);