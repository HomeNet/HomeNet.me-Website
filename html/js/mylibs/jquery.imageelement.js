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
    $.widget("cms.imageelement", $.cms.galleryelement, {
        options: {
            maxItems: 1,
            layout: 'compact'
        },
        
        
        _create: function() {

            this._loadOptions();
            
            this.element.wrap('<div class="ui-widget cms-filemanager cms-element-image" />')
            
            //<div class="cms-element-image" data-name="image" data-alt="" data-path="" data-thumbnail=""></div>
            
            var images = this.element.children();

            //flesh out intial contents
            for (var i = 0; i < images.length; i++) {
                this.addImageContents($(images[i]));
                this.itemCount++;
            }          
            this.button = $('<button class="cms-button"></button>');
            
            var that = this;

            this.button.text('Select Image');
            this.element.after(this.button);
            
            this.button.button();
            this.button.bind("click.selectimage", $.proxy(this.selectImages,this));
            //this.button.disableDefault

            //hide select if the container is full
            if(this.itemCount == this.options.maxItems){
                this.hideSelect();
            }
            this.element.filemanager(this.options);

            this.element.parents('form').bind("submit",function(){   
                that.element.children().each(function(index, element){
                    $(element).find('.order').val(index);
                });  
            });
     
            this.element.bind("filemanagerselected",$.proxy(this.addImage,this));
        },


        showSelect: function(){
            this.button.show();
        },
        hideSelect: function(){
            this.button.hide();
        },
        
        addImageContents: function(image){
            
            image.addClass('list-item ui-widget-content ui-corner-all');
            
            var name = this.options.name;
            if(this.options.maxItems != 1){
                name += '['+this.count+']';
            }
            
                     var item = '<div class="thumbnail"><img src="'+image.data('thumbnail')+'" alt="'+image.data('title')+'"></div>\n\
                <div class="details">\n\
                <div class="title">'+image.data('title')+'</div>\n\
                <div class="properties"></div>\n\
                    <div class="controls">\n\
                        <button class="delete">Remove</button>\n\
                        <button class="edit">Start</button>\n\
                    </div>';
            
            var row = $(item);

            
            row.find('.edit').button({
                    text: false,
                    icons: {primary: 'ui-icon-pencil'}
            }).bind('click', image,$.proxy(this.editPrompt, this));
          
            row.find('.delete').button({
                    text: false,
                    icons: {primary: 'ui-icon-closethick'}
             }).bind('click', image,$.proxy(this.deletePrompt,this));
            
            image.append(row);
            
            image.append('<input class="path" type="hidden" name="'+name+'[path]"  value="'+image.data('path')+'">'+
                '<input class="title" type="hidden" name="'+name+'[description]"   value="'+image.data('description')+'">'+
                '<input class="title" type="hidden" name="'+name+'[title]"   value="'+image.data('title')+'">'+
                '<input class="source" type="hidden" name="'+name+'[source]"   value="'+image.data('source')+'">'+
                '<input class="url" type="hidden" name="'+name+'[url]"   value="'+image.data('url')+'">'+
                '<input class="copyright" type="hidden" name="'+name+'[copyright]"   value="'+image.data('copyright')+'">'+
                '<input class="order" type="hidden" name="'+name+'[order]" value="'+this.itemCount+'">');
            this.count++;
            return image;
        },
        updateImage: function(event, data){

            $.cms.galleryelement.prototype.updateImage.call(this, event, data);

//            this.find("span.path").val(data.path);
            event.data.find("div.title").text(data.title);
//            this.find("span.source").val(data.source);
//            this.find("span.url").val(data.url);
//            this.find("span.copyright").val(data.copyright);
        }
        
       
    });
})(jQuery);