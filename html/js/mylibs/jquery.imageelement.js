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
            this.element.bind('update', $.proxy(this.updateContainer,this));
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
            
            var data = image.data();
            var name = this.options.name;
            if(this.options.maxItems != 1){
                name += '['+this.count+']';
            }
            
                     var item = '<div class="thumbnail"><img src="'+data.thumbnail+'" alt="'+data.title+'"></div>\n\
                <div class="details">\n\
                <div class="title">'+data.title+'</div>\n\
                <div class="properties"></div>\n\
                    <div class="controls">\n\
                        <button class="delete">Remove</button>\n\
                        <button class="edit">Start</button>\n\
                    </div>';
            
            var row = $(item);
            
            image.bind('edit', this.edit);
            image.bind('save', this.save);
            image.bind('deletePrompt', this.deletePrompt);
            image.bind('delete', this.deleteImage);

            
            row.find('.edit').button({
                    text: false,
                    icons: {primary: 'ui-icon-pencil'}
            }).bind('click', function(){
               $(this).trigger('edit');
               return false;
           });
          
            row.find('.delete').button({
                    text: false,
                    icons: {primary: 'ui-icon-closethick'}
             }).bind('click', function(){
               $(this).trigger('delete');
               return false;
           });
            
            image.append(row);
            
            image.append('<input class="path" type="hidden" name="'+name+'[path]"  value="'+data.path+'">'+
                '<input class="title" type="hidden" name="'+name+'[title]"   value="'+data.title+'">'+
                '<input class="description" type="hidden" name="'+name+'[description]"   value="'+data.description+'">'+
                '<input class="source" type="hidden" name="'+name+'[source]"   value="'+data.source+'">'+
                '<input class="url" type="hidden" name="'+name+'[url]"   value="'+data.url+'">'+
                '<input class="copyright" type="hidden" name="'+name+'[copyright]"   value="'+data.copyright+'">'+
                '<input class="order" type="hidden" name="'+name+'[order]" value="'+this.itemCount+'">');
            this.count++;
            return image;
        },
        save: function(event, data){

            $.cms.galleryelement.prototype.save.call(this, event, data);

//            this.find("span.path").val(data.path);
            $(this).find("div.title").text(data.title);
//            this.find("span.source").val(data.source);
//            this.find("span.url").val(data.url);
//            this.find("span.copyright").val(data.copyright);
        }
        
       
    });
})(jQuery);