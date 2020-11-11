/**
 * Globals
 */
let currentGeneration,selectionObject;
let selectionPointer = false;
let ancestorTools = [];
let selectedTools = [];
let activeTools = [];
const openMarkerString = '<marker id="openMarker"></marker>';
const closeMarkerString = '<marker id="closeMarker"></marker>';
const tags = ['b','i','u','strike'];

/**
 * Define rich-text editing tools
 * @type {({title: string, class: string, tool: string}|{title: string, class: string, tool: string}|{title: string, class: string, tool: string}|{title: string, class: string, tool: string}|{title: string, class: string, tool: string})[]}
 */
const toolsArray = [
    {class:'fas fa-bold',tool: 'b',title: 'Bold (ctrl-b)'},
    {class:'fas fa-italic',tool: 'i',title: 'Italic (ctrl-i)'},
    {class:'fas fa-underline',tool: 'u',title: 'Underline (ctrl-u)'},
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
    {class:'fas fa-subscript',tool: 'sub',title: 'Subscript'},
    {class:'fas fa-superscript toolbar-spacer',tool: 'sup',title: 'Superscript'},
    {class:'fas fa-minus-circle',tool: 'clearFormat',title: 'Clear all formatting'},
    {class:'fas fa-code',tool: 'toggleCode',title: 'Toggle code view'},
];

/**
* Find all hidden inputs with text-editor class and replace them with rich-text editors.
*/
export const initTextEditors = (timeout = 0) => {
    //Turn hidden inputs with 'text-editor' class into rich-text editors
    $('input[type="hidden"]').each(function(index,item) {
        if($(item).hasClass('text-editor')) {
            //Bail if it's already a text-editor
            const itemParent = $(item).parent();
            if(itemParent.hasClass('textEditorMasterDiv')) {
                return;
            }

            const newElement = $(item).clone();
            const fancyEditor = makeTextEditor(newElement);
            const toolbar = fancyEditor.find('.toolbar');

            insertMoreTools(toolbar);
            $(item).replaceWith(fancyEditor);

            //Optional timeout gives makeTextEditor a few milliseconds to be completely added to DOM on first load
            if (timeout) {
                setTimeout(function() {
                    processToolbarForWidth(toolbar);
                    },timeout);
            } else {
                processToolbarForWidth(toolbar);
            }
        }
    });
};

/**
* Build the container that will hold buttons that don't fit.
* It's hidden till revealed (if necessary) in processToolbarForWidth.
*/
const insertMoreTools = toolbar => {
    const moreToolsContainer = $('<span class="more-tools-container">');
    moreToolsContainer.on('click',function() {
        $(this).next('.more-tools-holder').fadeToggle();
    });
    const moreToolsButton = $('<i class="toolbar-button fas fa-ellipsis-h" title="Show/hide tools">');
    const moreToolsHolder = $('<div class="more-tools-holder">')
    moreToolsContainer.append(moreToolsButton);
    moreToolsContainer.insertAfter(toolbar);
    moreToolsHolder.insertAfter(moreToolsContainer);
};

/**
* Depending on container width, hide tools that don't fit and display button to toggle them.
*/
const processToolbarForWidth = toolbar => {
    const moreToolsHolder = toolbar.parent().find('.more-tools-holder');
    let childrenWidth = 0;
    const children = toolbar.children();
    const childrenIndexMax = children.length - 1;
    const moreToolsContainer = toolbar.next('.more-tools-container');
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
};

/**
* Handle window resize events viz. text-editors.
* This will make sure the toolbars display correctly.
*/
$(window).resize(function() {
    //Find them all
    const textEditors = $('.textEditorMasterDiv');

    textEditors.each(function(index,el) {
        //Find the hidden input at the core of each
        const hiddenInput = $(el).find('input[type="hidden"]').first();
        //Reset hidden input in DOM instead of text-editor
        $(el).replaceWith(hiddenInput);
    });

    //Reinitialize all text editors
    initTextEditors();
});

