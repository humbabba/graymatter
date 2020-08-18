
let toolsArray = [
    {class:'fas fa-bold',tool: 'bold',title: 'Bold'},
    {class:'fas fa-italic',tool: 'italic',title: 'Italic'},
    {class:'fas fa-underline',tool: 'underline',title: 'Underline'},
    {class:'fas fa-strikethrough toolbar-spacer',tool: 'strikeThrough',title: 'Strikethrough'},
    {class:'fas fa-minus toolbar-spacer',tool: 'insertHorizontalRule',title: 'Horizontal rule'},
    {class:'fas fa-link',tool: 'createLink',title: 'Link'},
    {class:'fas fa-unlink toolbar-spacer',tool: 'unlink',title: 'Unlink'},
    {class:'fas fa-indent',tool: 'indent',title: 'Indent'},
    {class:'fas fa-outdent toolbar-spacer',tool: 'outdent',title: 'Outdent'},
    {class:'fas fa-list-ol',tool: 'insertOrderedList',title: 'Ordered list'},
    {class:'fas fa-list-ul toolbar-spacer',tool: 'insertUnorderedList',title: 'Unordered list'},
    {class:'fas fa-text-height',tool: 'fontSize',title: 'Font size'},
    {class:'fas fa-palette toolbar-spacer',tool: 'foreColor',title: 'Font color'},
    {class:'fas fa-align-center',tool: 'justifyCenter',title: 'Center'},
    {class:'fas fa-align-justify',tool: 'justifyFull',title: 'Justify'},
    {class:'fas fa-align-left',tool: 'justifyLeft',title: 'Aling left'},
    {class:'fas fa-align-right toolbar-spacer',tool: 'justifyRight',title: 'Align right'},
    {class:'fas fa-subscript',tool: 'subscript',title: 'Subscript'},
    {class:'fas fa-superscript toolbar-spacer',tool: 'superscript',title: 'Superscript'},
    {class:'fas fa-minus-circle',tool: 'clearFormat',title: 'Clear all formatting'},
    {class:'fas fa-code',tool: 'toggleCode',title: 'Toggle code view'},
];

initTextEdtitors = (callback = false) => {
  //Turn hidden inputs with 'text-editor' class into rich-text editors
  $('input[type="hidden"]').each(function(index,item) {
      if($(item).hasClass('text-editor')) {

        //Bail if it's already a text-editor
        let itemParent = $(item).parent();
        if(itemParent.hasClass('textEditorMasterDiv')) {
          return;
        }

        let newElement = $(item).clone();
        let fancyEditor = makeTextEditor(newElement,callback);
        let toolbar = fancyEditor.find('.toolbar');

        insertMoreTools(toolbar);

        $(item).replaceWith(fancyEditor);
        processToolbarForWidth(toolbar);
      }
  });
}

insertMoreTools = toolbar => {
  let moreToolsContainer = $('<span class="more-tools-container">');
  moreToolsContainer.on('click',function() {
    $(this).next('.more-tools-holder').fadeToggle();
  });
  let moreToolsButton = $('<i class="toolbar-button fas fa-ellipsis-h" title="Show/hide tools">');
  let moreToolsHolder = $('<div class="more-tools-holder">')
  moreToolsContainer.append(moreToolsButton);
  moreToolsContainer.insertAfter(toolbar);
  moreToolsHolder.insertAfter(moreToolsContainer);
}

//Depending on container width, hide tools that don't fit and display button to toggle them
processToolbarForWidth = toolbar => {
  let moreToolsHolder = toolbar.parent().find('.more-tools-holder');
  let toolbarWidth = toolbar.outerWidth();
  let childrenWidth = 0;
  let children = toolbar.children();
  let childrenIndexMax = children.length - 1;
  let moreToolsContainer = toolbar.next('.more-tools-container');
  let widthModifier = moreToolsContainer.outerWidth(true);
  children.each(function(index,item) {
    let child = $(item);
    childrenWidth += child.outerWidth(true);

    //We leave room for the moreToolsContainer, unless we're on the last tool.
    //If it fits, we don't need the "more" button
    if(index === childrenIndexMax) {
      widthModifier = 0;
    }
    if(childrenWidth > (toolbar.outerWidth() - widthModifier)) {
      moreToolsHolder.append(child);
      moreToolsContainer.show();
    }
  });
}

//Handle window resize events viz. text-editors
//This will make sure the toolbars display correctly
$(window).resize(function() {
  //Find them all
  let textEditors = $('.textEditorMasterDiv');

  textEditors.each(function(index,el) {
    //Find the hidden input at the core of each
    let hiddenInput = $(el).find('input[type="hidden"]').first();
    //Reset hidden input in DOM instead of text-editor
    $(el).replaceWith(hiddenInput);
  });

  //Reinitialize all text editors
  initTextEdtitors(textEditorDefaultCallback);
});

