let alerts = $('.alert');
alerts.each(function(index, el) {
    let alert = $(el);
    alert.fadeIn(400);
    let closer = $('<span>');
    closer.addClass('closer');
    closer.html('<i class="fas fa-times"></i>');
    alert.prepend(closer);
    closer.on('click',function() {
      alert.fadeOut(400);
    });
});
