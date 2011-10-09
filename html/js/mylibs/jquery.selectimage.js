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
    $.widget("cms.selectimage", {
        options: {
            
            folder:"",
            hash:"",
            type:"",
            rest:"",
            
            className: "",
            layout: "default",
            maxItems: 5
        },
        
        _loadOptions: function(){
            for (var i in this.options)
            {
                if(this.element.data(i) != undefined){
                    this.options[i] = this.element.data(i);
                }
            }
        },
        
        _create: function() {
            
            this.itemCount = 0;
            this.count = 0;
            
            this._loadOptions();
            //<div class="cms-element-image" data-name="image" data-alt="" data-path="" data-thumbnail=""></div>
            this.element.addClass("ui-widget cms-filemanager-"+this.options.layout+" "+this.options.className);
            
            //if layout is compact set max to 1;
            if(this.options.layout == "compact"){
                this.options.maxItems = 1;
            }
            
            var images = this.element.children();
       
            images.wrapAll('<div class="ui-widget-content cms-filemanager-contents" />');
            
            //flesh out intial contents
            for (var i = 0; i < images.length; i++) {
                this.addImageContents($(images[i]));
                this.itemCount++;
            }
            
            this.button = $('<button class="cms-button"></button>');
            
            
            if(this.options.compact == true){
                this.button.append('Select Image');
                this.element.append(this.button);
            } else {
                this.button.append('Select Images');
                this.element.append($('<div class="ui-widget-header cms-footer"></div>').append(this.button));
            }
            this.button.button();
            this.button.bind("click.selectimage", $.proxy(this.selectImages,this));

            //hide select if the container is full
            if(images.length == this.options.maxitems){
                alert('hide select '+this.options.max);
                this.hideSelect();
            }
            this.element.filemanager(this.options);
     
            this.element.bind("filemanagerselected",$.proxy(this.addImage,this));
            
        },
        
        selectImages: function(){
            
            var count = this.options.maxItems - this.itemCount;
            if(count <= 0){
                alert("Form Full");
                this.hideSelect();
                return;
            }
            
            this.element.filemanager("option","maxItems",count);
            this.element.filemanager("show");
        },
         
        showSelect: function(){
            this.button.button("enable");
        },
        hideSelect: function(){
            this.button.button("disable");
        },
        
        
        addImage: function(event, data){
            
            if(this.itemCount >= this.options.maxItems){
                
                alert("Max Items Reached");
                return;
            }
            console.log(data);
            this.itemCount++;
            
            var img = $('<div>');//document.createElement('div');
            img.data(data);
            img = this.addImageContents(img); 
            this.element.find(".cms-filemanager-contents").append(img);

            if(this.itemCount == this.options.maxItems){
                this.hideSelect();
            }
            
        },
        
        addImageContents: function(image){
            
            image.addClass('cms-filemanager-item ui-widget-content');
            
            var name = image.data('name');
            if(this.options.maxItems != 1){
                name += '['+this.count+']';
            }
            
            image.append('<img src="'+image.data('thumbnail')+'">'+
                '<input class="path" type="hidden" name="'+name+'[path]"  value="'+image.data('path')+'">'+
                '<input class="title" type="hidden" name="'+name+'[title]"   value="'+image.data('title')+'">'+
                '<input class="source" type="hidden" name="'+name+'[source]"   value="'+image.data('source')+'">'+
                '<input class="sourceUrl" type="hidden" name="'+name+'[sourceUrl]"   value="'+image.data('sourceUrl')+'">'+
                '<input class="copyright" type="hidden" name="'+name+'[copyright]"   value="'+image.data('copyright')+'">'+
                '<input class="order" type="hidden" name="'+name+'[order]" value="'+image.data('order')+'">');
            var edit = $('<div class="ui-state-default ui-corner-all cms-filemanager-edit ui-button"  ><span class="ui-icon ui-icon-pencil"></span></div>');
            // edit.editimage(image.data());
            edit.bind("click.selectimage", $.proxy(this.editPrompt,this));
            var del = $('<div class="ui-state-default ui-corner-all cms-filemanager-delete ui-button"  ><span class="ui-icon ui-icon-closethick"></span></div>');
            del.bind("click.selectimage", $.proxy(this.deletePrompt,this));
            //  edit.append('');
            //  '<div class="ui-state-default ui-corner-all content-image-delete ui-button"><span class="ui-icon ui-icon-closethick"></span></div>');
            //  image.children('content-image-edit').click(this.editImage(image));// $.proxy(this, "editImage"));// //this.editImage(image)
            // image.children('content-image-delete').bind("click.selectimage", $.proxy(this.deleteImage()));
            image.append(edit,del);
            if(this.options.layout == 'compact'){
                image.append('<div class="content-image-alt">'+image.data('title')+'</div>'+
                    '<div class="content-image-properties">'+''+'</div>');
            }
            this.count++;
            return image;
        },
        
        editPrompt: function(evt){
            var image = $($(evt.target).parent().parent());
            var options = image.data();
            //options.save = ;
            image.imageeditor(options);
            image.bind('imageeditorsave', $.proxy(this.updateImage,image));
        },
        
        updateImage: function(event, data){

            this.data(data);
            // element.css('border', 'solid red');
            this.find(".content-image-alt").html(data.title);
            this.find("img").attr('alt',data.title);
            this.find(".path").val(data.path);
            this.find(".title").val(data.title);
            this.find(".source").val(data.source);
            this.find(".sourceUrl").val(data.sourceUrl);
            this.find(".copyright").val(data.copyright);
        },
        
        
        
        
        deletePrompt: function(event){
            var self = this;
            var image = $(event.target).parent().parent();
            $('<div>Are you sure you want to delete '+image.data('title')+'</div>').dialog({
                resizable: false,
                title: "Delete",
                modal: true,
                buttons: {
                    Delete: function(){
                        self.deleteImage(image);
                        $( this ).dialog( "close" );
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
        //image.remove();
        },
        
        deleteImage: function(image){
            this.showSelect();
            image.remove();
            this.itemCount--;
        },
        
        destroy: function() {
            this.element.removeClass("ui-widget cms-filemanager-"+this.options.layout+" "+this.options.className);
            var items = this.element.find('.cms-filemanager-contents').children();
            items.unwrap();
            items.each(function(index,element){
                $(element).empty();
                $(element).removeClass("cms-filemanager-item")
            })
            this.element.find('.cms-footer').remove();
            
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);