//Globals
let currentGeneration,selectionObject;

const openMarkerString = '<marker id="openMarker"></marker>';
const closeMarkerString = '<marker id="closeMarker"></marker>';
const tags = ['b','i','u','strike'];

//Define rich-text-editing tools.
const toolsArray = [
    {class:'fas fa-bold',tool: 'b',title: 'Bold'},
    {class:'fas fa-italic',tool: 'i',title: 'Italic'},
    {class:'fas fa-underline',tool: 'u',title: 'Underline'},
    {class:'fas fa-strikethrough toolbar-spacer',tool: 'strike',title: 'Strikethrough'},
    {class:'fas fa-image toolbar-spacer',tool: 'inserImage',title: 'Insert image'},
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

/*
* Find all hidden inputs with text-editor class and replace them with rich-text editors.
*/
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

/*
* Build the container that will hold buttons that don't fit.
* It's hidden till revealed (if necessary) in processToolbarForWidth.
*/
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

/*
* Depending on container width, hide tools that don't fit and display button to toggle them.
*/
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


/*
* Handle window resize events viz. text-editors.
* This will make sure the toolbars display correctly.
*/
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

/*
* Build rich-text editors to replace hidden inputs with.
* Loops through tools defined above and assigns click events.
*/
makeTextEditor = (el,callback = false) => {
    //Div to hold editor-input combo
    let editor = $('<div class="textEditorMasterDiv">');

    //Toolbar div and tools
    let toolbar = $('<div class="toolbar">');
    $(toolsArray).each(function(index,item) {
        let tool = $('<i class="toolbar-button">');
        tool.addClass(item.class);
        tool.attr('data-tool',item.tool);
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
                    copyDiv.html('<p>' + copyDiv.html().replace(/&nbsp;/g, ' ').replace(/\s\s/g, ' ').trim() + '</p>'); //Put the stripped code back inside a p tag
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
                    execFormattingTool(item.tool,editArea);
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

    //Only check for changes in codeEditArea if we have a callback.
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

    //Only check for changes in editArea if we have a callback.
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

/*
* For basic text formatting only
*/
execFormattingTool = (tool,editArea) => {
    //Get the selection range - since this varies browser to browser, we're going to have to do some normalizing
    let range = window.getSelection().getRangeAt(0);

    //We create and will insert custom tags to act as "markers," so we can reset the selection after all formatting
    const openMarker = document.createElement('marker');
    $(openMarker).attr('id','openMarker');
    const closeMarker = document.createElement('marker');
    $(closeMarker).attr('id','closeMarker');
    range.insertNode(openMarker);

    //Collaps the range to the end, so we can insert the closeMarker in the proper spot
    range.collapse(false);
    range.insertNode(closeMarker);

    //This makes sure markers are *inside* top-level P tags
    editArea.children().each(function() {
        if('MARKER' === this.tagName) {
            if('openMarker' === $(this).prop('id')) {
                let that = $(this).next();
                that.prepend($(this));
            }
            if('closeMarker' === $(this).prop('id')) {
                let that = $(this).prev();
                that.append($(this));
            }
        }
    });

    //We get a special object representing some key info about the selection for later use
    getSelectionObject(tool,editArea);

    console.log('AFTER: editArea.html()');
    console.log(editArea.html());

    wrapTags(editArea);

    cleanRedundantCode(editArea);
    replaceMarkersWithSelection(editArea);

    console.log('FINAL: editArea.html()');
    console.log(editArea.html());
};

/**
* Get some info about the selection in an object we can reference in code later on.
*/
getSelectionObject = (tool,editArea) => {
    selectionObject = {
      openAncestor:false,
      closeAncestor:false,
      containsOpenTag: false,
      containsCloseTag: false,
      allFormatted: false,
      tool: tool,
      openTool: '<' + tool + '>',
      closeTool: '</' + tool + '>',
    };
    let editAreaString = editArea.html();
    let patternString = openMarkerString + '(.*)' + closeMarkerString;
    let pattern = new RegExp(patternString);
    let contentString = editAreaString.match(pattern)[1];
    let openMarker = editArea.find('#openMarker');
    let openMarkerAncestor = openMarker.closest(tool);
    let closeMarker = editArea.find('#closeMarker');
    let closeMarkerAncestor = closeMarker.closest(tool);

    if(contentString) {
        selectionObject.contentString = contentString;
    }
    if(contentString.indexOf(selectionObject.openTool) > -1) {
        selectionObject.containsOpenTag = true;
    }

    if(contentString.indexOf(selectionObject.closeTool) > -1) {
        selectionObject.containsCloseTag = true;
    }
    if(openMarkerAncestor.length) {
        selectionObject.openAncestor = openMarkerAncestor;
    }
    if(closeMarkerAncestor.length) {
        selectionObject.closeAncestor = closeMarkerAncestor;
    }

    //Determine if all text in contentString is already wrapped in this tool
    if((selectionObject.containsOpenTag || selectionObject.containsCloseTag) ||
      (selectionObject.openAncestor || selectionObject.closeAncestor)) { //Only necessary if we've got an tag inside the selection or either marker has an ancestor of the selected tool

      //We are 'faking' proper HTML by wrapping the (possibly) partial HTML in the selected formatting tool.
      contentString = selectionObject.openTool + contentString + selectionObject.closeTool;

      //Search for and remove any doubles of tags created by the step above
      let openTagPattern = new RegExp(selectionObject.openTool + selectionObject.openTool, 'gi');
      let closeTagPattern = new RegExp(selectionObject.closeTool + selectionObject.closeTool, 'gi');
      contentString = contentString.replace(openTagPattern,selectionObject.openTool).replace(closeTagPattern,selectionObject.closeTool);

      //Create new JQuery object containing the cleaned HTML of the selected content
      let contentStringObj = $('<span>');
      contentStringObj.html(contentString);

      //Find any instances of the selected tool as *children* of our JQuery object
      let tools = contentStringObj.find(tool);
      let wrappedText = ''; //A placeholder string for all text already wrapped in tool
      tools.each(function() {
        if(0 === $(this).find(tool).length) { //This allows us only to give this treatment to the bottom-most children
          wrappedText += $(this).text(); //We "unwrap" them by simply replacing them with their own inner HTML and that to our placeholder string
        }
      });

      if(contentStringObj.text() === wrappedText) { //So if placeholder sting equals the text of the overall object, then we know all text had already been wrapped in the tool.
          selectionObject.allFormatted = true;
      }
    }
}

/*
* If selected text is already all formatted, reverse it. Otherwise apply selected tool.
*/
wrapTags = editArea => {
    let editAreaString = editArea.html();
    if(selectionObject.allFormatted) {
      editAreaString = reverseFormatting(editAreaString);
    } else {
      editAreaString = addFormatting(editAreaString);
    }
    editArea.html(editAreaString);
};

/*
* Simply wrap selected content in tags for tool.
*/
addFormatting = editAreaString => {
    let betweenMarkersContent = getBetweenMarkersContent(editAreaString);
    return editAreaString.replace(betweenMarkersContent, selectionObject.openTool + betweenMarkersContent + selectionObject.closeTool);
}

/*
* This is the more-complex case. Sometimes we need to wrap selected content in tags in reverse order,
* sometimes we need to just close formatting at the beginning of the selection.
*/
reverseFormatting = editAreaString => {
  let openMarkerPattern = new RegExp(openMarkerString);
  let closeMarkerPattern = new RegExp(closeMarkerString);

  //In the case of no ancestor elements of the markers for the selected tool, or only one for the open marker, we just close the formatting early.
  if((selectionObject.openAncestor && !selectionObject.closeAncestor) || !selectionObject.openAncestor && !selectionObject.closeAncestor) {
      console.log('CASE: Open ancestor only or no ancestors.');
      editAreaString = editAreaString.replace(openMarkerPattern,'~~makeClose~~');
  } else { //Otherwise, we have formatted conent BEYOND the selection and need to repoen the formatting after reversing it for the selection.
      console.log('CASE: Close ancestor only or both ancestors');
      editAreaString = editAreaString.replace(openMarkerPattern,'~~makeClose~~').replace(closeMarkerPattern,'~~makeOpen~~');
  }

  editAreaString = editAreaString.replace('~~makeClose~~',selectionObject.closeTool + openMarkerString).replace('~~makeOpen~~',closeMarkerString + selectionObject.openTool);

  //Get rid of any tools in between markers that survived the above, so we're just left with the close-then-open or simply close tag.
  let betweenMarkersContent = getBetweenMarkersContent(editAreaString);
  cleanBetweenMarkersContent = getCleanContent(betweenMarkersContent);

  return editAreaString.replace(betweenMarkersContent,cleanBetweenMarkersContent);
}

/*
* For selecting the content between the markers for manuplulation.
*/
getBetweenMarkersContent = editAreaString => {
  let betweenMarkersPattern = new RegExp(openMarkerString + '(.*)' + closeMarkerString);
  return editAreaString.match(betweenMarkersPattern)[1];
}

/*
* For removing any instances of the tool in a given piece of content
*/
getCleanContent = content => {
  let openTagPattern = new RegExp(selectionObject.openTool,'gi');
  let closeTagPattern = new RegExp(selectionObject.closeTool,'gi');
  return content.replace(openTagPattern,'').replace(closeTagPattern,'');
}

/*
* It's possible we've got "redundant" formatting tags left over after the above, as in a B tag with B children. Clean 'em up.'
*/
cleanRedundantCode = editArea => {
    tags.forEach(function(tag) {
      let inspectedElements = editArea.find(tag);
      if(inspectedElements.length) {
        inspectedElements.each(function() {
          let inspectedElementsDescendants = $(this).find(tag);
          if(inspectedElementsDescendants.length) {
            inspectedElementsDescendants.each(function() {
              $(this).replaceWith($(this).html());
            });
          }
        });
      }
    });

    //Find specified redundancies we know of due to experience.
    let editAreaString = editArea.html();
    tags.forEach(function(tag) {
        const openTag = '<' + tag + '>';
        const closeTag = '</' + tag + '>';
        //Case: Tag closes and immediately opens again, with or without a marker between.
        let redundantCloseOpen = new RegExp(closeTag + openMarkerString + openTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,openMarkerString);
        redundantCloseOpen = new RegExp(closeTag + closeMarkerString + openTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,closeMarkerString);
        redundantCloseOpen = new RegExp(closeTag + openTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,'');
        //Case: An empty tag.
        redundantCloseOpen = new RegExp(openTag + closeTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,'');
        //Case: Firefox sometimes leaves a <br> right before a </p>.
        editAreaString = editAreaString.replace('<br></p>','</p>');
        //Case: <strong> and <em> tags perhaps pasted in from elsewhere.
        editAreaString = editAreaString.replace('<strong>','<b>').replace('</strong>','</b>')
          .replace('<em>','<i>').replace('</em>','</i>');

    });
    editArea.html(editAreaString);
};

/*
* We've kept our marker tags throughout all the manipulation above, so we can reset the selection in a way that will be visually identical to what the user originally selected.
*/
replaceMarkersWithSelection = editArea => {
  //Make brand-new range
  const range = document.createRange();
  //Clean up any old selection
  const selection = window.getSelection();
  selection.removeAllRanges();

  //Add new range as selection
  selection.addRange(range);

  //Set start and end on new range
  range.setStartAfter(editArea.find('#openMarker')[0]);
  range.setEndBefore(editArea.find('#closeMarker')[0]);

  //Remove marker tags
  editArea.find('marker').remove();
}

//Remove HTML (except links) from copy.
stripTags = el => {
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
        $(el).text($(el).text() + ' '); //Adds a space to end of each text node so we end up with spaces between paragraphs.
        $(el).replaceWith($(el).contents());
    }
};

//Init on load; include default defined in centa.js as callback.
initTextEdtitors(textEditorDefaultCallback);
