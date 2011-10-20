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
    
    // var cmsImageEditor = undefined;
    
    $.widget("cms.imageeditor", {
        options: {
            image: {}
        },
        
        data: {
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

        _create: function() {
        //_init: function() {
            console.log('imageeditor create');
         if($.cmsImageEditor == undefined){
            $.cmsImageEditor = $('<div id="cms-image-editor"></div>'); 
                $.cmsImageEditor.dialog({
                    autoOpen: false,
                    resizable: false,
                    width:550,
                    height:600,
                    title: "Edit Image",
                    modal: true,
                    buttons: {
                        Save: function(){},
                        Cancel: function(){}
                    }
                });
           }
       
           // this.open();
        },
        _init: function(){
            console.log(['imageeditor init',this, this.element.get(0)]);
            if(this.options.image){
                this.data = $.extend(this.data,this.options.image);
            }
            this.open();
        },
        
        
        open: function(){
             
      
            $.cmsImageEditor.dialog('option','title', "Edit "+this.data.name);
            
            $.cmsImageEditor.dialog('option','buttons', {
                Save: $.proxy(this.save,this),
                Cancel: $.proxy(this.close,this)
            });
            
            $.cmsImageEditor.html('<img id="preview" src="'+this.data.preview+'" alt="Preview"/>\n\
  <fieldset><legend><span id="name">'+this.data.name+'</span> Properties</legend>\n\
<div class="properties">Width: <span id="width">'+this.data.width+'</span> Height: <span id="height">'+this.data.height+'</span> Uploaded By: <span id="owner">'+this.data.owner+'</span> <span id="date">'+this.data.date+'</span></div>\n\
<div><label for="title">Image Title:</label><input type="text" value="'+this.data.title+'" class="title"></div>\n\
<div><label for="description">Image Title:</label><input type="text" value="'+this.data.description+'" class="description"></div>\n\
<div><label for="source">Source:</label><input type="text" value="'+this.data.source+'" class="source"></div>\n\
<div><label for="url">Source Url:</label><input type="text" value="'+this.data.url+'" class="url"></div>\n\
<div><label for="copyright">Copyright:</label><input type="text" value="'+this.data.copyright+'" class="copyright"></div>\n\
</fieldset>');
            // $.cmsImageEditor.setOption('image', this);
            $.cmsImageEditor.dialog("open");
        // alert('Open Editor');
        },
        save: function(){
           var dialog = $.cmsImageEditor;
           this.data = $.extend(this.data,{
                    title: dialog.find("input.title").val(),
              description: dialog.find("input.description").val(),
                   source: dialog.find("input.source").val(),
                      url: dialog.find("input.url").val(),
                copyright: dialog.find("input.copyright").val()
            });
            this._trigger('save',{}, this.data);
            this.close();
        },
        close: function(){
            $.cmsImageEditor.dialog("close");
        },
     
        destroy: function() {
            $.cmsImageEditor.dialog('destory');
            $.cmsImageEditor = undefined;
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);