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
    $.widget("cms.galleryelement", {
        options: {
            
            folder:"",
            hash:"",
            type:"",
            rest:"",
            name:"image",
            className: "",
            layout: "default",
            maxItems: 100
        },
        
        _loadOptions: function(){
            for (var i in this.options)
            {
                if(this.element.data(i) != undefined){
                    this.options[i] = this.element.data(i);
                }
            }
        },
        
        itemCount: 0,
        count: 0,
        
        _create: function() {
            
            this._loadOptions();
            
            this.element.wrap('<div class="ui-widget cms-filemanager '+this.options.className+'" />')
            
            //<div class="cms-element-image" data-name="image" data-alt="" data-path="" data-thumbnail=""></div>

            //if layout is compact set max to 1;
            if(this.options.layout == "compact"){
                this.options.maxItems = 1;
            }
            
            var images = this.element.children();
       
            
            
            //flesh out intial contents
            for (var i = 0; i < images.length; i++) {
                this.addImageContents($(images[i]));
                this.itemCount++;
            }          
            this.button = $('<button class="cms-button"></button>');
            
            var that = this;
                
                this.element.sortable({
                    placeholder: "ui-state-highlight icon-item"
                });
                this.element.addClass('item-container ui-widget-content');
                this.element.disableSelection();
                this.button.text('Select Images');
                this.element.after($('<div class="ui-widget-header cms-footer"></div>').append(this.button));
        
            this.button.button();
            this.button.bind("click.selectimage", $.proxy(this.selectImages,this));
            //this.button.disableDefault

            //hide select if the container is full
            if(images.length == this.options.maxitems){
                alert('hide select '+this.options.max);
                this.hideSelect();
            }
            this.element.filemanager(this.options);

            //right before submit, update order elements
            this.element.parents('form').bind("submit",function(){   
                that.element.children().each(function(index, element){
                    $(element).find('.order').val(index);
                });  
            });
     
            //when a file is selected, called once for each image selected
            this.element.bind("filemanagerselected",$.proxy(this.addImage,this));
            
        },


        selectImages: function(){
            
            var count = this.options.maxItems - this.itemCount;
            if(count <= 0){
                alert("Form Full");
                this.hideSelect();
                return false;
            }
            
            this.element.filemanager("option","maxItems",count);
            this.element.filemanager("show");
           
            return false;
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

            var img = $('<li />');//document.createElement('div');
            //data.order = this.itemCount;
            img.data(data);
            img = this.addImageContents(img); 
            this.element.append(img);
            this.itemCount++;
            if(this.itemCount == this.options.maxItems){
                this.hideSelect();
            }
            
        },
        
        addImageContents: function(image){
            
            image.addClass('icon-item ui-widget-content');
            
            var name = this.options.name;
            if(this.options.maxItems != 1){
                name += '['+this.count+']';
            }
            
            image.append('<img src="'+image.data('thumbnail')+'">'+
                '<input class="path" type="hidden" name="'+name+'[path]"  value="'+image.data('path')+'">'+
                '<input class="title" type="hidden" name="'+name+'[title]"   value="'+image.data('title')+'">'+
                '<input class="source" type="hidden" name="'+name+'[source]"   value="'+image.data('source')+'">'+
                '<input class="url" type="hidden" name="'+name+'[url]"   value="'+image.data('url')+'">'+
                '<input class="copyright" type="hidden" name="'+name+'[copyright]"   value="'+image.data('copyright')+'">'+
                '<input class="order" type="hidden" name="'+name+'[order]" value="'+this.itemCount+'">');
            var edit = $('<div class="ui-state-default ui-corner-all edit ui-button"  ><span class="ui-icon ui-icon-pencil"></span></div>');
            // edit.editimage(image.data());
            edit.bind("click.selectimage", image, $.proxy(this.editPrompt,this));
            var del = $('<div class="ui-state-default ui-corner-all delete ui-button"  ><span class="ui-icon ui-icon-closethick"></span></div>');
            del.bind("click.selectimage", image, $.proxy(this.deletePrompt,this));
            //  edit.append('');
            //  '<div class="ui-state-default ui-corner-all content-image-delete ui-button"><span class="ui-icon ui-icon-closethick"></span></div>');
            //  image.children('content-image-edit').click(this.editImage(image));// $.proxy(this, "editImage"));// //this.editImage(image)
            // image.children('content-image-delete').bind("click.selectimage", $.proxy(this.deleteImage()));
            image.append(edit,del);
            this.count++;
            return image;
        },
        
        editPrompt: function(event){
            
            event.preventDefault();
           var image =  event.data;
            var that = this;
            console.log(that);
            image.imageeditor({
                     name: image.data('name'),
                     size: image.data('size'),
                    owner: image.data('owner'),
                     date: image.data('date'),
                    width: image.data('width'),
                   height: image.data('height'),
                thumbnail: image.data('thumbnail'),
                  preview: image.data('preview'),
                    title: image.data('title'),
              description: image.data('description'),
                   source: image.data('source'),
                url: image.data('url'),
                copyright: image.data('copyright')
//                save: function(event, data){
//                    that.updateImage(event, data);
//                }
            });
            this.element.bind('imageeditorsave', image, this.updateImage);
            return false;
        },
        
        updateImage: function(event, data){
            console.log('Update Image');
            var image =  event.data;
            
            image.data(data);
            //this.find(".content-image-alt").html(data.title);
            image.find("img").attr('alt',data.title);
        //    image.find("input.path").val(data.path);
            image.find("input.title").val(data.title);
            image.find("input.source").val(data.source);
            image.find("input.url").val(data.url);
            image.find("input.copyright").val(data.copyright);
        },

        deletePrompt: function(event){
           // event.preventDefault();
            var self = this;
            //var image = $(event.target).parent().parent();
            var image =  event.data;
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
            return false;
        },
        
        deleteImage: function(image){
            
            this.showSelect();
            image.remove();
            
            this.element.children().each(function(index, element){
                $(element).find('.order').val(index);
            });  
            
            this.itemCount--;
        },
        
        destroy: function() {
            this.element.unwrap();
            this.button.remove();
            var items = this.element.children();
            //  items.unwrap();
            items.each(function(index,element){
                $(element).empty();
                $(element).removeClass("list-item").removeClass("icon-item");
            })
            this.element.next('.cms-footer').remove();
            
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);