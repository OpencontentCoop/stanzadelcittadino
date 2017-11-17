$( function() {

    var chat = $('#chat'),
        helpers = {
            checkOwner: function( senderID ){
              if ($('#chat').data('user') === senderID ){
                  return true;
              } else {
                  return false;
              }
            },
            formatDate: function( timestamp ){
              date = new Date( timestamp * 1000);
              return date.toString('dddd MMM yyyy h:mm:ss');
            }
        };

    function loadMessages( target, silent ){
        if ($(target).length) {
            $.ajax({
                type: "GET",
                url: $(target).find('form').attr('action'),
                dataType: "json",
                beforeSend: function () {
                    if (!silent) {
                        $(target).find('.direct-chat-messages').html('<div class="alert alert-info loading"><i class="fa fa-spinner fa-pulse"></i> <small>Recupero dei messaggi in corso</small></div>');
                    }
                },
                success: function (data) {
                    //$(target).html(data);
                    $(target).find('.direct-chat-messages')
                        .html($.templates("#message-tmpl").render(data, helpers));
                    if (silent) {
                        $(target).find('.direct-chat-messages').scrollTop($(target).find('.direct-chat-messages').prop("scrollHeight"));
                    } else {
                        $(target).find('.direct-chat-messages').animate({scrollTop: $(target).find('.direct-chat-messages').prop("scrollHeight")}, 1000);
                    }
                }
            });
        }
    }

    /* Carico i messaggi del thread attivo */
    loadMessages(chat.find('.tab-pane.active.in'), false);

    /* Evento ciclico per il recupero dei messaggi del thread attivo */
    setInterval(function(){
        loadMessages(chat.find('.tab-pane.active.in'), true);
    }, 30000);

    chat.find('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        loadMessages( $(e.target).attr("href"), false);
    })

    chat.find('form').on('submit', function( event ) {
        event.preventDefault();
        var $form = $(this),
            messageInput = $form.find('.message'),
            text = '';

        $.ajax({
            type: "PUT",
            url: $(this).attr('action'),
            dataType: "json",
            data: {
                'message': {
                    'sender_id': $form.find('#message_sender_id').val(),
                    'thread_id': $form.find('#message_thread_id').val(),
                    'message': messageInput.val()
                }
            },
            beforeSend: function(){
                text = $form.find('button').text();
                $form.find('button').prepend('<i class="fa fa-spinner fa-pulse"></i> ')
            },
            success: function(data) {
                $form.parent().find('.direct-chat-messages')
                    .append($.templates("#message-tmpl").render(data, helpers))
                    .animate({ scrollTop: $form.parent().find('.direct-chat-messages').prop("scrollHeight")}, 1000);
                $form.find('button').text(text);
            }
        });
        messageInput.val('');
    })
} );