/**
* Build rich-text editors to replace hidden inputs with.
* Loops through tools defined above and assigns click events.
*/
const makeTextEditor = el => {
    //Div to hold editor-input combo
    const editor = $('<div class="textEditorMasterDiv">');

    //Toolbar div and tools
    const toolbar = $('<div class="toolbar">');
    $(toolsArray).each(function(index,item) {
        const tool = $('<i class="toolbar-button">');
        tool.addClass(item.class);
        tool.attr('data-tool',item.tool);
        tool.prop('title',item.title);
        tool.on('mousedown',function(e) {
            e.preventDefault();
            let input = null;
            let copyDiv,codeDiv;
            switch(item.tool) {
                case 'inserImage':
                    insertImage(editArea);
                    break;
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
    if(textEditorOnChangeCallback) {
        codeEditArea.on('keydown',function() {
          this.editAreaContent = $(this).val();
        }).on('keyup',function() {
          this.newEditAreaContent = $(this).val();
          if(this.editAreaContent != this.newEditAreaContent) { //We have changes to content, so run the callback
              textEditorOnChangeCallback();
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
    editArea.on('input keyup',function() {
        const updatedCode = $(this).html();
        el.val(updatedCode);
        codeEditArea.val(updatedCode);
    });

    //Only check for changes in editArea if we have a callback.
    if(textEditorOnChangeCallback) {
        editArea.on('keydown',function() {
          this.editAreaContent = $(this).html();
        }).on('keyup',function() {
          this.newEditAreaContent = $(this).html();
          if(this.editAreaContent != this.newEditAreaContent) { //We have changes to content, so run the callback
              textEditorOnChangeCallback();
          }
        });
    }

    //Make sure we inactivate tools when exiting editArea
    editArea.on('focusout',function() {
      inactivateAllToolsDisplay($(this));
    });

    //Reset selected tools on click
    editArea.on('click',function() {
      selectedTools = [];
    });

    //Reset selected tools on arrow keys
    editArea.on('keydown',function(e) {
      if('ArrowLeft' === e.key ||
       'ArrowRight' === e.key ||
       'ArrowUp' === e.key ||
       'ArrowDown' === e.key
     ) {
      selectedTools = [];
     }
    });

    //Deal with keystrokes and clicks re: formatting
    editArea.on('click keydown mouseup keyup',function(e) {
      evaluateFormatting($(this));
    });

    editor.append(el);
    return editor;
};

const insertImage = (editArea) => {
  return false;
};

/**
* For basic text formatting
*/
const execFormattingTool = (tool,editArea,format = true) => {

    //Get the selection range - since this varies browser to browser, we're going to have to do some normalizing
    const range = window.getSelection().getRangeAt(0);

    let emptySelection = range.collapsed;

    //We create and will insert custom tags to act as "markers," so we can reset the selection after all formatting
    const openMarker = document.createElement('marker');
    $(openMarker).attr('id','openMarker');
    const closeMarker = document.createElement('marker');
    $(closeMarker).attr('id','closeMarker');
    range.insertNode(openMarker);

    //Collapse the range to the end, so we can insert the closeMarker in the proper spot
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
    getSelectionObject(tool,editArea,emptySelection);

    //For empty selects, we need to update active tools display
    if(selectionObject.emptySelection) {
        toggleSelectedTools(selectionObject.tool);
        reconcileToolsDisplay(editArea);
    } else if(format) { //Format may be false in the case of evaluateFormatting, where we just want to get a selectionObject based on mere selection, not a formatting button click
      //Go through the logic to apply (or reverse) formatting on selection
      wrapTags(editArea);
    }

    //Remove any nested instances of formatting
    cleanRedundantCode(editArea);

    //Reset the selection since the above will destroy the original selection
    replaceMarkersWithSelection(editArea);

};

/**
* Turn tools on or off based on button click for empty selection
*/
const toggleSelectedTools = tool => {
  const targetToolIndex = selectedTools.indexOf(tool);
  if(targetToolIndex > -1) {
    selectedTools.splice(targetToolIndex,1);
  } else {
    selectedTools.push(tool);
  }
};

/**
* Get some info about the selection in an object we can reference in code later on.
*/
const getSelectionObject = (tool,editArea,emptySelection) => {
    selectionObject = {
        openAncestor:false,
        closeAncestor:false,
        allFormatted: false,
        tool: tool,
        openTool: '<' + tool + '>',
        closeTool: '</' + tool + '>',
        emptySelection: emptySelection
    };
    let editAreaString = editArea.html();
    const patternString = openMarkerString + '(.*)' + closeMarkerString;
    const pattern = new RegExp(patternString);
    let contentString = editAreaString.match(pattern)[1];
    const openMarker = editArea.find('#openMarker');
    const openMarkerAncestor = openMarker.closest(tool);
    const closeMarker = editArea.find('#closeMarker');
    const closeMarkerAncestor = closeMarker.closest(tool);
    let containsOpenTag = false;
    let containsCloseTag = false;

    if(contentString) {
        selectionObject.contentString = contentString;
    }
    if(contentString.indexOf(selectionObject.openTool) > -1) {
        containsOpenTag = true;
    }

    if(contentString.indexOf(selectionObject.closeTool) > -1) {
        containsCloseTag = true;
    }
    if(openMarkerAncestor.length) {
        selectionObject.openAncestor = openMarkerAncestor;
    }
    if(closeMarkerAncestor.length) {
        selectionObject.closeAncestor = closeMarkerAncestor;
    }

    //Determine if all text in contentString is already wrapped in this tool
    if((containsOpenTag || containsCloseTag) ||
      (selectionObject.openAncestor || selectionObject.closeAncestor)) { //Only necessary if we've got an tag inside the selection or either marker has an ancestor of the selected tool

      //We are 'faking' proper HTML by wrapping the (possibly) partial HTML in the selected formatting tool.
      contentString = selectionObject.openTool + contentString + selectionObject.closeTool;

      //Search for and remove any doubles of tags created by the step above
      const openTagPattern = new RegExp(selectionObject.openTool + selectionObject.openTool, 'gi');
      const closeTagPattern = new RegExp(selectionObject.closeTool + selectionObject.closeTool, 'gi');
      contentString = contentString.replace(openTagPattern,selectionObject.openTool).replace(closeTagPattern,selectionObject.closeTool);

      //Create new JQuery object containing the cleaned HTML of the selected content
      let contentStringObj = $('<span>');
      contentStringObj.html(contentString);

      //Find any instances of the selected tool as *children* of our JQuery object
      const tools = contentStringObj.find(tool);
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
};

/**
* If selected text is already all formatted, reverse it. Otherwise apply selected tool.
*/
const wrapTags = editArea => {
    //First we need to remove any lingering empty tags; we don't do this earlier cause it messes with selection to do so on some browsers.
    const emptyTag = editArea.find('empty');
    emptyTag.replaceWith(emptyTag.html());

    let editAreaString = editArea.html();
    if(selectionObject.allFormatted) {
        editAreaString = reverseFormatting(editAreaString);
        inactivateToolDisplay(editArea,selectionObject.tool);
    } else {
        editAreaString = addFormatting(editAreaString);
        activateToolDisplay(editArea,selectionObject.tool);
    }
    editArea.html(editAreaString);
};

/**
* Simply wrap selected content in tags for tool.
*/
const addFormatting = editAreaString => {
  const betweenMarkersContent = getBetweenMarkersContent(editAreaString);
  return editAreaString.replace(betweenMarkersContent, selectionObject.openTool + betweenMarkersContent + selectionObject.closeTool);
};

/**
* This is the more-complex case. Sometimes we need to wrap selected content in tags in reverse order,
* sometimes we need to just close formatting at the beginning of the selection.
*/
const reverseFormatting = editAreaString => {
  const openMarkerPattern = new RegExp(openMarkerString);
  const closeMarkerPattern = new RegExp(closeMarkerString);

  //In the case of no ancestor elements of the markers for the selected tool, or only one for the open marker, we just close the formatting early.
  if((selectionObject.openAncestor && !selectionObject.closeAncestor) || !selectionObject.openAncestor && !selectionObject.closeAncestor) {
      editAreaString = editAreaString.replace(openMarkerPattern,'~~makeClose~~');
  } else { //Otherwise, we have formatted content BEYOND the selection and need to repoen the formatting after reversing it for the selection.
      editAreaString = editAreaString.replace(openMarkerPattern,'~~makeClose~~').replace(closeMarkerPattern,'~~makeOpen~~');
  }

  editAreaString = editAreaString.replace('~~makeClose~~',selectionObject.closeTool + openMarkerString).replace('~~makeOpen~~',closeMarkerString + selectionObject.openTool);

  //Get rid of any tools in between markers that survived the above, so we're just left with the close-then-open or simply close tag.
  const betweenMarkersContent = getBetweenMarkersContent(editAreaString);
  const cleanBetweenMarkersContent = getCleanContent(betweenMarkersContent);

  return editAreaString.replace(betweenMarkersContent,cleanBetweenMarkersContent);
};

/**
* On each click or keydown, we check the formatting of the selection and adjust active tools accordingly
*/
const evaluateFormatting = editArea => {
  setTimeout(function() {
    const range = window.getSelection().getRangeAt(0);
    const emptySelection = range.collapsed;
    if(emptySelection) {
      const emptyMarker = $('<empty>'); //A fake element for the purposes finding ancestor elements with jQuery
      range.surroundContents(emptyMarker[0]);
      ancestorTools = [];
      tags.forEach(function(tag,index) {
        const ancestor = emptyMarker.closest(tag);
        if(ancestor.length) {
          ancestorTools.push(tag);
        }
      });
      emptyMarker.remove();
      inactivateNonSelectedToolsDisplay(editArea);
      if(ancestorTools.length) {
        reconcileToolsDisplay(editArea);
      }
    } else {
      tags.forEach(function(tool,index) {
        execFormattingTool(tool,editArea,false);
        if(selectionObject.allFormatted) {
          activateToolDisplay(editArea,tool);
        } else {
          inactivateToolDisplay(editArea,tool);
        }
      });
    }
  },5);
}

/**
* Toggle tool-active display
*/
const activateToolDisplay = (editArea,tool) => {
    editArea.closest('.textEditorMasterDiv').find(`[data-tool='${tool}']`).addClass('active');
    const targetToolIndex = activeTools.indexOf(tool);
    if(targetToolIndex === -1) {
      activeTools.push(tool);
    }
};
const inactivateToolDisplay = (editArea,tool) => {
  editArea.closest('.textEditorMasterDiv').find(`[data-tool='${tool}']`).removeClass('active');
  const targetToolIndex = activeTools.indexOf(tool);
  if(targetToolIndex > -1) {
    activeTools.splice(targetToolIndex,1);
  }
};
const inactivateAllToolsDisplay = editArea => editArea.closest('.textEditorMasterDiv').find('.active').removeClass('active');
const inactivateNonSelectedToolsDisplay = editArea => {
  tags.forEach(function(tool,index) {
      if(selectedTools.indexOf(tool) === -1) {
        inactivateToolDisplay(editArea,tool);
      }
  });
};

/**
* Reconcile user intention for tool-button status based on existing formatting and formatting commands from  clicks or ctrl-command
*/
const reconcileToolsDisplay = editArea => {
  //If the selection has ancestor formatting, we make that tool button active unless a formatting command is reversing it
  ancestorTools.forEach(function(tool,index) {
    inactivateToolDisplay(editArea,tool);
    if(selectedTools.indexOf(tool) === -1) {
      activateToolDisplay(editArea,tool);
    }
  });
  //For all formatting commands, we activate that the corresponding button unless reversed by ancestor formatting
  selectedTools.forEach(function(tool,index) {
    const toolButton = editArea.closest('.textEditorMasterDiv').find(`[data-tool='${tool}']`);
      inactivateToolDisplay(editArea,tool);
    if(ancestorTools.indexOf(tool) === -1) {
      activateToolDisplay(editArea,tool);
    }
  });
};

/**
* For selecting the content between the markers for manuplulation.
*/
const getBetweenMarkersContent = editAreaString => {

    const betweenMarkersPattern = new RegExp(openMarkerString + '(.*)' + closeMarkerString);
    return editAreaString.match(betweenMarkersPattern)[1];
};

/**
* For removing any instances of the tool in a given piece of content
*/
const getCleanContent = content => {
    const openTagPattern = new RegExp(selectionObject.openTool,'gi');
    const closeTagPattern = new RegExp(selectionObject.closeTool,'gi');
    return content.replace(openTagPattern,'').replace(closeTagPattern,'');
};

/**
* It's possible we've got "redundant" formatting tags left over after the above, as in a B tag with B children. Clean 'em up.
*/
const cleanRedundantCode = editArea => {
    tags.forEach(function(tag) {
        const inspectedElements = editArea.find(tag);
        if(inspectedElements.length) {
            inspectedElements.each(function() {
                const inspectedElementsDescendants = $(this).find(tag);
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
        //Case: Tag closes and immediately opens again, with a marker between.
        let redundantCloseOpen = new RegExp(closeTag + openMarkerString + openTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,openMarkerString);
        redundantCloseOpen = new RegExp(closeTag + closeMarkerString + openTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,closeMarkerString);

        //Case: Tag closes and immediately opens again, without a marker in between
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

/**
* We've kept our marker tags throughout all the manipulation above, so we can reset the selection in a way that will be visually identical to what the user originally selected.
*/
const replaceMarkersWithSelection = editArea => {
    //First let's see if we have an empty marker - if so, selection is different
    const emptyMarker = editArea.find('empty')[0];
    if('undefined' !== typeof emptyMarker) { //We are dealing with an empty select
        const range = window.getSelection().getRangeAt(0);
        range.selectNode(emptyMarker); //We have to select a NODE to get empty selection to work on all browsers; hence the "empty" tag inserted in addFormatting and reverseFormatting above
    } else {
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
    }

    //Remove marker tags
    editArea.find('marker').remove();
};

/**
 * Remove HTML (except links) from copy.
 * @param el
 */
const stripTags = el => {
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

/**
 * Handle keyboard shortcuts for text editor
 */
$(document).on('keydown', function (e) {
    if ((e.metaKey || e.ctrlKey)) {
        const tool = e.key.toLowerCase();
        if(tags.indexOf(tool) > -1) {
            const editArea = $(':focus');
            if(editArea.hasClass('fancy-text-div')) {
                e.preventDefault();
                execFormattingTool(tool,editArea);
            }
        }
    }
});

/**
 * Init on load
 */
initTextEditors(50);