makeTextEditor = (el,callback = false) => {
    //Div to hold editor-input combo
    let editor = $('<div class="textEditorMasterDiv">');

    //Toolbar div and tools
    let toolbar = $('<div class="toolbar">');
    $(toolsArray).each(function(index,item) {
        let tool = $('<i class="toolbar-button">');
        tool.addClass(item.class);
        tool.prop('title',item.title);
        tool.on('mousedown',function(e) {
            e.preventDefault();
            let input = null;
            let copyDiv,codeDiv;
            switch(item.tool) {
                case 'createLink':
                    input = prompt('Enter URL:');
                    if(input) {
                      document.execCommand(item.tool,false,input);
                    }
                    break;
                case 'fontSize':
                    input = prompt('Text size:','(Integer from 1 to 7)');
                    if(input) {
                      document.execCommand(item.tool,false,input);
                    }
                    break;
                case 'foreColor':
                    input = prompt('Hexidecimal color value for text:','#000000');
                    if(input) {
                      document.execCommand(item.tool,false,input);
                    }
                    break;
                case 'clearFormat':
                    copyDiv = $(this).parent().parent().find('.fancy-text-div').first();
                    codeDiv = $(this).parent().parent().find('.code-editor').first();
                    let numberOfLinks = copyDiv.find('a').length;
                    let targetInput = copyDiv.next('.text-editor');
                    let x = 0;
                    while(copyDiv.find('*').length > numberOfLinks) {
                        copyDiv.children().each(function(index,el) {
                            stripTags(el);
                        });
                        x++;
                        if(x>30) {
                            break;
                        }
                    }
                    copyDiv.html('<p>' + copyDiv.html().replace(/&nbsp;/g, ' ') + '</p>'); //Put the stripped code back inside a p tag
                    codeDiv.val(copyDiv.html()); //Put the stripped code into the editor
                    targetInput.val(copyDiv.html());
                    showUnsavedFlag();
                    break;
                case 'toggleCode':
                    copyDiv = $(this).parent().parent().find('.fancy-text-div').first();
                    codeDiv = $(this).parent().parent().find('.code-editor').first();
                    let buttonsToToggle = $(this).parent().parent().find('.toolbar-button').not('.fa-code');
                    let spacersToToggle = $(this).parent().parent().find('.spacer');
                    buttonsToToggle.toggle();
                    spacersToToggle.toggle();
                    copyDiv.toggle();
                    codeDiv.toggle();
                    break;
                default:
                    document.execCommand(item.tool,false,input);
                    break;
            }
        });
        toolbar.append(tool);
    });

    //Make edit elements
    let codeEditArea = $('<textarea style="display:none">');
    codeEditArea.addClass('code-editor');
    let editArea = $('<div contenteditable="true">');
    editArea.addClass('fancy-text-div');
    editor.append(toolbar);
    editor.append(codeEditArea);
    editor.append(editArea);

    //The code-editable area
    if(el.val()) {
        //Get the existing value in there
        codeEditArea.val(el.val());
    } else {
        //Start editArea with a P element so the first line gets wrapped
        codeEditArea.val('<p><br></p>');
    }
    //Make it so updates to the editArea affect the original el's value
    codeEditArea.on('input',function() {
        el.val($(this).val());
        editArea.html($(this).val());
    });
    if(callback) {
        codeEditArea.on('keydown',function() {
          this.editAreaContent = $(this).val();
        }).on('keyup',function() {
          this.newEditAreaContent = $(this).val();
          if(this.editAreaContent != this.newEditAreaContent) { //We have changes to content, so run the callback
            callback();
          }
        });
    }

    //The editable area
    if(el.val()) {
        //Get the existing value in there
        editArea.html(el.val());
    } else {
        //Start editArea with a P element so the first line gets wrapped
        editArea.html($('<p><br></p>'));
    }
    //Make it so updates to the editArea affect the original el's value
    editArea.on('input',function() {
        el.val($(this).html());
        codeEditArea.val($(this).html());
    });
    if(callback) {
        editArea.on('keydown',function() {
          this.editAreaContent = $(this).html();
        }).on('keyup',function() {
          this.newEditAreaContent = $(this).html();
          if(this.editAreaContent != this.newEditAreaContent) { //We have changes to content, so run the callback
            callback();
          }
        });
    }
    editor.append(el);
    return editor;
}

function stripTags(el) {
    if('A' === el.tagName) {
        //Remove style and any data attr
        $(el).removeAttr('style');
        $.each($(el).data(), function (i) {
            $(el).removeAttr("data-" + i);
        });
        while((currentGeneration = $(el).children()).length) {
            currentGeneration.each(function(index,currentEl) {
                stripTags(currentEl);
            });
        }
    } else if('BR' === el.tagName) {
        $(el).remove();
    } else {
        $(el).replaceWith($(el).contents());
    }
}

//Init on load; include showUnsavedFlag as callback
initTextEdtitors(textEditorDefaultCallback);
