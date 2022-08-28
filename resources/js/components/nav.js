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
  addWindowHandlers(dropdownLinksUl);
  //Handle cliks on the main link to show or hide dropdown items
  mainLink.on('click',function(e) {

    if(!dropdownLinksUl.hasClass('nav-links-show')) {
      el.addClass('nav-links-dropdown-open');
      dropdownLinksUl.addClass('nav-links-show');
    } else {
      el.removeClass('nav-links-dropdown-open');
      dropdownLinksUl.removeClass('nav-links-show');
    }

    //Hide any others that happen to be open
    let others = $('li.nav-links-dropdown').not(el);
    others.each(function(index,item) {
      $(item).removeClass('nav-links-dropdown-open');
      $(item).find('ul').first().removeClass('nav-links-show');
    });
  });
}

function addHamburgerHandler(el) {
  //Make sure clicks on the hamburger don't go through to the window
  el.on('click',function(e) {
      e.stopPropagation();
      if(navLinksContainer.length) {
        if(navLinksContainer.hasClass('nav-mobile-show')) {
          navLinksContainer.removeClass('nav-mobile-show');
        } else {
          navLinksContainer.addClass('nav-mobile-show');
        }
      }
  })
  addWindowHandlers(navLinksContainer);
}

function addWindowHandlers(el) {
  //Clicks anywhere else will hide open dropdowns
  $(window).on('click',function() {
    if(el.hasClass('nav-mobile-show')) {
      el.removeClass('nav-mobile-show');
    }
    if(el.hasClass('nav-links-show')) {
      el.parent().removeClass('nav-links-dropdown-open');
      el.removeClass('nav-links-show');
    }
  });

  //Remove if window is resized (or mobile orientation changes)
  $(window).resize(function() {
    if(el.hasClass('nav-mobile-show')) {
      el.removeClass('nav-mobile-show');
    }
    if(el.hasClass('nav-links-show')) {
      el.parent().removeClass('nav-links-dropdown-open');
      el.removeClass('nav-links-show');
    }
  });
}
