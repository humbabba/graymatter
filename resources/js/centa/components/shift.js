let shifts = $('[class*="shift+"]');

shifts.attr('data-original-position',function() {
  return $(this).index();
});

checkForShifts();

function checkForShifts() {
  //Confirm elements are Centa shifts, then process
  if(shifts.length) {
    shifts.each(function(index,element) {
      let el = $(element);
      var classes = el.prop('class').split(/\s+/);
      $(classes).each(function(index,elClass) {
          if(elClass.startsWith('shift+')) {
            let elSplitClass = elClass.split('+');
            if(2 === elSplitClass.length && !isNaN(elSplitClass[1])) {
              let degree = parseInt(elSplitClass[1]);
              cellShift(el,degree);
            }
          }
        });
    });
  }
  $('body').show(); //We've hidden it in CSS till all shifts are done
}

$(window).resize(function() {
  checkForShifts();
});

//Shift the cell
function cellShift(el,degree) {
  let original = el.data('originalPosition');
  let current = el.index();
  let siblingsCount = el.siblings().length;
  if('isolate' === el.css('unicode-bidi')) { //We're on desktop, so make sure it's in original position before moving
    if(el.index() === original) {
      let final = current + degree;
      if(final < 0) { //In case we overshoot, don't go below 0
        final = 0;
      }
      if(final > siblingsCount) { //And don't go beyond total siblings
        final = siblingsCount;
      }
      if(degree < 0) {
        el.parent().children().eq(final).before(el);
      } else {
        el.parent().children().eq(final).after(el);
      }
    }
  } else { //We're on mobile, so make sure it's not in its original position before moving
    if(el.index() !== original) {
      if(degree < 0) {
        el.parent().children().eq(original).after(el);
      } else {
        el.parent().children().eq(original).before(el);
      }
    }
  }
}
