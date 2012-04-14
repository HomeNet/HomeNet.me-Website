(function($) {
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    $.widget("cms.galleryelement", {
        options: {
            
            id:'',
            type:'',
            url:'',
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
            
            this.element.addClass('ui-widget ui-widget-content item-container');
            
            this.element.wrap('<div class="ui-widget cms-filemanager '+this.options.className+'" />')
            
            this.element.bind('update', $.proxy(this.updateContainer,this));
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

            this.button = $('<a class="cms-button" htrf="#"></a>');
            
            var that = this;
                
            this.element.sortable({
                placeholder: "ui-state-highlight icon-item"
            });
           
            this.element.disableSelection();
            this.button.text('Select Images');
            this.element.after($('<div class="ui-widget-header cms-footer"></div>').append(this.button));
        
            this.button.button();
            this.button.bind("click.selectimage", $.proxy(this.selectImages,this));
            //this.button.disableDefault
         //   console.log(['gallery create',this.element.length]);
           // this.button.css('border', 'solid red');
         //  console.log([images.length,this.options.maxItems]);

            //hide select if the container is full
            if(images.length == this.options.maxItems){
              //  alert('hide select '+this.options.max);
                this.hideSelect();
            }
            this.element.filemanager({
                id: this.options.id,
                name: this.options.name,
            //    folder: this.options.folder,
            //    hash:   this.options.hash,
                type:   this.options.type,
                url:   this.options.url,
                maxItems:  this.options.maxItems,
                selected: $.proxy(this.addImage,this)
            }
        );

            //right before submit, update order elements
            this.element.parents('form').first().bind("submit",function(){   
                that.element.children().each(function(index, element){
                    $(element).find('.order').val(index);
                });  
            });
            
          //  console.log(['gallery',this.element]);
     
            //when a file is selected, called once for each image selected
           // this.element.bind("filemanagerselected",);
            
        },


        selectImages: function(e){
           // console.log('selectimages');
            //e.preventDefault();
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

            var img = $('<div />');//document.createElement('div');
            //data.order = this.itemCount;
            var defaultImgData = {path:'', title:'',source:'', url:'', copyright:'', description:''};
            
            
            
            img.data($.extend(defaultImgData, data));
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
            image.bind('edit', this.edit);
            image.bind('save', this.save);
            image.bind('deletePrompt', this.deletePrompt);
            image.bind('delete', this.deleteImage);
            
            
            image.append('<img src="'+image.data('thumbnail')+'">'+
                '<input class="path" type="hidden" name="'+name+'[path]"  value="'+image.data('path')+'">'+
                '<input class="title" type="hidden" name="'+name+'[title]"   value="'+image.data('title')+'">'+
                '<input class="source" type="hidden" name="'+name+'[source]"   value="'+image.data('source')+'">'+
                '<input class="url" type="hidden" name="'+name+'[url]"   value="'+image.data('url')+'">'+
                '<input class="copyright" type="hidden" name="'+name+'[copyright]"   value="'+image.data('copyright')+'">'+
                '<input class="order" type="hidden" name="'+name+'[order]" value="'+this.itemCount+'">');
            var edit = $('<div class="ui-state-default ui-corner-all edit ui-button"  ><span class="ui-icon ui-icon-pencil"></span></div>');
            // edit.editimage(image.data());
           // edit.bind("click.selectimage", image, $.proxy(this.editPrompt,this));
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
        
        edit: function(event){
            //event.preventDefault();
           var image =  $(this);
            var data = image.data();
            //console.log(that);
            image.imageeditor({image:{
                     name: data.name,
                     size: data.size,
                    owner: data.owner,
                     date: data.date,
                    width: data.width,
                   height: data.height,
                   
                   path: data.path,
                   
                thumbnail: data.thumbnail,
                  preview: data.preview,
                    
                    title: data.title,
              description: data.description,
                   source: data.source,
                      url: data.url,
                copyright: data.copyright
            },
                 save: function(event, data){
                    image.trigger('save', data);
                }
            });
          //  this.element.bind('imageeditorsave', image, this.updateImage);
            return false;
        },
        
        save: function(event, data){
            //console.log('Update Image');
            var image =  $(this)
            
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
            var image =  $(this);
            $('<div>Are you sure you want to delete &quot;'+image.data('title')+'&quot;</div>').dialog({
                resizable: false,
                title: "Delete",
                modal: true,
                buttons: {
                    Delete: function(){
                        image.trigger('delete');
                        $( this ).dialog( "close" );
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
            return false;
        },
        deleteImage:function(){
            //console.log('deleteImage');
          var parent = $(this).parent();
          $(this).remove(); 
          parent.trigger('update');
        },
        
        updateContainer: function(e){
            //  console.log('updateContainer');
            //console.log(['deleteImage',image, this]);
            this.showSelect();
            
            
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