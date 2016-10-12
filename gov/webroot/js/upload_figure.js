
$(function () {


    // // Initialize the jQuery File Upload widget:
    // $('#fileupload').fileupload({
    //     // Uncomment the following to send cross-domain cookies:
    //     //xhrFields: {withCredentials: true},
    //     url: '/upload'
    // });


         $('.xfileupload').fileupload( {
            url: '/upload_figure',
            dataType: 'json',
            autoUpload: true,
            disableImageResize: /Android(?!.*Chrome)|Opera/
                .test(window.navigator.userAgent),
            maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
        }).on('fileuploaddone', function (e, data) {
            $.each(data.result.files, function (index, file) {
                if (file.url) {
                    count = parseInt($("#filecount").val(),10) + 1;
                    $("#filecount").val(count);
                    var hidden_input = "<input type='hidden' name='filename_" +count + "' value='" +  file.name + "' >"  
                                        +"<input type='hidden' name='filemd5sum_" +count + "' value='" +  file.md5sum + "' >"
                                        +"<input type='hidden' name='filesize_" +count + "' value='" + file.size + "' >"
                                        +"<input type='hidden' name='filepath_" +count + "' value='" + file.url + "' >";

                    $("#hidden_input").append(hidden_input);
                    
                } else if (file.error) {
                    // var error = $('<span class="text-danger"/>').text(file.error);
                    // $(data.context.children()[index])
                    //     .append('<br>')
                    //     .append(error);
                }
            });
        });

});
