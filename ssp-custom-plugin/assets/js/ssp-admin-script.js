/**
 * Created by Stephen on 11/9/2015.
 */

/*global woocommerce_admin_meta_boxes */

var globalCheckSign = '<span class="ssp-success-check">✔</span>';
var globalFilesToCheck = phpVars.filesToCheck;

var issuesBox = '<div id="issues-modal" class="modal fade" tabindex="-1" style="top:80px;" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
    '<div class="modal-dialog">' +
    '<div class="modal-content">' +
    '<div class="modal-header" style="height: 15px; padding-top: 0px;">' +
        '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
        '<h4>Artwork Issues</h4>' +
    '</div>' +
    '<div class="modal-body">' +
        '<form id="artwork-issues-form" style="margin-bottom:0px !important;" data-order="" action="' + phpVars.thisDomain + '/api/artwork-issues/" method="POST">' +
            '<div class="control-group">' +
                '<div class="controls">' +
                    '<label class="checkbox artwork-issue">' +
                        '<input type="checkbox" class="issue-option" name="issues[resolution]">Low resolution' +
                    '</label>' +
                    '<label class="checkbox artwork-issue">' +
                        '<input type="checkbox" class="issue-option" name="issues[bleed]">No bleed ' +
                    '</label>' +
                    '<label class="checkbox artwork-issue">' +
                        '<input type="checkbox" class="issue-option" name="issues[color]">Color mode' +
                    '</label>' +
                    '<label class="checkbox artwork-issue">' +
                        '<input type="checkbox" class="issue-option" name="issues[layout]">Image layout' +
                    '</label>' +
                    '<label class="checkbox artwork-issue">' +
                        '<input type="checkbox" class="issue-option" name="issues[fonts]">Fonts issues' +
                    '</label>' +
                    '<label class="checkbox artwork-issue">' +
                        '<input type="checkbox" class="issue-option" name="issues[format]">File format' +
                    '</label>' +
                    '<label class="checkbox artwork-issue" style="margin-top: 7px;">' +
                        '<input id="custom-issue-checkbox" class="issue-option" type="checkbox" name="issues[custom]">Custom issue:' +
                    '</label>' +
                    '<textarea rows="2" style="width:98%; margin-top:-5px; margin-bottom: 10px;" id="custom-issue-text" name="custom-issue" disabled="true"></textarea>' +
                '</div>' +
            '</div>' +
        '</form>' +
    '</div>' +
    '<div class="modal-footer" style="margin-top:-15px; padding:10px 0 5px 0;">' +
        '<p class="text-center"><button type="submit" id="submit-issues" class="btn btn-info btn-large btn-redirect">Send mail</button></p>' +
    '</div>' +
'</div>' +
'</div>' +
'</div>';

jQuery( function ( $ ) {
    $('a.ssp_trigger').on('click', function(){
        var id = $(this).attr('data-id');
        $('#ssp_order_info_'+id ).slideToggle(200);
    });
});

function SaveCustomEmail(input) {
    var check = input.parent().find('.check-icon');
    var cross = input.parent().find('.cross-icon');

    cross.hide();
    check.hide();

    var order = input.data('order');
    var email = input.val();
    var url = input.data('url');

    jQuery.ajax({
        type: "POST",
        url: url,
        method: "POST",
        data: {
            'order': order,
            'email': email
        },
        success: function(response){
            if (response.result == 'ok') {
                check.show();

                check.delay( 2000 ).fadeOut( 300 );
            } else {
                cross.show();
            }
        },
        error: function(response){
            cross.show();
        },
        dataType: "json"
    });
}

