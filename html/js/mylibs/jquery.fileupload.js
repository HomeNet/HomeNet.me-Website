/*
 * jQuery File Upload User Interface Plugin 5.0.17
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://creativecommons.org/licenses/MIT/
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global window, document, URL, webkitURL, FileReader, jQuery */

(function ($) {
    'use strict';
    
    // The UI version extends the basic fileupload widget and adds
    // a complete user interface based on the given upload/download
    // templates.
    $.widget('cms.cmsfileupload', $.blueimpUI.fileupload, {

    options: {
       autoUpload: false,
       previewMaxWidth: 100,
       previewMaxHeight: 75,

        errorMessages: {
            maxFileSize: 'File is too big',
            minFileSize: 'File is too small',
            acceptFileTypes: 'Filetype not allowed',
            maxNumberOfFiles: 'Max number of files exceeded'
        },

        add: function (e, data) {
                var that = e.data.fileupload;
                that._adjustMaxNumberOfFiles(-data.files.length);
                data.isAdjusted = true;
                data.isValidated = that._validate(data.files);
                data.context = that._renderUpload(data.files)
                    .appendTo($(this).find('.files')).fadeIn(function () {
                        // Fix for IE7 and lower:
                        $(this).show();
                    }).data('data', data);
                if ((that.options.autoUpload || data.autoUpload) &&
                        data.isValidated) {
                    data.jqXHR = data.submit();
                }
            },
            // Callback for the start of each file upload request:
            send: function (e, data) {
                if (!data.isValidated) {
                    var that =  e.data.fileupload;
                    if (!data.isAdjusted) {
                        that._adjustMaxNumberOfFiles(-data.files.length);
                    }
                    if (!that._validate(data.files)) {
                        return false;
                    }
                }
                if (data.context && data.dataType &&
                        data.dataType.substr(0, 6) === 'iframe') {
                    // Iframe Transport does not support progress events.
                    // In lack of an indeterminate progress bar, we set
                    // the progress to 100%, showing the full animated bar:
                    data.context.find('.ui-progressbar').progressbar(
                        'value',
                        parseInt(100, 10)
                    );
                }
            },
            // Callback for successful uploads:
            done: function (e, data) {
                var that = this;//e.data.fileupload;
                if (data.context) {
                    data.context.each(function (index) {
                        var file = ($.isArray(data.result) &&
                                data.result[index]) || {error: 'emptyResult'};
                        if (file.error) {
                            that._adjustMaxNumberOfFiles(1);
                        }
                        $(this).fadeOut(function () {
                            that._renderDownload([file])
                                .css('display', 'none')
                                .replaceAll(this)
                                .fadeIn(function () {
                                    // Fix for IE7 and lower:
                                    $(this).show();
                                });
                        });
                    });
                } else {
                    that._renderDownload(data.result)
                        .css('display', 'none')
                        .appendTo($(this).find('.files'))
                        .fadeIn(function () {
                            // Fix for IE7 and lower:
                            $(this).show();
                        });
                }
            },
            // Callback for failed (abort or error) uploads:
            fail: function (e, data) {
                var that = e.data.fileupload;
                console.log($(this));
                console.log(e);
                console.log(data);
                that._adjustMaxNumberOfFiles(data.files.length);
                if (data.context) {
                    data.context.each(function (index) {
                        $(this).fadeOut(function () {
                            if (data.errorThrown !== 'abort') {
                                var file = data.files[index];
                                file.error = file.error || data.errorThrown
                                    || true;
                                that._renderDownload([file])
                                    .css('display', 'none')
                                    .replaceAll(this)
                                    .fadeIn(function () {
                                        // Fix for IE7 and lower:
                                        $(this).show();
                                    });
                            } else {
                                data.context.remove();
                            }
                        });
                    });
                } else if (data.errorThrown !== 'abort') {
                    that._adjustMaxNumberOfFiles(-data.files.length);
                    data.context = that._renderUpload(data.files)
                        .css('display', 'none')
                        .appendTo($(this).find('.files'))
                        .fadeIn(function () {
                            // Fix for IE7 and lower:
                            $(this).show();
                        }).data('data', data);
                }
            },
            // Callback for upload progress events:
            progress: function (e, data) {
                if (data.context) {
                    data.context.find('.ui-progressbar').progressbar(
                        'value',
                        parseInt(data.loaded / data.total * 100, 10)
                    );
                }
            },
            // Callback for global upload progress events:
            progressall: function (e, data) {
                $(this).find('.fileupload-progressbar').progressbar(
                    'value',
                    parseInt(data.loaded / data.total * 100, 10)
                );
            },
            // Callback for uploads start, equivalent to the global ajaxStart event:
            start: function () {
                $(this).find('.fileupload-progressbar')
                    .progressbar('value', 0).fadeIn();
            },
            // Callback for uploads stop, equivalent to the global ajaxStop event:
            stop: function () {
                $(this).find('.fileupload-progressbar').fadeOut();
            },
            // Callback for file deletion:
            destroy: function (e, data) {
                var that = e.data.fileupload;
                if (data.url) {
                    $.ajax(data)
                        .success(function () {
                            that._adjustMaxNumberOfFiles(1);
                            $(this).fadeOut(function () {
                                $(this).remove();
                            });
                        });
                } else {
                    that._adjustMaxNumberOfFiles(1);
                    data.context.fadeOut(function () {
                        $(this).remove();
                    });
                }
            }

//            add: function (e, data) {
//                data.submit();
//            },
//            end: function (e, data) {
//       
//            },
//            // Callback for successful uploads:
//            done: function (e, data) {
//
//            },
//            // Callback for failed (abort or error) uploads:
//            fail: function (e, data) {
//            },
//             send: function (e, data) {
//            },
//            
//            destroy: function (e, data) {
//            }
        },
     _initFileUploadButtonBar: function () {
            var fileUploadButtonBar = this.element.find('.fileupload-buttonbar'),
                filesList = this.element.find('.files'),
                ns = this.options.namespace;
//            fileUploadButtonBar
//                .addClass('ui-widget-header ui-corner-top');
            this.element.find('.fileinput-button').each(function () {
                var fileInput = $(this).find('input:file').detach();
                $(this).button({icons: {primary: 'ui-icon-plusthick'}})
                    .append(fileInput);
            });
            fileUploadButtonBar.find('.start')
                .button({icons: {primary: 'ui-icon-circle-arrow-e'}})
                .bind('click.' + ns, function (e) {
                    e.preventDefault();
                    filesList.find('.start button').click();
                });
            fileUploadButtonBar.find('.cancel')
                .button({icons: {primary: 'ui-icon-cancel'}})
                .bind('click.' + ns, function (e) {
                    e.preventDefault();
                    filesList.find('.cancel button').click();
                });
//            fileUploadButtonBar.find('.delete')
//                .button({icons: {primary: 'ui-icon-trash'}})
//                .bind('click.' + ns, function (e) {
//                    e.preventDefault();
//                    filesList.find('.delete button').click();
//                });
        },  
        
        
       _renderUpload: function (files) {
            var that = this,
                options = this.options,
                tmpl = this._renderUploadTemplate(files),
                isValidated = this._validate(files);
            if (!(tmpl instanceof $)) {
                return $();
            }

            return tmpl;
        },  
        
    _renderUploadTemplate: function (files) {
        var that = this,
            rows = $(),
            options = this.options;
            
        $.each(files, function (index, file) {
            file = that._uploadTemplateHelper(file);

            var item = '<div class="cms-filemanager-listitem ui-widget-content ui-corner-all file">\n\
                <div class="thumbnail"></div>\n\
                <div class="details">\n\
                <div class="name">'+file.name+' <span class="size">'+file.sizef+'</span></div>\n\
                <div class="properties">';

            if(file.error){
                item +=  '<span class="error">'+(that.options.errorMessages[file.error] || file.error)+'</span></div><div class="controls">';
            } else {
                item +=  '<div class="progress"></div><div class="controls">\n\
                                 <button class="start">Start</button>';
            } 
            
            item += '<button class="cancel">Cancel</button></div></div>';
            
            var row = $(item);

            if (file.error) {
                row.addClass('ui-state-error');
            }
            
            row.css('display', 'none');
           // if(isValidated){
                row.find('.progress').progressbar();
           // }
            if(options.autoUpload ){ //|| !isValidated
            row.find('.start').button({
                    text: false,
                    icons: {primary: 'ui-icon-circle-arrow-e'}
                });
            } else {
                row.find('.start').remove();
            }
            row.find('.cancel').button({
                    text: false,
                    icons: {primary: 'ui-icon-closethick'}
                });
           
           var node = row.find('.thumbnail');
               that._loadImage(file, function (img) {
                        $(img).hide().appendTo(node).fadeIn();
                    },{
                        maxWidth: options.previewMaxWidth,
                        maxHeight: options.previewMaxHeight,
                        fileTypes: options.previewFileTypes,
                        canvas: options.previewAsCanvas
                    }
                );
          
            rows = rows.add(row);
        });
        return rows;
    },

    _renderDownloadTemplate: function (files) {
        var that = this,
            rows = $();
        $.each(files, function (index, file) {
            file = that._downloadTemplateHelper(file);
            var row = $('<tr class="template-download">' + 
                (file.error ?
                    '<td></td>' +
                    '<td class="name"></td>' +
                    '<td class="size"></td>' +
                    '<td class="error" colspan="2"></td>'
                :
                    '<td class="preview"></td>' +
                    '<td class="name"><a></a></td>' +
                    '<td class="size"></td>' +
                    '<td colspan="2"></td>'
                ) + 
                '<td class="delete"><button>Delete</button></td>' +
                '</tr>');
            row.find('.size').text(file.sizef);
            if (file.error) {
                row.find('.name').text(file.name);
                row.addClass('ui-state-error');
                row.find('.error').text(
                    that.options.errorMessages[file.error] || file.error
                );
            } else {
                row.find('.name a').text(file.name);
                if (file.thumbnail_url) {
                    row.find('.preview').append('<a><img></a>')
                        .find('img').prop('src', file.thumbnail_url);
                    row.find('a').prop('target', '_blank');
                }
                row.find('a').prop('href', file.url);
                row.find('.delete button')
                    .attr('data-type', file.delete_type)
                    .attr('data-url', file.delete_url);
            }
            rows = rows.add(row);
        });
        return rows;
    },
    
     _initEventHandlers: function () {
            $.blueimp.fileupload.prototype._initEventHandlers.call(this);
            var filesList = this.element.find('.files'),
                eventData = {fileupload: this};
            filesList.find('.start')
                .live(
                    'click.' + this.options.namespace,
                    eventData,
                    this._startHandler
                );
            filesList.find('.cancel')
                .live(
                    'click.' + this.options.namespace,
                    eventData,
                    this._cancelHandler
                );
            filesList.find('.delete')
                .live(
                    'click.' + this.options.namespace,
                    eventData,
                    this._deleteHandler
                );
        },
        
         _startHandler: function (e) {
            e.preventDefault();
            var tmpl = $(this).closest('.file'),
                data = tmpl.data('data');
            if (data && data.submit && !data.jqXHR) {
                data.jqXHR = data.submit();
                $(this).fadeOut();
            }
        },
        
        _cancelHandler: function (e) {
            e.preventDefault();
            var tmpl = $(this).closest('.file'),
                data = tmpl.data('data') || {};
            if (!data.jqXHR) {
                data.errorThrown = 'abort';
                e.data.fileupload._trigger('fail', e, data);
            } else {
                data.jqXHR.abort();
            }
        },
        
        _deleteHandler: function (e) {
            e.preventDefault();
            var button = $(this);
            e.data.fileupload._trigger('destroy', e, {
                context: button.closest('.file'),
                url: button.attr('data-url'),
                type: button.attr('data-type'),
                dataType: e.data.fileupload.options.dataType
            });
        },
    
    
    

    _dropZoneActivate: function () {
        this.options.dropZone.addClass(
            'ui-state-active',
            'normal'
        );
        this._dropZoneActive = true;
    },

    _dropZoneDeactivate: function () {
        this.options.dropZone.removeClass(
            'ui-state-active',
            'normal'
        );
        this._dropZoneActive = false;
    },

    _dropZoneHighLight: function (dropZone) {
        dropZone.toggleClass(
            'ui-state-highlight',
            'normal'
        );
    },

    _dropZoneDragEnter: function (e) {
        var fu = e.data.fileupload;
        fu._dropZoneHighLight($(e.target));
    },
    
    _dropZoneDragLeave: function (e) {
        var fu = e.data.fileupload;
        fu._dropZoneHighLight($(e.target));
    },

    _documentDragEnter: function (e) {
        var fu = e.data.fileupload;
        if (!fu._dropZoneActive) {
            fu._dropZoneActivate();
        }
    },

    _documentDragOver: function (e) {
        var fu = e.data.fileupload;
        clearTimeout(fu._dragoverTimeout);
        fu._dragoverTimeout = setTimeout(function () {
            fu._dropZoneDeactivate();
        }, 200);
    },

    _create: function () {
        if (this.options.dropZone && !this.options.dropZone.length) {
            this.options.dropZone = this.element.find('.dropzone-container div');
        }
        $.blueimpUI.fileupload.prototype._create.call(this);
        var ns = this.options.namespace || this.name;
        this.options.dropZone
            .bind('dragenter.' + ns, {fileupload: this}, this._dropZoneDragEnter)
            .bind('dragleave.' + ns, {fileupload: this}, this._dropZoneDragLeave);
        $(document)
            .bind('dragenter.' + ns, {fileupload: this}, this._documentDragEnter)
            .bind('dragover.' + ns, {fileupload: this}, this._documentDragOver);
    },
    
    destroy: function () {
        var ns = this.options.namespace || this.name;
        this.options.dropZone
            .unbind('dragenter.' + ns, this._dropZoneDragEnter)
            .unbind('dragleave.' + ns, this._dropZoneDragLeave);
        $(document)
            .unbind('dragenter.' + ns, this._documentDragEnter)
            .unbind('dragover.' + ns, this._documentDragOver);
        $.blueimpUI.fileupload.prototype.destroy.call(this);
    }

});

}(jQuery));