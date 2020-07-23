let alerts = $('.alert');
alerts.each(function(index, el) {
    let alert = $(el);
    let closer = $('<span>');
    closer.addClass('closer');
    closer.html('<i class="fas fa-times"></i>');
    alert.prepend(closer);
    alert.on('click',function() {
      alert.fadeOut(400);
    });
});
