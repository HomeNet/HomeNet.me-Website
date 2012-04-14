(function($) {
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    $.widget("cms.galleryblockhelper", $.cms.galleryelement, {
        options: {
            
            //folder:"",
            //hash:"",
            type:"",
            url:"",
            id:"",
            name:"image",
            className: "",
            layout: "default",
            maxItems: 100
        },
     
        _create: function() {
            
            this._loadOptions();

            var images = this.element.children();
            this.element.bind('update', $.proxy(this.updateContainer,this));
            //flesh out intial contents
            for (var i = 0; i < images.length; i++) {
                this.addImageContents($(images[i]));
                this.itemCount++;
            }    
          //  console.log('create');
            this.button = $('<a class="cms-button" htrf="#"></a>');
            
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
            if(images.length == this.options.maxItems){
                //  alert('hide select '+this.options.max);
                this.hideSelect();
            }
            this.element.filemanager({
                name: this.options.name,
                //folder: this.options.folder,
                //hash:   this.options.hash,
                id:   this.options.id,
                type:   this.options.type,
                url:   this.options.url,
                maxItems:  this.options.maxItems,
                selected: $.proxy(this.addImage,this)
            }
            );

            
          //  console.log(['gallery',this.element]);
     
        //when a file is selected, called once for each image selected
        // this.element.bind("filemanagerselected",);
            
        },
          addImageContents: function(image){
            
            image.addClass('icon-item ui-widget-content');
            
            var name = this.options.name;
            if(this.options.maxItems != 1){
                name += '['+this.count+']';
            }
            
            
            image.bind('edit', this.edit);
            image.bind('save', this.save);
            image.bind('deletePrompt', this.deletePrompt);
            image.bind('delete', this.deleteImage);
            
           // this.updateImage({data: image},image.data());
           image.trigger('save',image.data());
            
            image.append('<img src="'+image.data('thumbnail')+'">');
            var edit = $('<div class="ui-state-default ui-corner-all edit ui-button"  ><span class="ui-icon ui-icon-pencil"></span></div>');
            // edit.editimage(image.data());
              edit.bind("click.selectimage", function(){
               $(this).trigger('edit');
           });
            var del = $('<div class="ui-state-default ui-corner-all delete ui-button"  ><span class="ui-icon ui-icon-closethick"></span></div>');
            del.bind("click.selectimage",  function(){
               $(this).trigger('deletePrompt');
           });
            //  edit.append('');
            //  '<div class="ui-state-default ui-corner-all content-image-delete ui-button"><span class="ui-icon ui-icon-closethick"></span></div>');
            //  image.children('content-image-edit').click(this.editImage(image));// $.proxy(this, "editImage"));// //this.editImage(image)
            // image.children('content-image-delete').bind("click.selectimage", $.proxy(this.deleteImage()));
            image.append(edit,del);
            this.count++;
            return image;
        },
        
        save: function(event, data){
          // console.log('Update Image');
            var image =  $(this);
            
            image.data(data);
            //this.find(".content-image-alt").html(data.title);
  
            image.attr("data-path", data.path);
            image.attr("data-title", data.title);
            image.attr("data-description", data.description);
            image.attr("data-source", data.source);
            image.attr("data-url", data.url);
            image.attr("data-copyright", data.copyright);
            
            image.attr("data-thumbnail", data.thumbnail);
            image.attr("data-preview", data.preview);
        },

        destroy: function() {
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