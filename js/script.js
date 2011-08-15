$('#submit').bind('click', function(e) {
    e.preventDefault();
    
    var targetBox  = $('#compressed_code');
    var resultSect = $('#results');
    var resultTxt  = $('#results > p > strong');
    var loaderDiv  = $('#loader');
    loaderDiv.fadeIn();
    
    $.ajax({
        url         : 'index.php',
        dataType    : 'json',
        type        : 'POST',
        data        : {
            type                    : $('input[name="type"]:checked').val(),
            code                    : $('textarea[name="code"]').val(),
            nomunge                 : $('input[name="nomunge"]').is(':checked'),
            'preserve-semi'           : $('input[name="preserve-semi"]').is(':checked'),
            'disable-optimizations'   : $('input[name="disable-optimizations"]').is(':checked'),
        },
        success : function(retdata) {
            var dat = retdata.data;
            //console.log(retdata.data);
            loaderDiv.fadeOut();
            resultSect.css('opacity', 0);
            targetBox.val(dat.compressed);
            resultSect.animate({ opacity :1}, 1000);
            $(resultTxt[0]).text(dat.original_size);
            $(resultTxt[1]).text(dat.compressed_size);
            $(resultTxt[2]).text(dat.difference+'%');
            if(resultSect.is(':hidden')) resultSect.slideDown('slow');
        },
        error       : function(xhr, txtStatus, error) {
            loaderDiv.hide('fast');
            alert($.parseJSON(xhr.responseText).msg);
        }
    });
});
