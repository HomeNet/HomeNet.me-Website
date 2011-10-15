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

(function($) {
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    
    // var cmsEditImageDialog = undefined;
    
    $.widget("cms.imageeditor", {
        options: {
            width: 1000,
            height: 1000,
            owner: "Matthew Doll",
            date: "3/5/2006",
            path: false,
            preview: "/image.php",
            crop: false,
            title: "",
            source: "",
            url: "",
            copyright: ""
        },
        
        
        
        _init: function() {
            if($.cmsEditImageDialog == undefined){
                $.cmsEditImageDialog = $('<div id="cms-image-editor"></div>'); 
                $.cmsEditImageDialog.dialog({
                    autoOpen: false,
                    resizable: false,
                    width:550,
                    height:600,
                    title: "Edit Image",
                    modal: true,
                    buttons: {
                        Save: $.proxy(this.saveImage,this),
                        Cancel: $.proxy(this.closeEditor,this)
                    }
                });
            }
       
            this.openEditor();
        },
        openEditor: function(){
            $.cmsEditImageDialog.dialog('option','title', "Edit "+this.options.name);
            $.cmsEditImageDialog.html('<img id="preview" src="'+this.options.preview+'" alt="Preview"/>\n\
  <fieldset><legend><span id="name">'+this.options.name+'</span> Properties</legend>\n\
<div class="properties">Width: <span id="width">'+this.options.width+'</span> Height: <span id="height">'+this.options.height+'</span> Uploaded By: <span id="owner">'+this.options.owner+'</span> <span id="date">'+this.options.date+'</span></div>\n\
<div><label for="title">Image Title:</label><input type="text" value="'+this.options.title+'" id="title"></div>\n\
<div><label for="source">Source:</label><input type="text" value="'+this.options.source+'" id="source"></div>\n\
<div><label for="url">Source Url:</label><input type="text" value="'+this.options.url+'" id="url"></div>\n\
<div><label for="copyright">Copyright:</label><input type="text" value="'+this.options.copyright+'" id="copyright"></div>\n\
</fieldset>')
            // $.cmsEditImageDialog.setOption('image', this);
            $.cmsEditImageDialog.dialog("open");
        // alert('Open Editor');
        },
        saveImage: function(){
           var dialog = $.cmsEditImageDialog;
            var data = {
                    title: dialog.find("input#title").val(),
              description: dialog.find("input#description").val(),
                   source: dialog.find("input#source").val(),
                url: dialog.find("input#url").val(),
                copyright: dialog.find("input#copyright").val()
            };
            this._trigger('save',null, data);
            
            this.closeEditor();
        },
        closeEditor: function(){
            $.cmsEditImageDialog.dialog("close");
        },
     
        destroy: function() {
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);