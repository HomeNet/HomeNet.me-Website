/* Author: 
 * @author Matthew Doll
 * 
 * /* jQuery.contentEditable Plugin
Copyright © 2011 FreshCode
http://www.freshcode.co.za/

*/ 
(function($) {
   
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    $.widget("cms.wysiwyg", {
        options: {
            toolbar: "bold italic strike removeformat | insertLink insertImage blockquote code "+
        "| unorderedlist orderedlist indent outdent superscript subscript " +
        "section paragraph h2 h3 h4 fontcolor" //h1 //h5
        },
        actions: {
            bold: {
                text: 'B',
                title: "Bold",
                action: function(){
                    document.execCommand("bold", false, null);
                },
                iconClass: 'cms-icon-bold',
                hotkey: 'ctrl+b'
            },
            italic: {
                text: 'I',
                title: 'Italicize',
                action: function(){
                    document.execCommand('italic', false, null);
                },
                iconClass: 'cms-icon-italic',
                hotkey: 'ctrl+i'
            },
            underline: {
                text: 'U',
                title: 'Underline',
                action: function(){
                    document.execCommand('underline', false, null);
                },
                iconClass: 'cms-icon-underline',
                hotkey: 'ctrl+u'
            },
            strike: {
                text: "S",
                title: "Strike",
                action: function(){
                    document.execCommand('strikeThrough', false, null);
                    return false;
                },
                iconClass: 'cms-icon-strike'
            },
            removeformat: {
                text: '&minus;',
                title: 'Remove Formating',
                action: function(){
                    document.execCommand('removeFormat', false, null);
                    return false;
                },
                iconClass: 'cms-icon-erase',
                hotkey: 'ctrl+m'
            },
            /////////////////////////////////
            insertLink: {
                text: "L",
                title: "Insert Link to a web page",
                action: function(){
                    //   var urlPrompt = prompt("Enter URL:", "http://");
                    // document.execCommand("createLink", false, urlPrompt);
                    alert(this.getSelectionContainerElement());
                    return false;
                },
                iconClass: 'cms-icon-link',
                hotkey: 'ctrl+l'
            },
            insertImage: {
                text: "I",
                title: "Insert Image",
                init: function(){
                    console.log('init Image');
                    this.editor.filemanager({
                        maxItems: 20, 
                        folder: this.element.data('folder'), 
                        rest: this.element.data('rest'), 
                        hash: this.element.data('hash')
                        });
                    var self = this;
                    this.editor.bind("filemanagerselected",function(event,data){
                        try{
                            document.execCommand("enableObjectResizing", false, false);
                        } catch (e){}
                        
                        console.log(data);

                        self.insertBlock('cms.image', data);
                    });
                },
                
                
                action: function(){
                    console.log('insert IMage');
                    
                    
                    this.editor.filemanager("show");
                //  var urlPrompt = prompt("Enter Image URL:", "http://");
                //document.execCommand("InsertImage", false, urlPrompt); 
                // this._insertHtml('<div style="width: 100px; height:100px; background: #f00; float:left">dfsdsfsf</div>');
                },
                iconClass: 'cms-icon-image',
                hotkey: 'ctrl+g'
            },
            blockquote: {
                text: "&nbsp;",
                title: "Blockquote",
                action: function(){
                    document.execCommand("FormatBlock", null, '<blockquote>');
                },
                iconClass: 'cms-icon-quote',
                hotkey: 'ctrl+q'
            },
            code: {
                text: "&nbsp;",
                title: "Code",
                action: function(){
                    document.execCommand("FormatBlock", null, '<pre>');
                },
                iconClass: 'cms-icon-code',
                hotkey: 'ctrl+alt+k'
            },
            ///////////////////////////////
            unorderedlist: {
                text: "U",
                title: "Unordered List",
                action: function(){
                    document.execCommand("InsertUnorderedList", false, null);
                },
                iconClass: 'cms-icon-ul',
                hotkey: 'ctrl+alt+u'
            },
            orderedlist: {
                text: "O",
                title: "Ordered List",
                action: function(){
                    document.execCommand("InsertOrderedList", false, null);
                },
                iconClass: 'cms-icon-ol',
                hotkey: 'ctrl+alt+o'
            },
            indent: {
                text: ">",
                title: "Indent",
                action: function(){
                    document.execCommand("indent", false, null);
                },
                iconClass: 'cms-icon-indent',
                hotkey: 'tab'
            },
            outdent: {
                text: "<",
                title: "Outdent",
                action: function(){
                    document.execCommand("outdent", false, null);
                },
                iconClass: 'cms-icon-outdent',
                hotkey: 'shift+tab'
            },
            superscript: {
                text: "x<sup>2</sup>",
                title: "Superscript",
                action: function(){
                    document.execCommand("superscript", false, null);
                },
                iconClass: 'cms-icon-superscript',
                hotkey: 'ctrl+.'
            },
            subscript: {
                text: "x<sub>2</sub>",
                title: "Subscript",
                action: function(){
                    document.execCommand("subscript", false, null);
                },
                iconClass: 'cms-icon-subscript',
                hotkey: 'ctrl+shift+.'
            },
            //////////////////////////////
            section: {
                text: "S",
                title: "Section",
                action: function(){ 
                    // document.execCommand("FormatBlock", null, '<div>');
             
                    console.log('wrapping');
                    console.log($().wrapSelection().parentsUntil( this.editor));
                    document.createElement('section');//this fixes html5 issue in ie8
                    $().wrapSelection().parentsUntil('.editor').filter('p, h1, h2, h3, h4, h5, hr, ul, ol').wrapAll('<div class="section" />'); //
                    //  $().wrapSelection().parentsUntil('.editor').wrapAll('<div class="section" />');
                    this.editor.find('.selection').replaceWith(function() {
                        return $(this).contents();
                    }); //remove selection tags
                },
                iconClass: 'cms-icon-section'
            },
            paragraph: {
                text: "P",
                title: "Paragraph",
                action: function(){
                    document.execCommand("FormatBlock", null, '<p>');
                },
                iconClass: 'cms-icon-paragraph',
                hotkey: 'ctrl+alt+0'
            },
            h1: {
                text: "H<sub>1</sub>",
                title: "Heading 1",
                action: function(){
                    document.execCommand("FormatBlock", null, '<h1>');
                },
                iconClass: 'cms-icon-h1',
                hotkey: 'ctrl+alt+1'
            },
            h2: {
                text: "H<sub>2</sub>",
                title: "Heading 2",
                action: function(){
                    document.execCommand("FormatBlock", null, '<h2>');
                },
                iconClass: 'cms-icon-h2',
                hotkey: 'ctrl+alt+2'
            },
            h3: {
                text: "H<sub>3</sub>",
                title: "Heading 3",
                action: function(){
                    document.execCommand("FormatBlock", null, '<h3>');
                },
                iconClass: 'cms-icon-h3',
                hotkey: 'ctrl+alt+3'
            },
            h4: {
                text: "H<sub>4</sub>",
                title: "Heading 4",
                action: function(){
                    document.execCommand("FormatBlock", null, '<h4>');
                },
                iconClass: 'cms-icon-h4',
                hotkey: 'ctrl+alt+4'
            },
            h5: {
                text: "H<sub>5</sub>",
                title: "Heading 5",
                action: function(){
                    document.execCommand("FormatBlock", null, '<h5>');
                },
                iconClass: 'cms-icon-h5',
                hotkey: 'ctrl+alt+5'
            },
            ////////////////////////
            undo: {
                text: "U",
                title: "Undo",
                action: function(){
                    document.execCommand("FormatBlock", null, '<h4>');
                },
                iconClass: 'cms-icon-undo'
            },
            redo: {
                text: "Redo",
                title: "Redo",
                action: function(){
                    document.execCommand("FormatBlock", null, '<h5>');
                },
                iconClass: 'cms-icon-redo'
            },
            
            fontcolor: {
                text: "Font Color",
                title: "Font Color",
                action: function(){
                    $().wrapSelection().removeClass('selection').css('color','#f00');
                // this.editor.find('.selection').unwrap();
                    
                },
                iconClass: 'cms-icon-fontcolor'
            }
            
            
        },
        
        defaultBlock: {
            title: "CMS Block",
            className: "cms-block-image",
            contents: undefined,
            data: {},
            
            init: function(data){
                this.data = $.extend(this.data,data);
              //  this.element = $(this);
         //       element.data('block',this);
               // this.$self.data(this);
            },
            
            
            compact: function(){
                var block = $(this).data('block');
                block.element.empty();
            },
            expand: function(){
                
                 var block = $(this).data('block');
                    block.element = $(this);
                    $(this).data('block', block);
                    
                console.log(['default expand', this, block]);    

                // this.create.call(this);
                block.create.call(this);
                    
                block.element.children().wrapAll('<div class="ui-widget-content ui-state-default"/>');
                block.element.prepend('<div class="ui-widget-header handle"><span class="ui-icon ui-icon-carat-2-n-s"></span> '+block.title+'</div>');
            
                //data.order = this.itemCount;
                //self.data(block.data);
            
                block.element.bind('edit',   block.edit);
                block.element.bind('update', block.update);
                block.element.bind('save',   block.save);
                block.element.bind('remove', block.remove);
                block.element.bind('collapse',block.collapse);
                //$self.unbind('expand');
                //$self.bind('expand', block.expand);


                var edit = $('<a class="ui-state-default ui-corner-all edit ui-button"><span class="ui-icon ui-icon-pencil"></span></a>');
                var del = $('<a class="ui-state-default ui-corner-all delete ui-button"><span class="ui-icon ui-icon-closethick"></span></a>');
                block.element.append(edit,del);
 
                edit.bind("click.block", function(e){
                    console.log(['click edit', this, block]);
                    $(this).trigger('edit');
                });
                
                del.bind("click.selectimage", function(e){
                    $('<div>Are you sure you want to delete<br /> &quot;'+block.title+'&quot;</div>')
                    .dialog({
                        resizable: false,
                        title: "Delete",
                        modal: true,
                        buttons: {
                            Delete: function(){
                                block.element.trigger('remove');
                                $(this).dialog( "close" );
                            },
                            Cancel: function() {
                                $(this).dialog( "close" );
                            }
                        }
                    });                    
                });
                
                block.element.addClass('expanded');
                block.element.find('*').andSelf().attr('contentEditable',false);
                block.element.disableSelection();
              //  console.log(['default expand2', block, block.data]);    

            },
            create: function(){ },
            edit: function(){},
            save: function(e, data){
                var block = $(this).data('block');
                $.extend(block.data,data);
                
                console.log(['default save', this, block]);
                for(var i in block.data){
                    block.element.attr('data-'+i, block.data[i]);
                }
              //  this.$self.data('block',block);
                    
            },
            update: function(e, data){
               var block = $(this).data('block');
               console.log(['default update', this, block]);
               block.element.trigger('save');
            },
            remove: function(e){
               var block = $(this).data('block');
               console.log(['default remove', this, block]);
               block.element.unbind();
               block.element.empty().remove();
            }
        },   
        
        blocks: {
            'cms.image': {
                title: "Image Block",
                data: {
                    name:"",
                    type:"",
                    source:"",
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
                options: {
                    
                },
                create: function(e){                    
                   
                    block = $(this).data('block');
                    console.log(['img create', this, block]);
                    var data = block.data;
                    block.element.append('<img src="'+data.preview+'" alt="'+data.title+'" />\n\
                        <div class="overlay"><div class="properties">\n\
                            <div><label>Title:</label><span class="title">'+data.title+'</span></div>\n\
                            <div><label>Description:</label><span class="description">'+data.description+'</span></div>\n\
                            <div><label>Source:</label><span class="source">'+data.source+'</span></div>\n\
                            <div><label>Url:</label><span class="url">'+data.url+'</span></div>\n\
                            <div><label>Copyright:</label><span class="copyright">'+data.copyright+'</span></div>\n\
                        </div></div>');
                },
                edit: function(e){
                    var block = $(this).data('block');
                   
                    console.log(['img edit', this, block]);
                    
                    $(this).imageeditor({
                        image: block.data,
                        save: function(e, data){ 
                            block.element.trigger('update',data);
                        }
                    });
                
//                function(e, data){ 
//                            console.log(['imgeditor save',block, this, data, block.element.get(0)]);
//                            block.element.css('border','solid red')}
                        
                   // $(this).unbind('imageeditorsave');
                   // $(this).bind('imageeditorsave', function(){ $(this).css('border','solid red'); });//$.proxy(block.update, this)
                },
                update: function(e, data){
                    var block = $(this).data('block');
                    console.log(['img update',this, block]);

                    block.element.trigger('save', data);
                    block.element.find('.title').text(data.title);
                    block.element.find('.description').text(data.description);
                    block.element.find('.source').text(data.source);
                    block.element.find('.url').text(data.url);
                    block.element.find('.copyright').text(data.copyright); 
                }
            },
            'cms.html': {
                title: "HTML Block",
                data: undefined,
                create: function(data){  },
                edit: function(){
                    alert('trigger edit')
                    },
                save: function(){}
            },
            'cms.gallery': {
                title: "Gallery Block",
                data: undefined,
                create: function(e, block){ /*$(this).append('<img src="'+$(this).data('preview')+'" alt="'+$(this).data('title')+'"  contentEditable="false" />');*/ },
                edit: function(){
                    alert('trigger edit')
                    },
                save: function(){}
            }
            
        },
    
        _create: function() {
            
            //disable style with css; would rather use existing style sheet to style strong/em, and fix b-> strong later
            try { //http://stackoverflow.com/questions/536132/stylewithcss-for-ie
                document.execCommand("styleWithCSS", 0, false);
            } catch (e) {
                try {
                    document.execCommand("useCSS", 0, true);
                } catch (e) {
                    try {
                        document.execCommand('styleWithCSS', false, false);
                    }
                    catch (e) {
                    }
                }
            }

            
            
            
            
            
            var that = this;
            this.container = this.element.wrap('<div class="ui-widget cms-wysiwyg" />');
            
            if(this.element.is("textarea")){
                this.element.hide();
                this.editor = $('<div />');
           
                this.parent('form').bind('submit',function(){
                    that.element.val(that.editor.html());
                });
                
            } else {
                this.editor = this.element;
            }
            
            
            this.editor.addClass("ui-widget-content");
 
            //build toolbar
            var sets = this.options.toolbar.split("|");
            
            this.menubar = $('<div class="toolbar" class="" />');
            for(var i in sets){
                var seg = $('<span class="ui-buttonset" />').appendTo(this.menubar);
                var bits = sets[i].split(" ");
                for(var j in bits){
                    var item = this.actions[bits[j]];
                    if(item != undefined){
                        //call init
                        if(item.init){
                            $.proxy(item.init,this)();
                        }
                        
                        
                        $('<a href="#" class="ui-button ui-widget ui-state-default ui-state-disabled ui-button-icon-only" title="'+(item.title?item.title:'')+(item.hotkey?' ('+item.hotkey+')':'')+'"><span class="cms-icon '+(item.iconClass?item.iconClass:'')+'"></span></a>')
                        .bind("click", bits[j],$.proxy(this._action,this))
                        .appendTo(seg);
                    }
                }
                

                
            }

            // = $(bar);
            
            this.menubar.children().each(function(index, element){
                var items = $(element).children();
                // items.disableSelection();
                items.first().addClass("ui-corner-left");
                items.last().addClass("ui-corner-right");
            });//.//wrapAll('</div>');
            this.menubar.disableSelection();
            // 
            this.editor.before(this.menubar.wrap('<div class="toolbar-wrapper"></div>'));
            this.menubar.wrap('<div class="toolbar-wrapper"></div>')
            // this.container.prepend(this.menubar);

            $(window).scroll(function () {
                var docTop = $(window).scrollTop();

                var toolbarTop = that.menubar.offset().top;
                if (docTop > toolbarTop) {
                    that.menubar.css({
                        "position": "fixed", 
                        "top": "0"
                    });
                } else if(toolbarTop == 0) {
                    that.menubar.css("position", "relative");
                }
            });
            this.enable();
              
            this.editor.sortable({ 
                //containment:this.editor, 
                items: 'p, h2, h3, ul, ol, div.container', 
                placeholder: 'ui-state-highlight', 
                forcePlaceholderSize: true,
                handle: '.handle',
                axis: 'y',
                // cursorAt: 'left',
                cursor: 'move'
            });
        //              this.editor.find('p, h2, h3, ul, ol').live({
        //                  load: function() {
        //                  },
        //                  
        //                  mouseenter: function() {
        //                   $(this).append('<div class="handle">handle</div>')
        //                  },
        //                  mouseleave: function() {
        //                     $(this).find('.handle').remove();
        //                  }});
                      
        },
        enabled: false,
        enable : function(){
            
            var items = this.menubar.find('a');
            items.bind("mouseover.wysiwyg", function(){
                $(this).addClass('ui-state-hover')
                });
            items.bind("mouseout.wysiwyg", function(){
                $(this).removeClass('ui-state-hover ui-state-active')
                });
            items.bind("mousedown.wysiwyg", function(){
                $(this).addClass('ui-state-active')
                });
            items.bind("mouseup.wysiwyg", function(){
                $(this).removeClass('ui-state-active')
                });
            
            
            items.removeClass('ui-state-disabled');
            this.editor.attr("contentEditable", "true").addClass("editor");
            this.enabled = true;
        },
        disable : function(){
            this.enabled = false;
            this.menubar.find('a').addClass('ui-state-disabled').unbind('.wysiwyg');
            this.editor.attr("contentEditable", false).removeClass("editor");
        },
        _init: function() {
        // this.dialog.dialog("open");
        },
        
       
            
        _action: function(event){
            event.stopPropagation();
            event.preventBubble = true;
            ;
            event.preventDefault();
            this.editor.focus();
            if(this.enabled){
                this.actions[event.data].action.call(this);
            }
            return false;
        },
        
        //        _getSelection: function() {
        //            console.log(document.createRange());
        //            if (this.getRangeAt)
        //		return this.getRangeAt(0);
        //            else { // Safari!
        //		var range = document.createRange();
        //		range.setStart(this.anchorNode,selectionObject.anchorOffset);
        //		range.setEnd(this.focusNode,selectionObject.focusOffset);
        //		return range;
        //            }
        //            
        //            
        //            //           if ($.browser.msie) return document.selection;
        //            //            return document.getSelection();
        //        },

     
        


        insertBlock: function(name, data){
           // var def = $.extend({},this.defaultBlock);
           var base = Object.create(this.blocks[name]);
           var block = $.extend(true,{},this.defaultBlock,base);
           
        //   block.init(data);
           
            //$.extend(true, block.data, data);
            
            //  console.log(['insertBlock',block, base, this.defaultBlock]);
            
         //   b.data('block', block);
           var b = $(document.createElement('div'));
           b.addClass('ui-widget container '+block.className);
          //  b.data('block',block);
           // 
            console.log(["insert block",b]);
           
           
            block.init(data);
            b.data('block', block);
            b.bind('expand', block.expand); 
            this._insertBlock(b);
            b.trigger('expand');
            //  img.append();
           
          //  return b;
        },
        
        _insertBlock: function(block, prepend){
            this.editor.focus();
            console.log('insert block');
            var element = this._getSelectionContainerElement();
            
            //console.log(element);


            if(element == this.editor.get(0)){
                return this.editor.prepend(block);
            }

            if(prepend){
                return $(element).parentsUntil('.editor, section').last().before(block); //last() fixes issues with inserting into lists
            } else {
                return $(element).parentsUntil('.editor, section').last().after(block);
            }
        },


        _insertHtml: function(html){
            this.editor.focus(); //fixes insert bug in IE
            if($.browser.msie){
               
                var range = document.selection.createRange();
                    

                if ($(range.parentElement()).parents('.editor').is('*')) {
                    try {
                        // Overwrite selection with provided html
                        range.pasteHTML(html);
                    } catch (e) { }
                } else {
                    this.editor.append(html);
                }
            //document.selection.createRange().collapse(); 

            //  
            //  document.createRange().pasteHTML(html);
            } else {
                window.getSelection().collapseToStart();
                document.execCommand("insertHtml", false, html)
            }
        },
    
        _getSelectionContainerElement: function() {
            var range, sel, container;
            if ($.browser.msie) {
                // IE case
                range = document.selection.createRange();
                return range.parentElement();
            //   return range.parentElement();
            } else if (window.getSelection) {
                sel = window.getSelection();
                sel.collapseToStart();
                if (sel.getRangeAt) {
                    if (sel.rangeCount > 0) {
                        range = sel.getRangeAt(0);
                    }
                } else {
                    // Old WebKit selection object has no getRangeAt, so
                    // create a range from other selection properties
                    range = document.createRange();
                    range.setStart(sel.anchorNode, sel.anchorOffset);
                    range.setEnd(sel.focusNode, sel.focusOffset);

                    // Handle the case when the selection was selected backwards (from the end to the start in the document)
                    if (range.collapsed !== sel.isCollapsed) {
                        range.setStart(sel.focusNode, sel.focusOffset);
                        range.setEnd(sel.anchorNode, sel.anchorOffset);
                    }
                }
                return range.commonAncestorContainer;
            }
        },
        
        _getDomTree: function(){
            var range = this. _getSelectionContainerElement();
            var tree = [];
            if (range) {
                

                // Check if the container is a text node and return its parent if so
                if(container.nodeType === 3){
                    container =  container.parentNode
                }
                while (container != this.editor.get(0)){
                    tree.push(container.tagName);
                    container =  container.parentNode;  
                } 
   
                var path = tree.pop();
                tree = tree.reverse()
                for(var i in tree){
                    path += ' > '+tree[i];
                }
                return path;
            } 
            return null;
        },
        _collapseAll: function(){
            
        },
        _expandAll: function(){
            
        },


        //        replaceSelection: function(text) {
        //
        //            if($.browser.msie){
        //                this.focus();
        //                document.selection.createRange().text = text;
        //                return this;
        //            } else {
        //                this.innerHTML = this.value.substr(0, this.selectionStart) + text + this.value.substr(this.selectionEnd, this.value.length);
        //                return this;
        //            }
        //            
        //            
        //            
        //        },
        
        
        // getRange - gets the current text range object
        //  function getRange(editor) {
        //    if (ie) return getSelection(editor).createRange();
        //    return getSelection(editor).getRangeAt(0);
        //  }
        //
        //  // getSelection - gets the current text range object
        //  function getSelection(editor) {
        //    if (ie) return editor.doc.selection;
        //    return editor.$frame[0].contentWindow.getSelection();
        //  }
        // selectedHTML - returns the current HTML selection or and empty string
        //  function selectedHTML(editor) {
        //    restoreRange(editor);
        //    var range = getRange(editor);
        //    if (ie)
        //      return range.htmlText;
        //    var layer = $("<layer>")[0];
        //    layer.appendChild(range.cloneContents());
        //    var html = layer.innerHTML;
        //    layer = null;
        //    return html;
        //  }
        //
        //  // selectedText - returns the current text selection or and empty string
        //  function selectedText(editor) {
        //    restoreRange(editor);
        //    if (ie) return getRange(editor).text;
        //    return getSelection(editor).toString();
        //  }

        
        

        destroy: function() {
            //this.dialog.dialog("destroy");
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);