jQuery(document).ready(function($){
    function findFilesToUpdate() {
        return($('.file-status'));
    }
    
    function findItemsToCheck() {
        return($('.proofing-item'));
    }
    
    setInterval(function(){
        var itemIds = [];
        var itemElements = $('.proofing-item');

        itemElements.each(function () {
            itemId = $(this).data('order_item_id');
            itemIds.push(itemId);
        });

        jQuery.ajax({
            type: "GET",
            url: phpVars.thisDomain + '/api/items-files-info',
            method: "GET",
            data: {
                'items': itemIds,
            },
            success: function(response){
                if(response.result == 'ok') {
                    var t1 = Date.now();
                    
                    for (var i = 0; i < response.count; ++i) {
                        var file = response.files[i];
                        
                        var validStatus = (file.status == 'corrupt' || file.status == 'approval' || file.status == 'approved' || file.status == 'ready' || file.status == 'not approved'  || file.status == 'converting' || file.status == 'downloading');
                        var validSide = (file.side == 'front' || file.side == 'back');

                        if (!validStatus || !validSide) {
                            continue;
                        }
                        
                        var statusSpans = $('.belongs-to-item-' + file.itemId);
                        
                        var statusSpan = null;
                        
                        statusSpans.each(function() {
                            if ($(this).data('side') == file.side) {
                                statusSpan = $(this);
                            }
                        });
                        
                        statusSpan.attr(
                            'class',
                            statusSpan.attr('class').replace(/\blabel-[^ ]+\b/g, '')
                        );

                        if (file.comment) {
                            var commentRow = statusSpan.parent().parent().parent().find('.comment-row.' + file.side);
                            commentRow.show();
                            commentRow.find('.comment-box').html(file.comment);
                        } else {
                            var commentRow = statusSpan.parent().parent().parent().find('.comment-row.' + file.side);
                            commentRow.hide();
                            commentRow.find('.comment-box').html('');
                        }

                        var fileRow = statusSpan.parent().parent();
                        fileRow.find('.proof-file-name').html(file.name).attr('href', phpVars.thisDomain + '/file/' + file.id);
                        fileRow.find('.proof-file-size').html(file.size);
                        fileRow.find('.file-remove').data('action', phpVars.thisDomain + '/api/remove/' + file.id);
                        fileRow.find('.reset-button').data('action', phpVars.thisDomain + '/api/reset-file-status/' + file.id);
                        statusSpan.data('file-id', file.id);
                        status = file.status;

                        if (status == 'approval') {
                            statusSpan.html('approval').addClass('label-warning');
                        } if (status == 'approved') {
                            statusSpan.html('approved').addClass('label-success');
                        } else if (status == 'ready') {
                            statusSpan.html('ready').addClass('label-info');
                        } else if (status == 'not approved') {
                            statusSpan.html('not approved').addClass('label-danger');
                        } else if (status == 'converting') {
                            statusSpan.html('converting').addClass('label-default');
                        } else if (status == 'downloading'){
                            statusSpan.html('downloading').addClass('label-default');
                        } else if (status == 'corrupt'){
                            statusSpan.html('corrupt').addClass('label-default');
                        }
                    }
                    
                    var t2 = Date.now();
                    var dt =t2-t1;
                    
                    console.log('t: ' + dt);
                } else {
                    console.log('weird');
                }
            },
            error: function(response){
            },
            dataType: "json"
        });      
    }, 5000);

    $('.fileupload').each(function () {
        $(this).fileupload({
            dataType: 'json',
            submit: function (e, data) 
            {
                data.context.find('.abort-icon').fadeIn(150).on('click', function() { data.abort(); });
            },
            progress: function (e, data) 
            {
                var progress = Math.floor(data.loaded / data.total * 100);
                data.context.find('.upload-messages').html(progress + '%');
            },
            always: function (e, data) 
            {
                data.context.find('.abort-icon').hide();
            },
            done: function (e, data) 
            {
                if (data.context) {
                    data.context.find('.upload-messages').html('OK');
                }
            },
            fail: function (e, data) 
            {
                if (data.errorThrown == 'abort') {
                    data.context.find('.upload-messages').html('aborted');
                } else if (data.errorThrown == 'Request Entity Too Large') {
                    data.context.find('.upload-messages').html('too big');
                }
            }
        }).on('fileuploadadd', function (e, data) {
            var form = $( "<form></form>", {
                "action": $(this).data('action'),
                "method": "POST",
                "enctype": "multipart/form-data"
            });

            var parent = $(this).parent();

            var sizeInput = $('<input />', {
                "name": "MAX_FILE_SIZE",
                "value": parent.find('.MAX_FILE_SIZE').data('value')
            });
            sizeInput.appendTo(form);

            fileInput = parent.find('.file');
            fileInput.name = "files[]";
            fileInput.appendTo(form);

            var sideInput = $('<input />', {
                "name": "side[]",
                "value": parent.find('.side').data('value')
            });
            sideInput.appendTo(form);

            var orderInput = $('<input />', {
                "name": "order[]",
                "value": parent.find('.order').data('value')
            });
            orderInput.appendTo(form);
         
            var itemInput = $('<input />', {
                "name": "item[]",
                "value": parent.find('.item').data('value')
            });
            itemInput.appendTo(form);
         
            var adminInput = $('<input />', {
                "name": "admin[]",
                "value": 1
            });
            adminInput.appendTo(form);

            data.form = form;
            data.context = $(this);
        });
    });

    // $('.btn-redirect').click( function () { 
    //     $.cookie("redirect-scroll-pos", $(document).scrollTop(),{ path: '/'} );
    // });

    $('body').append(issuesBox);

    $('.custom-email').keypress(function(e) {
        if (e.which == "13") {
            e.preventDefault();
            SaveCustomEmail($(this));
        }
    });

    $('.item-selector').on("click", function () {
        var item  = jQuery(this).data('item');
        var order = $(this).data('order');

        var approvalForm = '#approvalform_' + order;
        var button       = $(approvalForm).find('.btn');

        var issuesForm = $('#artwork-issues-form');

        if (issuesForm.data('order') != order) {
            issuesForm.data('order', order);
            $('.item-issue').remove();
        }

        if ($(this).data('selected') == '0') {
            $(this).data('selected', '1');
            $(approvalForm).append('<div class="pseudo-input item" id="mail-' + item + '" data-value="' + item + '" ></div>');
            $(issuesForm).append('<input type="hidden" name="items[]" class="item-issue" id="issue-' + item + '" value="' + item + '" />');
        } else {
            $(approvalForm).find("#mail-" + item).remove();
            $(issuesForm).find("#issue-" + item).remove();
            $(this).data('selected', '0');
        }

        if ($(approvalForm).find('.item').size() > 0) {
            $(button).removeClass('disabled');
        } else {
            button.addClass('disabled');
        };
    });


    function submitProofApprovalMailForm(form) {
        var items = form.find('.pseudo-input.item');
        var formData = {
            nomail: form.find('.nomail-val').data('value'),
            items: []
        };
        var url = form.data('action');

        for (var i=0; i<items.length; ++i) {
            var itemId = $(items[i]).data('value');
            formData.items.push(itemId);
        }

        var loaderBox;
        if (formData.nomail) {
            loaderBox = form.find('.loader-box.approval-nomail');
        } else {
            loaderBox = form.find('.loader-box.approval-mail');
        }

        loaderBox.html(phpVars.ajaxLoader);
        loaderBox.show();

        jQuery.ajax({
            type: "POST",
            method: "POST",
            url: url,
            data: formData,
            success: function(response){
                if (response.result == 'ok') {
                    loaderBox.html(globalCheckSign);
                    loaderBox.children().delay(500).fadeOut(300);
                    items.each(function(){
                        if ($(this).hasClass('item')) {
                            $('.item-selector.belongs-to-item-'+ $(this).data('value')).click();
                            var statuses = $('.file-status.belongs-to-item-' + $(this).data('value'));
                            statuses.each(function() {
                                if ($(this).html() == 'ready') {
                                    $(this).removeClass('label-info').addClass('label-warning').html('approval');
                                }
                            });
                            
                        }
                    });
                } else {
                    loaderBox.html('<span>' + response.message + '</span>');
                    loaderBox.children().delay(500).fadeOut(300);
                }
            },
            error: function(response){
                if (response.message) {
                    loaderBox.html('<span>' + response.message + '</span>');
                } else {
                    loaderBox.html('<span>Server error</span');
                    loaderBox.children().delay(500).fadeOut(300);
                }
            },
            dataType: "json"
        });
    }

    $('.proof-approval-mail').on("click", function (e) {
        e.preventDefault();
        submitProofApprovalMailForm($(this).parent());
    });


    $('.nomail').click(function (e) {
        e.preventDefault();
        if(!$(this).hasClass('disabled')) {
            $(this).parent().find('.nomail-val').data('value', 1);
        }
        submitProofApprovalMailForm($(this).parent());
    });

    $('.reset-button').on("click", function (e) {
        e.preventDefault();

        var thisButton = $(this);

        var loaderBox = $(this).next();
        loaderBox.html(phpVars.ajaxLoader);
        loaderBox.show();

        jQuery.ajax({
            type: "POST",
            method: "POST",
            url: $(this).data('action'),
            success: function(response){
                loaderBox.html('');
                var fileRow = $(thisButton).parent().parent();
                if (response.result == 'ok') {
                    var status = fileRow.find('.file-status');
                    status.attr(
                      'class',
                      status.attr('class').replace(/\blabel-[^ ]+\b/g, '')
                    );
                    status.html('ready').addClass('label-info');
                } else {
                }
            },
            error: function(response){
                loaderBox.html('');
                console.log('error');
            },
            dataType: "json"
        });
    });    

    $('.file-remove').on("click", function (e) {
        e.preventDefault();

        var url = $(this).data('action');
        var loaderBox = $(this).next();
        loaderBox.html(phpVars.ajaxLoader);
        loaderBox.show();
        var button = $(this);

        jQuery.ajax({
            type: "POST",
            method: "POST",
            url: url,
            success: function(response){
                loaderBox.html('');
                button.parent().parent().find('.proof-file-name').html('no file').attr('href', '');
                button.parent().parent().find('.proof-file-size').html('');
                button.parent().parent().find('.file-status').addClass('removed');
                button.parent().parent().find('.file-status').addClass('missing');
            },
            error: function(response){
                loaderBox.html('');
            },
            dataType: "json"
        });
    });

    $('.proof-allow-uploads').on("click", function (e) {
        e.preventDefault();

        var url = $(this).data('action');
        var loaderBox = $(this).next();

        loaderBox.html(phpVars.ajaxLoader);
        loaderBox.show();

        jQuery.ajax({
            type: "POST",
            method: "POST",
            url: url,
            success: function(response){
                console.log(globalCheckSign);
                loaderBox.html(globalCheckSign);
                loaderBox.children().delay(500).fadeOut(300);
            },
            error: function(response){
                loaderBox.html('error');
            },
            dataType: "json"
        });
    });

    $('#submit-issues').click(function (e) {
        e.preventDefault();

        var form = $('#artwork-issues-form');
        var formData = form.serialize();

        jQuery.ajax({
            type: "POST",
            method: "POST",
            url: form.attr('action'),
            data: formData,
            success: function(response){
                $('#issues-modal').modal('hide');

                form.find('input.item').each(function() {
                    var itemId = $(this).val();
                    var status = $('.file-status.belongs-to-item-' + itemId);
                    status.attr(
                        'class',
                         status.attr('class').replace(/\blabel-[^ ]+\b/g, '')
                    );
                    status.html('issues').addClass('label-default').addClass('label');
                });

                // $('.item-issue').remove();
            },
            error: function(response){
                console.log('error');
            },
            dataType: "json"
        });
    });

    $('.artwork-issues-link').click(function (event) {
        event.stopImmediatePropagation();

        var selector = $(this).parent().parent().find('.item-selector');

        var item  = selector.data('item');

        selector.parent().parent().find('.item-selector').each(function(){
            if($(this).data('selected') == '1') {
                $(this).click();
            }
        });
        $('.item-issue').remove();

        var issuesForm = $('#artwork-issues-form');
        $('#issues-modal').modal('show');
        $(issuesForm).append('<input type="hidden" class="item item-issue" id="mail-' + item + '" name="items[]" value="' + item + '" />');
    });


    $('#custom-issue-checkbox').on('click', function () {
        var prop = $('#custom-issue-text').prop('disabled');
        $('#custom-issue-text').prop('disabled', !prop);
    });


    $('#issues-modal').on('hidden', function () {
        $('.issue-option').prop('checked', false);
        $('#custom-issue-text').prop('disabled', true).val('');
        $('.item-issue').remove();

        $('.item-selector').each(function(){
            if($(this).data('selected') == '1') {
                $(this).click();
            }
        });
    })

    $('#issues-modal').on('shown', function () {
        $('.issue-option').prop('checked', false);
        $('#custom-issue-text').prop('disabled', true).val('');
    });

    $('.history-view').click(function () {
        if($(this).html() == 'Order history' ){
            $(this).html('Hide history');
            $(this).next().slideDown(300);
        }else{
            $(this).html('Order history');
            $(this).next().slideUp(300);
        }
    });
});
