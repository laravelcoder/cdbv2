/**
 * WebVideoEditor
 * @version 1.2.2
 * @author Andchir<andycoderw@gmail.com>
 */

var WebVideoEditor = function (options) {

    'use strict';

    var self = this;

    var defaultOptions = {
        requestHandler: 'index.php',
        userId: '',
        baseUrl: '',
        inputDir: 'userfiles/input/',
        outputDir: 'userfiles/output/',
        updateDataInterval: 2000
    };

    var currentMedia, mainVideo, isVideoPlaying, $sliderTimeline, $sliderTimelineRange, episodes;

    var inputList = [], outputList = [];

    this.audioPlayer = new Audio();

    /** initialize */
    this.init = function () {

        this.options = $.extend({}, defaultOptions, options);

        if (document.readyState != 'loading') {
            this.onStart();
        } else {
            document.addEventListener('DOMContentLoaded', this.onStart.bind(this));
        }
    };

    /** On DOM ready */
    this.onStart = function () {

        $sliderTimeline = $('#wve-timeline');
        $sliderTimelineRange = $('#wve-timeline-range');

        $('body')
            .tooltip({
                selector: '[data-toggle="tooltip"],.toggle-tooltip',
                container: 'body',
                trigger: 'hover',
                animation: false,
                placement: function (tooltip, element) {
                    return $(this.element).data('placement')
                        ? $(this.element).data('placement')
                        : 'bottom';
                }
            })
            .on('mousedown', '[data-toggle="tooltip"],.toggle-tooltip', function () {
                $($(this).data('bs.tooltip')['tip']).remove();
            });

        mainVideo = $('#wve-video').get(0);
        $(mainVideo)
            .on('error', function () {
                if (!this.readyState/* && mainVideo.src && mainVideo.src.substr(mainVideo.src.length - 4, 1) === '.'*/) {
                    //self.alert('The video format is not supported by your browser.');
                } else {

                }
            })
            .on('loadedmetadata', function () {
                $sliderTimeline
                    .slider('option', {
                        max: this.duration * 1000,
                        value: 0
                    });
                $sliderTimelineRange
                    .slider('option', {
                        max: this.duration * 1000,
                        values: [0, this.duration * 1000]
                    });

                if (currentMedia) {
                    var isIntro = !!(currentMedia.isIntro);
                    if (!isIntro) {
                        $('#wve-editor-player-time')
                            .text(self.secondsToTime(this.duration))
                            .parent()
                            .show();
                    }
                }
            })
            .on('play pause', self.onMainVideoPlayChange.bind(self))
            .on('timeupdate', {playing: true}, function (event) {
                if (isVideoPlaying && $sliderTimeline.data('uiSlider')) {
                    $sliderTimeline.slider('option', {value: this.currentTime * 1000});
                    $('#wve-editor-player-time-current').text(self.secondsToTime(this.currentTime));
                }
            });

        this.updateMediaList('input');
        this.updateMediaList('output');
        this.buttonsInit();

        // Welcome video
        /*
        currentMedia = {
            ext: 'mp4',
            title: 'intro',
            duration_ms: 6000,
            duration_time: '00:06:00',
            type: 'video',
            isIntro: true,
            url: this.options.baseUrl + 'assets/video/intro.mp4'
        };
        setTimeout(function(){
            self.updateSelectedMedia();
        }, 100);
        */
    };

    /**
     * On video play state change
     */
    this.onMainVideoPlayChange = function () {

        var $playButton = $('button[data-action="play_main"]');
        if (mainVideo.paused) {
            $playButton.html('<span class="icon-play3"></span>');
            isVideoPlaying = false;
            self.clearTimers();
        } else {
            $playButton.html('<span class="icon-pause2"></span>');
            isVideoPlaying = true;
        }
    };

    /**
     * Clear timers
     */
    this.clearTimers = function () {
        clearInterval(this.interval);
        clearTimeout(this.timer);
    };

    /**
     * On Timeline change
     * @param event
     * @param ui
     */
    this.onTimelineChange = function (event, ui) {

        if (!currentMedia || !currentMedia.url) {
            return;
        }

        if (event.originalEvent) {
            mainVideo.currentTime = ui.value / 1000;

            $('#wve-editor-player-time-current')
                .text(this.secondsToTime(ui.value / 1000));

        }
    };

    /**
     * On Timeline range change
     * @param event
     * @param ui
     */
    this.onTimelineRangeChange = function (event, ui) {
        isVideoPlaying = false;
        mainVideo.pause();
        mainVideo.currentTime = ui.value / 1000;
    };

    /**
     * On Timeline tange slide
     * @param event
     * @param ui
     */
    this.onTimelineRangeSlide = function (event, ui) {
        var index = ui.values.indexOf(ui.value),
            timelineValue = $sliderTimeline.slider('value'),
            rangeStep = Math.round(currentMedia.duration_ms * 0.007),
            range = _.range(timelineValue - rangeStep, timelineValue + rangeStep);

        if (range.indexOf(ui.value) > -1) {

            var newValues = index === 0
                ? [timelineValue, ui.values[1]]
                : [ui.values[0], timelineValue];

            $sliderTimelineRange.slider('values', newValues);
            return false;
        }
    };

    /**
     * Import media
     */
    this.importMediaInit = function () {

        var template = _.template($('#modalImportMediaTemplate').html());
        $(document.body).append(template());

        var $modal = $('#modalImportMedia'),
            $button = $('.js-button-submit', $modal),
            $urlInput = $('[name="youtube_url"]', $modal),
            $fileInput = $('[type="file"]', $modal);

        $('.file-input-container', $modal).each(function () {
            var $fileInput = $('[type="file"]', this),
                $button = $('.file-input', this);
            $button.on('click', function (e) {
                e.preventDefault();
                if ($(this).is('.disabled')) {
                    return;
                }
                $fileInput.trigger('click');
            });
            $fileInput
                .on('change', function () {
                    if (this.files.length > 0) {
                        var fileName = this.files[0].name;
                        if (fileName.length > 26) {
                            fileName = fileName.substr(0, 12) + '...' + fileName.substr(fileName.length - 12);
                        }
                        if (this.files.length > 1) {
                            fileName += ' + ' + (this.files.length - 1);
                        }
                        $button.text(fileName);
                    } else {
                        $button.text(self.getLangString('browse_files') + '...');
                    }
                });
        });

        $modal.on('hidden.bs.modal', function () {
            $modal.remove();
        });

        $button.on('click', function (e) {
            e.preventDefault();
            if (!$urlInput.val() && !$fileInput.val()) {
                self.alert(self.getLangString('please_enter_url'), self.getLangString('error'), 'danger');
                return false;
            }

            $modal.modal('hide');

            var formData = new FormData();
            formData.append('input_url', $urlInput.val());
            formData.append('action', 'upload');

            var files = $fileInput.get(0).files;
            for (var i = 0; i < files.length; i++) {
                formData.append('input_file[]', files[i]);
            }

            self.importMedia(formData);
        });

        $modal.modal('show');
    };

    /**
     * Import media
     * @param formData
     */
    this.importMedia = function(formData) {

        self.showPreloader();

        $.ajax({
            url: self.options.baseUrl + self.options.requestHandler,
            method: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        })
            .done(function (response) {
                self.hidePreloader(500);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if (response.success) {
                    if (response.msg) {
                        self.alert(response.msg);
                    }
                    self.updateMediaList();
                } else {
                    if (response.msg) {
                        self.alert(response.msg, self.getLangString('error'), 'danger');
                    }
                }
                self.updateUserStat();
            });
    };

    /**
     * Pagination
     * @param $itemsContainer
     * @param $paginationContainer
     * @param numberPerPage
     * @param numberTotal
     * @param options
     * @param pageCallback
     */
    this.createPagination = function ($itemsContainer, $paginationContainer, numberPerPage, numberTotal, options, pageCallback) {

        $paginationContainer.empty();
        var paginationTemplate = _.template($('#paginationTemplate').html()),
            numberPages = Math.ceil(numberTotal / numberPerPage),
            currentPage = options.page;

        if (numberPages <= 1) {
            return;
        }

        var pagesToDisplay = [], skipped = false;
        for (var i = 1; i <= numberPages; i++) {
            if((i > 2 && i < numberPages - 2)
                && (i < currentPage - 3 || i > currentPage + 3) ){
                    if (!skipped) {
                        pagesToDisplay.push('...');
                    }
                    skipped = true;
                    continue;
            }
            skipped = false;
            pagesToDisplay.push(i);
        }
        var data = {
            numberTotal: numberTotal,
            pages: pagesToDisplay,
            currentPage: currentPage,
            numberPages: numberPages
        };
        $paginationContainer.append(paginationTemplate(data));

        $paginationContainer.find('.js-page-next').on('click', function(e) {
            e.preventDefault();
            var pageNumber = currentPage + 1;
            if (pageNumber < numberPages + 1) {
                options.page = pageNumber;
                pageCallback($itemsContainer, $paginationContainer, options);
            }
        });
        $paginationContainer.find('.js-page-prev').on('click', function(e) {
            e.preventDefault();
            var pageNumber = currentPage - 1;
            if (pageNumber > 0) {
                options.page = pageNumber;
                pageCallback($itemsContainer, $paginationContainer, options);
            }
        });
        $paginationContainer.find('.js-page-number').on('click', function(e) {
            e.preventDefault();
            var pageNumber = parseInt($(this).text(), 10);
            if (pageNumber !== currentPage) {
                options.page = pageNumber;
                pageCallback($itemsContainer, $paginationContainer, options);
            }
        });
    };

    /**
     * Buttons initialization
     */
    this.buttonsInit = function () {

        $(document.body).on('click', '[data-toggle="action"]', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $button = $(this),
                action = $button.data('action').split('_'),
                itemId = $button.data('id');

            switch (action[0]) {
                case 'delete':

                    switch (action[1]) {
                        case 'input':
                        case 'output':

                            self.mediaRemove(itemId, action[1]);

                            break;
                        case 'episode':

                            self.episodeRemove($button.data('index'));

                            break;
                    }

                    break;
                case 'rename':

                    self.mediaRename(itemId, action[1]);

                    break;
                case 'export-url':

                    self.exportUrl(itemId, action[1]);

                    break;
                case 'import':

                    self.importMediaInit();

                    break;
                case 'select-media':

                    if ($(this).parent('li').hasClass('active')) {
                        return;
                    }

                    self.selectMedia(itemId, action[1]);

                    break;
                case 'edit':

                    switch (action[1]) {
                        case 'episode':

                            var itemIndex = $button.data('index');
                            if (!episodes[itemIndex]) {
                                return;
                            }
                            var episode = episodes[itemIndex];
                            self.showWindowAddImage(episode);

                            break;
                    }

                    break;
                case 'play':

                    switch (action[1]) {
                        case 'main':

                            if (!currentMedia || !currentMedia.url) {
                                return;
                            }
                            if (!mainVideo.readyState) {
                                self.alert(self.getLangString('video_format_not_supported'));
                                return;
                            }
                            if (mainVideo.paused) {
                                mainVideo.play();
                            } else {
                                mainVideo.pause();
                            }

                            break;
                        case 'selected':

                            self.playVideoSelected();

                            break;
                        case 'episode':

                            var itemIndex = $button.data('index');

                            self.playMedia('episode', null, itemIndex);

                            break;
                        case 'output':

                            self.playMedia('output', itemId);

                            break;
                    }

                    break;

                case 'stepback':
                case 'stepforward':

                    self.clearTimers();

                    switch (action[1]) {
                        case 'main':

                            if (!currentMedia || !currentMedia.url) {
                                return;
                            }

                            var currentTimelineValue = $sliderTimeline.slider('value'),
                                minValue = $sliderTimeline.slider('option', 'min'),
                                maxValue = $sliderTimeline.slider('option', 'max');

                            currentTimelineValue += action[0] == 'stepforward' ? 10 : -10;
                            currentTimelineValue = Math.min(maxValue, Math.max(minValue, currentTimelineValue));

                            $sliderTimeline.slider('value', currentTimelineValue);
                            mainVideo.currentTime = currentTimelineValue / 1000;

                            break;
                    }

                    break;
                case 'take-episode':

                    self.takeEpisode();

                    break;
                case 'cut-fast':

                    self.makeCutFast();

                    break;
                case 'render':

                    self.checkProcessStatus(function () {
                        self.renderProject();
                    });

                    break;
                case 'convert':

                    self.checkProcessStatus(function () {
                        self.convertMedia(itemId, action[1]);
                    });

                    break;
                case 'preview':

                    var imageUrl = $button.data('url');

                    switch (action[1]) {
                        case 'image':
                            if (imageUrl) {
                                self.previewImage(imageUrl);
                            }
                            break;
                        case 'audio':
                            if (imageUrl) {
                                self.previewAudio(imageUrl);
                            }
                            break;
                    }

                    break;
                case 'add-image':

                    self.selectMedia(itemId, 'input');
                    self.showWindowAddImage();

                    break;
                case 'log':

                    self.showLog();

                    break;
                case 'profile':

                    self.showUserProfile();

                    break;
            }

        });

    };

    /**
     *
     * @param type
     */
    this.updateMediaList = function (type) {

        type = type || 'input';
        var $container = $('#wve-list_' + type);
        if (!$container.length) {
            return;
        }
        $container.empty();
        var isIframeMode = window.parent && typeof window.parent.wveExportUrl !== 'undefined';

        $.ajax({
            url: self.options.baseUrl + self.options.requestHandler,
            method: 'GET',
            data: {
                action: 'content_list',
                type: type
            },
            dataType: 'json',
            cache: false
        })
            .done(function (response) {
                if (response.success) {
                    var template = _.template($('#listItemTemplate_' + type).html()),
                        templateEmpty = _.template($('#listEmptyTemplate_' + type).html());

                    if (type === 'input') {
                        inputList = response.data || [];
                    } else {
                        outputList = response.data || [];
                    }
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function (item, index) {
                            item.index = index;
                            item.isIframeMode = isIframeMode;
                            $container.append(template(item));
                        });
                    } else {
                        $container.append(templateEmpty());
                    }
                }
            });
    };

    /**
     * Get list input audio
     */
    this.getListAudio = function() {
        return _.where(inputList, {type: 'audio'});
    };

    /**
     * Delete media file
     * @param itemId
     * @param type
     */
    this.mediaRemove = function (itemId, type) {

        self.confirm('Are you sure you want to remove this item?', function () {

            $.ajax({
                url: self.options.baseUrl + self.options.requestHandler,
                method: 'POST',
                data: {
                    action: 'delete',
                    type: type,
                    itemId: itemId
                },
                dataType: 'json',
                cache: false
            })
                .done(function (response) {
                    if (response.success) {

                        if (type == 'input') {
                            currentMedia = null;
                            mainVideo.pause();
                            mainVideo.src = '';
                            $('#wve-editor-player-time-current').parent().hide();
                        }
                        self.updateUserStat();
                        self.updateMediaList(type);

                    } else {
                        if (response.msg) {
                            self.alert(response.msg, self.getLangString('error'), 'danger');
                        }
                    }
                });
        });
    };

    /**
     * Rename media
     * @param itemId
     * @param type
     */
    this.mediaRename = function (itemId, type) {

        this.getMediaData(itemId, type, function (response) {

            var template = _.template($('#mediaRenameModalTemplate').html());

            $(document.body).append(template({content: response.data.title}));
            var $modal = $('#mediaRenameModal'),
                $inputText = $('input[type="text"]', $modal);

            $modal
                .on('shown.bs.modal', function (e) {
                    $inputText.get(0).focus();
                    var value = $inputText.val();
                    $inputText.val('').val(value);
                })
                .modal('show')
                .on('hidden.bs.modal', function (e) {
                    $modal.remove();
                })
                .find('.js-button-submit')
                .on('click', function (e) {
                    e.preventDefault();

                    var value = $inputText.val();

                    $.ajax({
                        url: self.options.baseUrl + self.options.requestHandler,
                        method: 'POST',
                        data: {
                            action: 'update_media',
                            type: type,
                            itemId: itemId,
                            value: value
                        },
                        dataType: 'json',
                        cache: false
                    })
                        .done(function (response) {
                            if (response.success) {
                                self.updateMediaList(type);
                                $modal.modal('hide');
                            } else {
                                if (response.msg) {
                                    self.alert(response.msg, self.getLangString('error'), 'danger');
                                }
                            }
                        });
                });

        });

    };

    this.exportUrl = function (itemId, type) {
        if (typeof window.parent.wveExportUrl === 'undefined') {
            return;
        }
        this.getMediaData(itemId, type, function (response) {
            if (response.success) {
                window.parent.wveExportUrl(response.data);
            }
        });
    };

    /**
     * Get media data
     * @param itemId
     * @param type
     * @param callback
     */
    this.getMediaData = function (itemId, type, callback) {

        $.ajax({
            url: self.options.baseUrl + self.options.requestHandler,
            method: 'GET',
            data: {
                action: 'select_media',
                type: type,
                itemId: itemId
            },
            dataType: 'json',
            cache: false
        })
            .done(function (response) {
                if (response.success) {
                    if (typeof callback == 'function') {
                        callback(response);
                    }
                } else {
                    if (response.msg) {
                        self.alert(response.msg, self.getLangString('error'), 'danger');
                    }
                }
            });
    };

    /**
     * Play episode
     * @param type
     * @param itemId
     * @param index
     */
    this.playMedia = function (type, itemId, index) {

        if (type === 'episode' && !episodes[index]) {
            return;
        }

        var media, mediaUrl = '';

        if (type === 'episode') {
            media = episodes[index];
            mediaUrl = media.url;
        }

        var template = _.template($('#videoPreviewModalTemplate').html());

        $(document.body).append(template({src: mediaUrl}));
        var $modal = $('#videoPreviewModal'),
            $buttonPlay = $('.js-button-play', $modal),
            videoEl = $('video', $modal).get(0),
            $inputRange = $('input[type="range"]', $modal),
            videoLoaded = false;

        if (type !== 'episode') {

            this.getMediaData(itemId, type, function (response) {
                media = response.data;
                media.time = [0, media.duration_ms];
                videoEl.src = media.url;
            });

        }

        $inputRange
            .on('change', function (event) {
                var value = parseInt(this.value);
                if (event.originalEvent) {
                    var episodeTime = (media.time[1] - media.time[0]) / 1000;
                    videoEl.currentTime = (episodeTime * (value / 100)) + (media.time[0] / 1000);
                }
            });

        $(videoEl)
            .css({visibility: 'hidden'})
            .on('loadedmetadata error', function () {
                $(this).css({visibility: 'visible'});
            })
            .on('canplay', function () {
                if (!videoLoaded) {
                    if (media.time) {
                        this.currentTime = media.time[0] / 1000;
                    }
                    videoLoaded = true;
                    $(this).css({visibility: 'visible'});
                }
            })
            .on('play pause', function () {
                if (this.paused) {
                    $buttonPlay.html('<i class="icon-play3"></i> ' + self.getLangString('play_small'));
                } else {
                    $buttonPlay.html('<i class="icon-pause2"></i> ' + self.getLangString('pause'));
                    if (videoEl.currentTime > media.time[1] / 1000) {
                        videoEl.currentTime = media.time[0] / 1000;
                    }
                }
            })
            .on('timeupdate', {playing: true}, function (event) {
                if (!this.readyState) {
                    return;
                }
                var currentTime = this.currentTime;
                if (currentTime < media.time[0] / 1000) {
                    this.currentTime = media.time[0] / 1000;
                }

                if (currentTime > media.time[1] / 1000
                    && ['episode'].indexOf(type) > -1) {
                        this.pause();
                }
                var percent = (currentTime - (media.time[0] / 1000)) / ((media.time[1] - media.time[0]) / 1000);
                $inputRange.val(percent * 100);
            });

        $buttonPlay
            .on('click', function (e) {
                e.preventDefault();
                if (!videoEl.readyState) {
                    self.alert(self.getLangString('playback_not_possible'), self.getLangString('error'), 'danger');
                    return;
                }
                if (videoEl.paused) {
                    videoEl.play();
                } else {
                    videoEl.pause();
                }
            });

        $modal
            .modal('show')
            .on('hidden.bs.modal', function (e) {
                videoEl.pause();
                $modal.remove();
            });
    };

    /**
     * Play selected episode
     */
    this.playVideoSelected = function () {

        if (!currentMedia || !currentMedia.url) {
            return;
        }

        var values = $sliderTimelineRange.slider('values');
        mainVideo.currentTime = values[0] / 1000;

        if (!mainVideo.readyState) {
            self.alert(self.getLangString('video_format_not_supported'));
            return;
        }
        mainVideo.play();

        this.clearTimers();
        this.interval = setInterval(function () {
            if (mainVideo.currentTime * 1000 >= values[1]) {
                mainVideo.pause();
                mainVideo.currentTime = values[1] / 1000;
            }
        }, 10);
    };

    /**
     * Select media
     * @param itemId
     * @param type
     */
    this.selectMedia = function (itemId, type) {

        mainVideo.pause();

        $.ajax({
            url: self.options.baseUrl + self.options.requestHandler,
            method: 'GET',
            data: {
                action: 'select_media',
                type: type,
                itemId: itemId
            },
            dataType: 'json',
            cache: false
        })
            .done(function (response) {
                if (response.success) {

                    var $container = $('#wve-list_input');
                    $container.find('.list-group-item').removeClass('active');

                    if (response.data) {
                        currentMedia = response.data;
                    }
                    if (response.data.type && response.data.type !== 'video') {
                        self.updateSelectedMedia();
                        return;
                    }

                    $container.find('.btn-link[data-id="' + response.data.id + '"]')
                        .parent('li')
                        .addClass('active');

                    self.updateSelectedMedia();

                } else {
                    if (response.msg) {
                        self.alert(response.msg, self.getLangString('error'), 'danger');
                    }
                }
            });
    };

    /**
     * Show image options window
     * @param array content
     */
    this.showWindowAddImage = function(content) {
        var template = _.template($('#modalImageOptionsTemplate').html()),
            data = {
                title: this.getLangString('image_parameters'),
                buttonText: typeof content !== 'undefined'
                    ? this.getLangString('save')
                    : this.getLangString('add'),
                content: ''
            };

        data.audioList = this.getListAudio();

        $(document.body).append(template(data));
        var self = this,
            $modal = $('#modalImageOptions'),
            $form = $modal.find('form');

        if (content && content.options) {
            Object.keys(content.options).forEach(function(key) {
                var $field = $form.find('[name="' + key + '"]');
                if ($field.is(':checkbox')) {
                    if (content.options[key]) {
                        $field.prop('checked', true);
                    }
                } else {
                    $form.find('[name="' + key + '"]').val(content.options[key]);
                }
            });
        }

        $modal
            .modal('show')
            .on('hidden.bs.modal', function (e) {
                $modal.remove();
            })
            .find('.js-button-submit')
            .on('click', function (e) {
                e.preventDefault();

                var options = self.serializeForm($form);
                options.duration = parseInt(options.duration, 10);

                if (!episodes) {
                    episodes = [];
                }

                if (typeof content !== 'undefined') {

                    content.options = options;

                } else {

                    var data = _.clone(currentMedia);
                    data.options = options;
                    episodes.push(data);

                    currentMedia = null;
                    self.updateEpisodesContent();
                }

                $modal.modal('hide');
            });
    };

    /**
     * Update view on select new video source
     */
    this.updateSelectedMedia = function () {
        if (currentMedia.type !== 'video') {
            if ($sliderTimeline.data('uiSlider')) {
                $sliderTimeline.slider('destroy');
            }
            if ($sliderTimelineRange.data('uiSlider')) {
                $sliderTimelineRange.slider('destroy');
            }
            return;
        }
        var isIntro = !!(currentMedia.isIntro);

        if (!isIntro && !$sliderTimeline.data('uiSlider')) {
            $sliderTimeline.slider({
                step: 1,
                min: 0,
                max: 500
            })
                .on('slidechange', self.onTimelineChange.bind(self));
        }
        if (!isIntro && !$sliderTimelineRange.data('uiSlider')) {
            $sliderTimelineRange.slider({
                range: true,
                step: 1,
                min: 0,
                max: 500,
                values: [0, 500],
                change: self.onTimelineRangeChange.bind(self),
                slide: self.onTimelineRangeSlide.bind(self)
            });
        }

        mainVideo.pause();
        this.clearTimers();

        mainVideo.currentTime = 0;

        if (!isIntro) {
            $('#wve-editor-player-time-current')
                .text('00:00:00');
        }

        if (isIntro) {
            mainVideo.autoplay = 'autoplay';
        } else {
            mainVideo.removeAttribute('autoplay');
        }
        mainVideo.src = currentMedia.url;
    };

    /**
     * Take episode
     */
    this.takeEpisode = function () {

        if (!currentMedia || !currentMedia.url) {
            return;
        }
        if (!episodes) {
            episodes = [];
        }

        var values = $sliderTimelineRange.slider('values');
        var data = _.clone(currentMedia);
        data.time = values;

        episodes.push(data);
        this.updateEpisodesContent();
    };

    /**
     * Make fast cut
     */
    this.makeCutFast = function () {
        if (!currentMedia || !currentMedia.url) {
            return;
        }
        var self = this,
            values = $sliderTimelineRange.slider('values'),
            min = $sliderTimelineRange.slider('option', 'min'),
            max = $sliderTimelineRange.slider('option', 'max');

        if (values[0] === min && values[1] === max) {
            return;
        }

        self.confirm('Attention! Fast cut does not guarantee accuracy and synchronization. Do you want to continue?', function () {

            self.showPreloader();

            $.ajax({
                url: self.options.baseUrl + self.options.requestHandler,
                method: 'POST',
                data: {
                    action: 'cut_fast',
                    itemId: currentMedia.id,
                    from: values[0],
                    to: values[1]
                },
                dataType: 'json',
                cache: false
            })
                .done(function (response) {
                    self.hidePreloader();
                    if (response.success) {
                        self.updateMediaList('output');
                        self.updateUserStat();
                    } else {
                        if (response.msg) {
                            self.alert(response.msg, 'Error', 'danger');
                        }
                    }
                });
        });
    };

    /**
     * Update episodes list content
     */
    this.updateEpisodesContent = function () {

        var self = this,
            $container = $('#wve-episode-container'),
            $containerInner = $('#wve-episode-container-inner');

        $containerInner.empty();
        $container.toggle(episodes.length > 0);

        if (episodes.length === 0) {
            return;
        }

        var template = _.template($('#episodeItemTemplate').html());

        episodes.forEach(function (item, index) {
            item.index = index;
            if (!item.imageUrl) {
                item.imageUrl = '';
                if (item.type === 'video') {
                    var imageUrl = self.options.baseUrl + self.options.requestHandler;
                    imageUrl += '?action=frame_image&time=' + item.time[0] + '&itemId=' + item.id;
                    self.toDataURL(imageUrl, function (result) {
                        item.imageUrl = result;
                        $containerInner
                            .find('.episode-item:eq(' + index + ')')
                            .find('.card-block')
                            .css('background-image', 'url(' + result + ')');
                    });
                } else if (item.type === 'image') {
                    item.imageUrl = item.url;
                }
            }
            $containerInner.append(template(item));
        });
    };

    /**
     * Image URL to Data URL
     * @param url
     * @param callback
     */
    this.toDataURL = function (url, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open('get', url);
        xhr.responseType = 'blob';
        xhr.onload = function () {
            var fr = new FileReader();
            fr.onload = function () {
                callback(this.result);
            };
            fr.readAsDataURL(xhr.response); // async call
        };
        xhr.send();
    };

    /**
     * Remove episode
     * @param index
     */
    this.episodeRemove = function (index) {
        episodes.splice(index, 1);
        self.updateEpisodesContent();
    };

    /**
     * Check process status
     */
    this.checkProcessStatus = function (callback) {

        this.showPreloader();

        $.ajax({
            url: self.options.baseUrl + self.options.requestHandler,
            method: 'GET',
            data: {
                action: 'queue_status'
            },
            dataType: 'json',
            cache: false
        })
            .done(function (response) {
                self.hidePreloader();
                if (response.status && response.status == 'not_logged_in') {
                    clearInterval(self.interval);
                    window.location.reload();
                }
                else if (response.status && ['pending', 'processing'].indexOf(response.status) > -1) {
                    setTimeout(self.showProgress.bind(self), 1);
                }
                else {
                    if (typeof callback == 'function') {
                        callback();
                    }
                }
            });
    };

    /**
     * Render project
     */
    this.renderProject = function () {
        var template = _.template($('#renderModalTemplate').html());
        var data = {title: self.getLangString('create_video'), type: 'render'};
        data.audioList = this.getListAudio();

        $(document.body).append(template(data));
        var $modal = $('#renderModal'),
            projectData = this.getProjectData();

        if (projectData.length === 0) {
            this.alert(self.getLangString('project_is_empty'), self.getLangString('error'), 'danger');
            return;
        }

        var movieTitle, options;

        $modal
            .modal('show')
            .on('hidden.bs.modal', function (e) {
                $modal.remove();
                if (!self.audioPlayer.paused) {
                    self.audioPlayer.pause();
                }
            })
            .on('shown.bs.modal', function() {
                self.updateLibraryContent();
            })
            .find('.js-button-submit')
            .on('click', function (e) {
                e.preventDefault();

                $modal.find('.js-button-submit').prop('disabled', true);

                movieTitle = $('input[name="title"]', $modal).val();
                options = self.serializeForm($('form', $modal));
                projectData = self.getProjectData();

                var $button = $(this);
                $button.prop('disabled', true);
                self.alertClear();

                $.ajax({
                    url: self.options.baseUrl + self.options.requestHandler,
                    method: 'POST',
                    data: {
                        action: 'render',
                        title: movieTitle,
                        options: options,
                        data: JSON.stringify(projectData)
                    },
                    dataType: 'json',
                    cache: false
                })
                    .done(function (response) {
                        $button.prop('disabled', false);
                        if (response.success) {

                            $modal.modal('hide');
                            self.showProgress();

                        } else {
                            if (response.msg) {
                                self.alert(response.msg, self.getLangString('error'), 'danger');
                            }
                            $button.prop('disabled', false);
                        }
                    })
                    .fail(function () {
                        $button.prop('disabled', false);
                    });
            });
    };

    /**
     * Update library content
     */
    this.updateLibraryContent = function() {
        var $container = $('#audioLibrary'),
            $itemsContainer = $('#wve-audio-library'),
            $libraryCategoriesSelect = $('select[name="audio_category"]', $container),
            $paginationContainer = $('.js-container-pagination', $container),
            $audioSelect = $container.closest('.modal.show').find('select[name="audio"]'),
            $libraryField = $container.closest('.modal.show').find('form input[name="audio_library"]'),
            options = {
                category: '',
                page: 1
            };

        $libraryCategoriesSelect.on('change', function() {
            options.page = 1;
            $audioSelect.val('').prop('disabled', false);
            $libraryField.val('');
            if (!self.audioPlayer.paused) {
                self.audioPlayer.pause();
            }
            self.updateLibraryContentRequest($itemsContainer, $paginationContainer, options);
        });

        this.updateLibraryContentRequest($itemsContainer, $paginationContainer, options);
    };

    /**
     *
     * @param $itemsContainer
     * @param $paginationContainer
     * @param options
     */
    this.updateLibraryContentRequest = function($itemsContainer, $paginationContainer, options) {

        var $container = $('#audioLibrary'),
            $libraryCategoriesSelect = $('select[name="audio_category"]', $container),
            template = _.template($('#libraryListItemAudioTemplate').html()),
            $audioSelect = $container.closest('.modal.show').find('select[name="audio"]'),
            $libraryField = $container.closest('.modal.show').find('form input[name="audio_library"]'),
            numberPerPage = 10;

        options.category = $libraryCategoriesSelect.val() || '';

        $.ajax({
            url: self.options.baseUrl + self.options.requestHandler,
            method: 'GET',
            data: {
                action: 'audio_library',
                query: options
            },
            dataType: 'json',
            cache: false
        })
            .done(function(response) {

                if (response.categories && response.categories.length > 0) {
                    $libraryCategoriesSelect.empty();
                    response.categories.forEach(function(categoryName) {
                        $libraryCategoriesSelect.append('<option value="' + categoryName + '">' + categoryName + '</option>');
                    });
                    if (options.category) {
                        $libraryCategoriesSelect.val(options.category);
                    }
                }

                $itemsContainer.empty();

                response.items.forEach(function(item) {
                    var content = template(item);
                    $itemsContainer.append(content);
                });

                $itemsContainer.find('.btn-link[data-file-name]')
                    .on('click', function() {
                        var isActive = $(this).closest('li').is('.active');
                        $itemsContainer.find('li').removeClass('active');
                        if (isActive) {
                            $audioSelect.val('').prop('disabled', false);
                            $libraryField.val('');
                            return;
                        }
                        $(this).closest('li').addClass('active');
                        $audioSelect.val('').prop('disabled', true);
                        $libraryField.val($(this).data('file-name'));
                    });

                self.createPagination($itemsContainer, $paginationContainer, numberPerPage, response.total, options,
                    function($iContainer, $pContainer, opts) {
                        $audioSelect.val('').prop('disabled', false);
                        $libraryField.val('');
                        if (!self.audioPlayer.paused) {
                            self.audioPlayer.pause();
                        }
                        self.updateLibraryContentRequest($iContainer, $pContainer, opts);
                    });

            });
    };

    /**
     * Convert media file
     * @param mediaId
     * @param type
     */
    this.convertMedia = function (mediaId, type) {

        var template = _.template($('#renderModalTemplate').html());
        var data = {title: 'Convert video', type: 'convert', audioList: []};
        $(document.body).append(template(data));
        var $modal = $('#renderModal');

        var options;

        $modal
            .modal('show')
            .on('hidden.bs.modal', function (e) {
                $modal.remove();
            })
            .find('.js-button-submit')
            .on('click', function (e) {
                e.preventDefault();

                options = self.serializeForm($('form', $modal));

                var $button = $(this);
                $button.prop('disabled', true);

                $.ajax({
                    url: self.options.baseUrl + self.options.requestHandler,
                    method: 'POST',
                    data: {
                        action: 'convert',
                        itemId: mediaId,
                        type: type,
                        options: options
                    },
                    dataType: 'json',
                    cache: false
                })
                    .done(function (response) {
                        $button.prop('disabled', false);
                        if (response.success) {

                            $modal.modal('hide');
                            self.showProgress();

                        } else {
                            if (response.msg) {
                                self.alert(response.msg, self.getLangString('error'), 'danger');
                            }
                        }
                    });

            });
    };

    /**
     * Get project data
     * @returns {[]}
     */
    this.getProjectData = function () {
        var data = [];
        if (episodes && episodes.length > 0) {

            episodes.forEach(function (episode) {
                var item = {id: episode.id};
                item.type = episode.type || null;
                item.time = episode.time || null;
                if (episode.options) {
                    if (episode.options.duration) {
                        item.duration = episode.options.duration;
                    }
                    if (episode.options.text) {
                        item.text = episode.options.text;
                    }
                    if (episode.options.auto_split) {
                        item.text_auto_split = true;
                    }
                    if (episode.options.audio) {
                        item.audio = episode.options.audio;
                    }
                }
                data.push(item);
            });

        }
        else if (currentMedia
            && currentMedia.url
            && $sliderTimelineRange.data('uiSlider')) {
                var item = {id: currentMedia.id};
                item.time = $sliderTimelineRange.slider('values');
                data.push(item);
        }
        return data;
    };

    /**
     * Get form data
     * @param form
     * @returns {{}|*}
     */
    this.serializeForm = function (form) {
        var arrayData, objectData;
        arrayData = $(form).serializeArray();
        objectData = {};
        $.each(arrayData, function () {
            var value;
            if (this.value != null) {
                value = this.value;
            } else {
                value = '';
            }
            if (objectData[this.name] != null) {
                if (typeof objectData[this.name] !== 'object') {
                    objectData[this.name] = [objectData[this.name]];
                }
                objectData[this.name].push(value);
            } else {
                objectData[this.name] = value;
            }
        });
        return objectData;
    };

    /**
     * Preview image
     * @param imageUrl
     */
    this.previewImage = function(imageUrl) {

        var template = _.template($('#modalLargeTemplate').html()),
            data = {
                title: self.getLangString('image_preview'),
                content: '<img src="' + imageUrl + '" alt="" style="width: 100%;">'
            };

        $(document.body).append(template(data));
        var $modal = $('#modalLarge');

        $modal
            .modal('show')
            .on('hidden.bs.modal', function (e) {
                $modal.remove();
            });
    };

    /**
     * Play audio
     * @param audioUrl
     */
    this.previewAudio = function(audioUrl) {
        if ($('.modal.show').length === 0) {

            var template = _.template($('#modalSmallTemplate').html()),
                data = {
                    title: self.getLangString('play_audio'),
                    content: '<audio src="' + audioUrl + '" controls autoplay>'
                };

            $(document.body).append(template(data));
            var $modal = $('#modalSmall');

            $modal
                .modal('show')
                .on('hidden.bs.modal', function (e) {
                    $modal.find('audio').get(0).pause();
                    $modal.remove();
                });

        }
        else {
            if (!this.audioPlayer.paused && this.audioPlayer.src.indexOf(encodeURI(audioUrl)) > -1) {
                this.audioPlayer.pause();
            } else {
                this.audioPlayer.pause();
                this.audioPlayer.src = audioUrl;
                this.audioPlayer.play();
            }
        }
    };

    /**
     * Confirm action
     * @param text
     * @param callback
     */
    this.confirm = function (text, callback) {

        var template = _.template($('#modalConfirmTemplate').html());
        $(document.body).append(template({content: text}));
        var $modal = $('#modalConfirm');

        $modal
            .modal('show')
            .on('hidden.bs.modal', function (e) {
                $modal.remove();
            })
            .find('.js-button-submit')
            .on('click', function (e) {
                e.preventDefault();
                if (typeof callback == 'function') {
                    callback();
                }
                $modal.modal('hide');
            });
    };

    /**
     * Alert
     * @param text
     * @param title
     * @param type
     */
    this.alert = function (text, title, type) {

        title = title || self.getLangString('warning');
        type = type || 'warning';

        var icons = {
            danger: 'icon-warning color-red big',
            info: 'icon-info color-blue big'
        };
        var template, $modal,
            icon_class = icons[type] || icons.info;

        if ($('.modal.show').length > 0) {

            template = _.template($('#alertTemplate').html());
            $modal = $('.modal.show');
            var $modalBody = $('.modal.show:first').find('.modal-body');

            var alertHtml = template({
                type: type,
                title: title,
                content: text,
                icon_class: icon_class
            });

            $modalBody.find('.alert').remove();
            $modalBody.append(alertHtml);

        } else {

            template = _.template($('#modalAlertTemplate').html());
            var html = template({
                type: type,
                title: title,
                content: text,
                icon_class: icon_class
            });
            $(document.body).append(html);
            $modal = $('#modalAlert');

            $modal
                .modal('show')
                .on('hidden.bs.modal', function (e) {
                    $modal.remove();
                });
        }
    };

    /**
     * Remove alert message
     */
    this.alertClear = function() {
        if ($('.modal.show').length > 0) {
            var $modalBody = $('.modal.show:first').find('.modal-body');
            $modalBody.find('.alert').remove();
        }
    };

    /**
     * Show progress bar
     */
    this.showProgress = function () {

        if ($('.wve-preloader').length > 0) {
            $('.wve-preloader').remove();
        }

        var html = '<div class="wve-preloader" id="wve-preloader">';
        html += '<div class="wve-preloader-inner">';
        html += '<div class="wve-preloader-caption">' + self.getLangString('processing') + '...</div>';
        html += '<div class="wve-preloader-progress">';
        html += '<div class="progress">';
        html += '<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>';
        html += '</div>';
        html += '<div class="mt-3 text-center">';
        html += ' <button class="btn btn-danger js-button-stop">Stop</button>';
        html += ' <button class="btn btn-secondary js-button-close">Close</button>';
        html += '<div>';
        html += '<div></div></div>';

        $(document.body).append(html);
        var $progressBar = $('.wve-preloader:first'),
            $buttonStop = $progressBar.find('.js-button-stop');

        //Button Close
        $progressBar.find('.js-button-close')
            .on('click', function (e) {
                e.preventDefault();
                self.clearTimers();
                $progressBar.remove();
            });

        //Button Stop
        $buttonStop
            .on('click', function (e) {
                e.preventDefault();

                $buttonStop.prop('disabled', true);
                self.clearTimers();

                $.ajax({
                    url: self.options.baseUrl + self.options.requestHandler,
                    method: 'POST',
                    data: {
                        action: 'processing_stop'
                    },
                    dataType: 'json',
                    cache: false
                })
                    .done(function (response) {
                        if (response.success) {
                            self.clearTimers();
                            self.updateUserStat();
                            self.updateMediaList('input');
                            self.updateMediaList('output');
                            $progressBar.remove();
                        } else {
                            if (response.msg) {
                                alert(response.msg);
                            }
                        }
                    });
            });

        self.clearTimers();
        this.timer = setTimeout(this.updateRenderingProgressData.bind(this), self.options.updateDataInterval);
    };

    /**
     * Update rendering progress data
     */
    this.updateRenderingProgressData = function () {

        var $progressBar = $('.wve-preloader:first'),
            $progressCaption = $progressBar.find('.wve-preloader-caption'),
            status;

        $.ajax({
            url: self.options.baseUrl + self.options.requestHandler,
            method: 'GET',
            data: {
                action: 'queue_status'
            },
            dataType: 'json',
            cache: false
        })
            .done(function (response) {

                if (response.status && response.status === 'not_logged_in') {
                    self.clearTimers();
                    window.location.reload();
                }
                if (typeof response.percent !== 'undefined') {

                    $progressBar.find('.progress-bar')
                        .css('width', response.percent + '%')
                        .toggleClass('progress-bar-empty', response.percent < 7)
                        .text(response.percent + '%');

                    if (typeof response.status !== 'undefined') {
                        status = self.capitalizeFirstLetter(response.status) + '...';
                        if (response.status === 'pending') {
                            status += ' ' + self.getLangString('queue') + ': ' + (response.pendingCount + response.processingCount);
                        }
                        $progressCaption.text(status);
                    } else {
                        $progressCaption.text(self.getLangString('please_wait'));
                    }

                    if (response.percent >= 100 || ( !response.status )) {
                        self.clearTimers();
                        setTimeout(function () {
                            self.hidePreloader();
                            self.updateUserStat();
                            self.updateMediaList('input');
                            self.updateMediaList('output');
                        }, 1000)
                    } else {
                        self.timer = setTimeout(self.updateRenderingProgressData.bind(self), self.options.updateDataInterval);
                    }
                }
            });

    };

    /**
     * Show preloader
     */
    this.showPreloader = function () {

        var html = '<div class="wve-preloader" id="wve-preloader">';
        html += '<div class="wve-preloader-inner">';
        html += '<div class="wve-preloader-caption">Please wait...</div>';
        html += '<div class="wve-preloader-content"><div>';
        html += '</div>';
        html += '</div>';

        $(document.body).append(html);
    };

    /**
     * Hide preloader
     */
    this.hidePreloader = function (delay) {
        delay = delay || 0;
        setTimeout(function () {
            $('#wve-preloader').remove();
        }, delay);
    };

    /**
     * Show log
     */
    this.showLog = function () {

        this.showPreloader();

        var template, $modal;

        $.ajax({
            url: self.options.baseUrl + self.options.requestHandler,
            method: 'GET',
            data: {
                action: 'user_log'
            },
            dataType: 'json',
            cache: false
        })
            .done(function (response) {
                self.hidePreloader();
                if (response.success) {

                    template = _.template($('#modalLargeTemplate').html());
                    var content = '<pre class="code">' + response.content + '</pre>';
                    $(document.body).append(template({title: 'Log', content: content}));
                    $modal = $('#modalLarge');
                    $modal
                        .modal('show')
                        .on('hidden.bs.modal', function (e) {
                            $modal.remove();
                        });

                } else {
                    if (response.msg) {
                        self.alert(response.msg, 'Error', 'danger');
                    }
                }
            });

    };

    /**
     * Get user profile data
     * @param callback
     */
    this.getUserData = function (callback) {
        $.ajax({
            url: self.options.baseUrl + self.options.requestHandler,
            method: 'GET',
            data: {
                action: 'user_profile'
            },
            dataType: 'json',
            cache: false
        })
            .done(function (response) {
                if (response.status && response.status == 'not_logged_in') {
                    clearInterval(self.interval);
                    window.location.reload();
                }
                else if (typeof callback == 'function') {
                    callback(response);
                }
            });
    };

    /**
     * Update user statistics
     */
    this.updateUserStat = function () {

        var template = _.template($('#userStatTemplate').html()),
            $container = $('#wve-user-stat');

        this.getUserData(function (response) {
            if (response.success) {
                $container.html(template(response.data));
            }
        });
    };

    /**
     * Seconds to time
     * @param in_seconds
     * @returns {string}
     */
    this.secondsToTime = function (in_seconds) {

        var hours = Math.floor(in_seconds / 3600);
        var minutes = Math.floor((in_seconds - (hours * 3600)) / 60);
        var seconds = in_seconds - (hours * 3600) - (minutes * 60);
        seconds = seconds.toFixed(2);

        if (hours < 10) hours = '0' + hours;
        if (minutes < 10) minutes = '0' + minutes;
        if (seconds < 10) seconds = '0' + seconds;

        return hours + ':' + minutes + ':' + seconds;
    };

    /**
     * Show user profile
     */
    this.showUserProfile = function () {

        var template = _.template($('#userProfileTemplate').html()),
            modalTemplate = _.template($('#modalAlertTemplate').html()),
            $modal;

        this.showPreloader();

        this.getUserData(function (response) {
            self.hidePreloader();
            if (response.success) {

                var content = template(response.data);
                var html = modalTemplate({
                    title: 'User profile',
                    content: '[content]',
                    icon_class: ''
                });
                html = html.replace('[content]', content);
                $(document.body).append(html);
                $modal = $('#modalAlert');

                $modal
                    .modal('show')
                    .on('hidden.bs.modal', function (e) {
                        $modal.remove();
                    });
            }
        });

    };

    /**
     * First letter uppercase
     * @param string
     * @returns {string}
     */
    this.capitalizeFirstLetter = function (string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    };

    /**
     * Get language string
     * @param {string} langKey
     * @returns {string}
     */
    this.getLangString = function(langKey) {
        if (typeof window.LANG === 'undefined') {
            return langKey;
        }
        return window.LANG[langKey] || langKey;
    };

    this.init();
};
