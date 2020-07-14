//Declare globals
let navLinksContainer,
  dropdownNavItems,
  mainLink,
  navHamburger;

//Define globals
navLinksContainer = $('.nav-links-container');
navHamburger = $('.nav-hamburger');
dropdownNavItems = $('li.nav-links-dropdown');

//Set handlers
setHandlers();

function setHandlers() {

  //Dropdown handlers
  if(dropdownNavItems.length) {
    dropdownNavItems.each(function(index,el) {
      addDropdownHandler($(el));
    });
  }

  if(navHamburger.length) {
    addHamburgerHandler(navHamburger);
  }

}

function addDropdownHandler(el) {
  //Make sure clicks on the nav item don't go through to the window
  el.on('click',function(e) {
      e.stopPropagation();
  })
  //Define target elements - the main link and any ULs within the nav item
  mainLink = el.find('a').first();
  let dropdownLinksUl = el.find('ul');
  addWindowClickHandler(dropdownLinksUl);
  //Handle cliks on the main link to show or hide dropdown items
  mainLink.on('click',function(e) {
    if('none' === dropdownLinksUl.css('display')) {
      el.addClass('nav-links-dropdown-open');
      dropdownLinksUl.fadeIn(400);
    } else {
      el.removeClass('nav-links-dropdown-open');
      dropdownLinksUl.fadeOut(400);
    }
  });

  //Remove if window is resized (or mobile orientation changes)
  $(window).resize(function() {
    if('none' !== dropdownLinksUl.css('display')) {
      dropdownLinksUl.fadeOut(400);
    }
  });
}

function addHamburgerHandler(el) {
  //Make sure clicks on the hamburger don't go through to the window
  el.on('click',function(e) {
      e.stopPropagation();
      if(navLinksContainer.length) {
        if('none' === navLinksContainer.css('display')) {
          navLinksContainer.fadeIn(400);
        } else {
          navLinksContainer.fadeOut(400);
        }
      }
  })
  addWindowClickHandler(navLinksContainer);
}

function addWindowClickHandler(el) {
  let firstChild = el.children().first();
  let isFlex = ('flex' === firstChild.css('display'))? true:false;
  let isRow = ('row' === firstChild.css('flex-direction'))? true:false;
  //Clicks anywhere else will hide open dropdowns
  $(window).on('click',function() {
    if('none' !== el.css('display') && isFlex && !isRow) {
      el.fadeOut(400);
    }
  });
}
