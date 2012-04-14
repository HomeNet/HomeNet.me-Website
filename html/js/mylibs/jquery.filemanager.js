/* Author: 
 * @author Matthew Doll
*/
(function($) {
    
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    $.filemanager = {
        options: {
            elementid:undefined,
            url:undefined,
            title: "Files", 
            button:"Select",
            progressBar: undefined,
            maxItems:0
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
        loadTab: function(){
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
          //  console.log('Create Upload');
            $('<div id="fileupload">\n\
    <div class="fileupload-buttonbar ui-widget-content ui-corner-all">\n\
        <span class="fileinput-button">\n\
            <span>Add files...</span>\n\
            <input type="file" name="files[]" multiple>\n\
        </span>\n\
        <button type="submit" class="start">Start upload</button>\n\
        <button type="reset" class="cancel">Cancel upload</button>\n\
    </div>\n\
    <div class="cms-filemanager-container dropzone files"></div>\n\
    </div>').appendTo(this.container);
            this.container.cmsfileupload({
                url: this.options.url,//+'?element=filemanager&method=upload&folder='+this.options.folder+'&hash='+this.options.hash,
                formData: {
                    element: "FileManager", 
                    method: "upload", 
                    id: this.options.elementid
                },
                dropZone: this.container.find('.dropzone'),
                progressBar: this.options.progressBar
                });
                
        //);
        },
        loadTab: function(){
            this.container.cmsfileupload('option','maxNumberOfFiles', this.options.maxItems);
        },
        getSelected: function(){
            var items = [];
            this.container.find(".ui-selected").each(function(index, element){ 
                
                items.push($(element).data()); 
                $(element).remove();   
                
            });
            this.selectCount = 0;
            //console.log(items);
            return items;
        }

    });
    
   
    $.filemanagerlocal = $.extend({},$.filemanager, {
        options: {
            title: "File Browser",
            button:"Select",
            id: "",//for some reason this is needed, it's not inherting right'
            url: ""//for some reason this is needed, it's not inherting right'
           // folder: "",
           // hash: ""
        },
        
        create: function(){
         //   console.log(['createfilemanager']);
            var that = this;
            this.itemContainer = $('<ol class="item-container"></ol>');
            this.itemContainer.selectable({
                filter:'li',
                selected: function(event,ui){
                    var item = $(ui.selected);
                    item.removeClass('ui-state-hover');
                    if(!item.hasClass('ui-state-highlight')){
        
                        if(that.selectCount >= that.options.maxItems){
                            //$(event.target).removeClass(class)
                            item.removeClass('ui-selected')
                            .removeClass('ui-state-highlight');
                        } else {
                            that.selectCount++;
                            item.addClass('ui-state-highlight');
                        }
                    }
                },
                unselected: function(event, ui){

                    var item = $(ui.unselected);
                    item.removeClass('ui-state-hover');
                    if(item.hasClass('ui-state-highlight')){
                        if(that.selectCount > 0){
                            that.selectCount--;
                        } else {
                            that.selectCount = 0;
                        }
       
                        item.removeClass('ui-state-highlight');
                    }
                },
                 
                selecting: function(event, ui){
                    $(ui.selecting)    
                    .addClass('ui-state-hover');
                },

                unselecting: function(event, ui){
                    $(ui.unselecting)
                    .removeClass('ui-state-hover');
                }
            });
            this.loadData();
            this.container.append(this.itemContainer);
           
        },

        loadData: function() {
          //  console.log(['loadData',this.options]);
            $.getJSON(this.options.url, {
                element: "FileManager", 
                method: "items", 
                id: this.options.elementid
               // folder: this.options.folder, 
               // hash: this.options.hash
            },
            $.proxy(this._loadData, this)).error(function() {
                //console.log();
                alert("Could Not Load Data");
            })
        },
        
        _loadData: function(data){
            for(var i in data){
                var div = $('<li class="icon-item"><img src="'+data[i].thumbnail+'" /></li>');
                div.data(data[i]);
                div.addClass('ui-state-default');
                this.itemContainer.append(div);
            }
            
            this.itemContainer.height(this.container.parent().height()-50);
        },
    
        getSelected: function(){
            var items = [];
            this.itemContainer.find(".ui-selected").each(function(index, element){ 
               // console.log($(element).data());
                items.push($(element).data()); 
                $(element).removeClass("ui-selected cms-selected");   
                
            });
            this.itemContainer.find(".ui-state-highlight").removeClass("ui-state-highlight").addClass("ui-state-default");
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
   $.cmsTabCount = 0;
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    $.widget("cms.filemanager", {
        options: {
            name: "",
            type: "image", //any|document
            maxItems: 1,

            managers: {
                upload: $.filemanagerupload,//.init({}),
                 local: $.filemanagerlocal,//.init({}),
                flickr: $.filemanagerflickr//.init({})
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
                
                var id = 'tabs-'+this.options.name+'-'+i+'-'+$.cmsTabCount;
                $.cmsTabCount++;
                this.options.managers[i] = $.extend(true, {}, this.options.managers[i]);
                //console.log(this.options)
                this.options.managers[i].init({
                    id:id,
                    elementid: this.options.id,
                    index: i, 
                    maxItems: this.options.maxItems,  
                    url: this.options.url
                   // folder: this.options.folder, 
                  //  hash: this.options.hash
                    });
                
                tabs += '<li><a href="#'+id+'">'+this.options.managers[i].options.title+'</a></li>';
                
                var tab = $('<div id="'+id+'"></div>');
                
                this.options.managers[i].container = tab;
                body.push(tab);
                this.tabIndexes.push(i);
                this.selectCount[i] = 0;
            }     
                 
            this.dialog.append('<ul >'+tabs+'</ul>');

            for(var i in body){
                this.dialog.append(body[i]);
            }
            
            var that = this;
            this.dialog.tabs({
                selected: 0,
                select: $.proxy(that._loadTab,that)
            }); 
        
            this.currentTab = this.tabIndexes[0]; //set current tab
            this.currentIndex = 0;
        
            //this.dialog.bind( "tabsselect", );
    
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
            
            this.progressBar = $('<div class="cms-filemanager-progressbar" style="width:400px; float:left; margin-top:10px; "></div>');
            this.progressBar.hide().progressbar();
            this.dialog.dialog('widget').find(".ui-dialog-buttonpane").prepend(this.progressBar);
            this._updateOption('progressBar',   this.progressBar);

        },
        _init: function() {
        // this.dialog.dialog("open");
        },
    
        show: function(){
            this._updateOption('maxItems',this.options.maxItems);
            this._loadTab(null,{
                index: this.currentIndex
                });
            this.dialog.dialog("open");
        },
    
        _updateOption: function(option, value){
            for(var i in this.options.managers){
                this.options.managers[i].options[option] = value;
            }
        },

        _loadTab: function(event,ui){
           // console.log('show tab');
            var that = this;
          //  var that = $(this).data('filemanager');
            
            that.currentIndex = ui.index;
            that.currentTab = that.tabIndexes[ui.index];
            var manager = that.options.managers[that.currentTab];
          //  console.log(['show tab',this.tabsCreated,manager.options.id]);
            if(that.tabsCreated[manager.options.id] != true){
                that.tabsCreated[manager.options.id] = true;
                //alert('create'+this.currentTab);
                manager.create();
            }
            
            manager.loadTab();

            that.dialog.dialog("widget").find(".ui-dialog-buttonset .ui-button-text").first().text(manager.options.button);
        },
    
    
        _selectItems: function(){

            var that = this;
            this.dialog.dialog("close");
            //console.log(['managers', this.options.managers]);
            
            $.each(this.options.managers[this.currentTab].getSelected(), function(index,value){
                
               // console.log(value);
                 that._trigger('selected', null ,value);
            } );
        },

        destroy: function() {
            this.dialog.dialog("destroy");
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);