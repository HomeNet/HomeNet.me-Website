/* Author: 
 * @author Matthew Doll
*/
(function($) {
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    $.filemanager = {
        options: {
            title: "Upload Files", 
            button:"Select"
        },
        
        container: undefined,
        loaded: false,
        selectCount: 0,
        
        init: function(options){
            for(var i in options){
                this.options[i] = options[i];
            }
            return this;
        },
        
        create: function(){
            return;
        },

        getSelected: function(){
            return [];
        }

    };
    
    $.filemanagerupload = $.extend({},$.filemanager, {
        options: {
            title: "Upload Files", 
            button:"Select"
        },

        create: function(){
            
           $('<div id="fileupload">\n\
    <div class="fileupload-buttonbar ui-widget-content ui-corner-all">\n\
        <label class="fileinput-button">\n\
            <span>Add files...</span>\n\
            <input type="file" name="files[]" multiple>\n\
        </label>\n\
        <button type="submit" class="start">Start upload</button>\n\
        <button type="reset" class="cancel">Cancel upload</button>\n\
        <div class="fileupload-progressbar"></div>\n\
    </div>\n\
    <div class="cms-filemanager-container dropzone files"></div>\n\
    </div>').cmsfileupload({
                 url: this.options.rest+'?element=filemanager&method=upload',
                 dropZone: this.container.find('.dropzone')  }).appendTo(this.container);
         //);
        }

    });
    
    $.filemanagerlocal = $.extend({},$.filemanager, {
        options: {
            title: "File Browser",
            button:"Select",
            rest: "",
            folder: "",
            hash: ""
        },
        
        create: function(){
            
            this.itemContainer = $('<ol class="cms-filemanager-container"></ol>');
            this.itemContainer.selectable({
                filter:'li'
            });

            this.itemContainer.bind( "selectableselected",   $.proxy(this._itemSelected,this));
            this.itemContainer.bind( "selectableunselected", $.proxy(this._itemUnselected, this));
            
            this.loadData();
            
            this.container.append(this.itemContainer);
        },

        loadData: function() {
            $.getJSON(this.options.rest, {
                element: "Image", 
                method: "items", 
                folder: this.options.folder, 
                hash: this.options.hash
            },
            $.proxy(this._loadData, this)).error(function() {
                alert("Could Not Load Data");
            })
        },
        
        _loadData: function(data){
            for(var i in data){
                var div = $('<li><img src="'+data[i].thumbnail+'" /></li>');
                div.data(data[i]);
                this.itemContainer.append(div);
            }
            
            this.itemContainer.height(this.container.parent().height()-50);
        },
        
         _itemSelected: function(event,ui){
        
            if(!$(ui.selected).hasClass('cms-selected')){
        
                if(this.selectCount >= this.options.maxItems){
                    //$(event.target).removeClass(class)
                    $(ui.selected).removeClass('ui-selected');
                } else {
                    this.selectCount++;
                    $(ui.selected).addClass('cms-selected');
                }
            // alert(this.selectCount[this.currentTab]);
            }
        
        },
        _itemUnselected: function(event,ui){       
            if($(ui.unselected).hasClass('cms-selected')){
                if(this.selectCount > 0){
                    this.selectCount--;
                } else {
                    this.selectCount = 0;
                }
       
                $(ui.unselected).removeClass('cms-selected');
            }
        },
        
        
        getSelected: function(){
            var items = [];
            this.itemContainer.find(".ui-selected").each(function(index, element){ 
                items.push($(element).data()); 
                $(element).removeClass("ui-selected cms-selected");   
                
            });
            this.selectCount = 0;
            return items;
        }

    });
    
    $.filemanagerflickr = $.extend({}, $.filemanager, {
        options: {
            title: "Flickr",
            button:"Select"
        },

        create: function() {
            
        }

    });
    
})(jQuery);

(function($) {
    
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    $.widget("cms.filemanager", {
        options: {
            type: "image", //any|document
            maxItems: 1,

            managers: {
                upload: $.filemanagerupload.init({}),
                 local: $.filemanagerlocal.init({}),
                flickr: $.filemanagerflickr.init({})
            }

    },
    
    tabsCreated: {},
    
    _create: function() {
           
            var tabs ="";
            var body = [];
        
            this.tabIndexes = [];
            this.selectCount = {};
            
            this.dialog = $('<div class="cms-filemanager"></div>'); 
        
            for(var i in this.options.managers){
                
                this.options.managers[i].init({index: i, maxItems: this.options.maxItems,  rest: this.options.rest, folder: this.options.folder, hash: this.options.hash });
                
                tabs += '<li><a href="#tabs-'+i+'">'+this.options.managers[i].options.title+'</a></li>';
                
                var tab = $('<div id="tabs-'+i+'"></div>');
                
                this.options.managers[i].container = tab;
                body.push(tab);
                this.tabIndexes.push(i);
                this.selectCount[i] = 0;
            }     
                 
            this.dialog.append('<ul >'+tabs+'</ul>');
           for(var i in body){
               this.dialog.append(body[i]);
           }
            
        
            this.dialog.tabs({
                selected: 0
            }); 
        
            this.currentTab = this.tabIndexes[0]; //set current tab
        
            this.dialog.bind( "tabsselect", $.proxy(this._loadTab,this));
    
            this.dialog.bind( "tabscreate", function(){
                alert('Tab Created');
            });
    
            this.dialog.dialog({
                autoOpen: false,
                resizable: false,
                width:620,
                height:550,
                title: "File Manager",
                modal: true,
                zIndex: 500,
                buttons: {
                    Select: $.proxy(this._selectItems,this),
                    Close: function(){
                        $(this).dialog("close");
                    }
                }
            });

        },
        _init: function() {
        // this.dialog.dialog("open");
        },
    
        show: function(){
            
            if(this.first != true){
            this._loadTab(null,{
                index: 0
            });
            this.first = true;
        }
            
            this._updateOption('maxItems',this.options.maxItems);
            
            this.dialog.dialog("open");
            
        },
    
        _updateOption: function(option, value){
            for(var i in this.options.managers){
                this.options.managers[i].options[option] = value;
            }
        },


    
        _loadTab: function(event,ui){
            this.currentTab = this.tabIndexes[ui.index];
            var manager = this.options.managers[this.currentTab];
            
            if(this.tabsCreated[this.currentTab] != true){
                this.tabsCreated[this.currentTab] = true;
                //alert('create'+this.currentTab);
                manager.create();
            }
            
            

            this.dialog.dialog("widget").find(".ui-dialog-buttonset .ui-button-text").first().text(manager.options.button);
        },
    
    
        _selectItems: function(){

        var that = this;
            this.dialog.dialog("close");
            $.each(this.options.managers[this.currentTab].getSelected(), function(index,value){
                that._trigger('selected', null ,value);
            } );

        
        },

        destroy: function() {
            this.dialog.dialog("destroy");
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);


