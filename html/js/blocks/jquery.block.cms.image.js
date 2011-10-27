(function($) {
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    
    // var cmsImageEditor = undefined;
    if($.cmsBlocks == undefined){
        $.cmsBlocks = {};
    }
    
    
    
    $.cmsBlocks['cms.image'] = 
    {
        options: {
            title: "Image Block",
            className: "cms-block-image"
        },
        data: {
            id:"",
            name:"",
            path:"",
            type:"",
  
            thumbnail:"",
            preview:"",
                    
            title:"", 
            description:"", 
            source:"",
            url:"",
            copyright:"", 
            
            width:"", 
            height:"",
            
            owner:"", 
            fullname:""
        },
          
        create: function(){                    
                   
            // block = $(this).data('block');
           // console.log(['img create', this]);
            var data = this.data;
            this.element.append('<img src="'+data.preview+'" alt="'+data.title+'" />\n\
                        <div class="overlay"><div class="properties">\n\
                            <div><label>Title:</label><span class="title">'+data.title+'</span></div>\n\
                            <div><label>Description:</label><span class="description">'+data.description+'</span></div>\n\
                            <div><label>Source:</label><span class="source">'+data.source+'</span></div>\n\
                            <div><label>Url:</label><span class="url">'+data.url+'</span></div>\n\
                            <div><label>Copyright:</label><span class="copyright">'+data.copyright+'</span></div>\n\
                        </div></div>');
        },
        
        configure: function(){
            var self =this;
            this.element.imageeditor({
                image: this.data,
                save: function(e, data){ 
                    self.update(data);
                 //   self.element.trigger('save');
                },
                cancel: function(){
                  //  console.log('cancel');
                  //  self.element.trigger('cancel');
                }
            });
                
        //                function(e, data){ 
        //                            console.log(['imgeditor save',block, this, data, block.element.get(0)]);
        //                            block.element.css('border','solid red')}
                        
        // $(this).unbind('imageeditorsave');
        // $(this).bind('imageeditorsave', function(){ $(this).css('border','solid red'); });//$.proxy(block.update, this)
        },
        update: function(data){
        
            this.save(data);
            
            this.element.find('.title').text(this.data.title);
            this.element.find('.description').text(this.data.description);
            this.element.find('.source').text(this.data.source);
            this.element.find('.url').text(this.data.url);
            this.element.find('.copyright').text(this.data.copyright); 
        }
    }
            
})(jQuery);