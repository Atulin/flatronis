tinymce.init({
    selector: '#editor',
    skin: 'dark',

    plugins: "autoresize textcolor colorpicker hr link nonbreaking anchor contextmenu image pagebreak " +
        "lists preview searchreplace table wordcount autolink code fullscreen media spellchecker",

    menubar: "file edit view insert format",

    toolbar: "removeformat | styleselect fontsizeselect forecolor | bold italic underline strikethrough | alignleft aligncenter alignjustify | " +
        "outdent indent| numlist bullist | link image media blockquote | hr pagebreak | fullscreen preview spellchecker",

    branding: false,
    default_link_target: "_blank",
    link_assume_external_targets: true,

    target_list: [
        {title: 'None', value: ''},
        {title: 'Same page', value: '_self'},
        {title: 'New page', value: '_blank'},
        {title: 'Lightbox', value: '_lightbox'}
    ],

    media_url_resolver: function (data, resolve) {
        if (data.url.indexOf('youtube.com') !== -1 || data.url.indexOf('youtu.be') !== -1) {

            let codearr;
            if (data.url.indexOf('youtube.com') !== -1)
                codearr = data.url.split("?v=");
            else
                codearr = data.url.split("/");
            let code = codearr[codearr.length - 1];

            let embedHtml = '<div class="youtube"><iframe src="https://www.youtube.com/embed/' + code + '" width="100%" height="100%" allowfullscreen="allowfullscreen"></iframe></div>';
            console.log("youtube");
            resolve({html: embedHtml});
        } else if (data.url.indexOf('gfycat') !== -1) {
            let codearr = data.url.split("/");
            let code = codearr[codearr.length - 1];
            let embedHtml = '<div style="position:relative; padding-bottom:54.17%"><iframe src="https://gfycat.com/ifr/' + code + '" frameborder="0" scrolling="no" width="100%" height="100%" style="position:absolute;top:0;left:0;" allowfullscreen></iframe></div>';
            console.log("gfycat");
            resolve({html: embedHtml});
        } else {
            console.log("shit");
            resolve({html: ''});
        }
    },

    contextmenu_never_use_native: true,

    image_caption: true,
    image_class_list: [
        {title: 'Fluid', value: 'fluid image'},
        {title: 'Centered', value: 'centered image'},
        {title: 'Rounded', value: 'rounded image'},
        {title: 'Circular', value: 'circular image'}
    ],

    plugin_preview_height: 600,
    plugin_preview_width: 1000,

    fontsize_formats: "8pt 10pt 11pt 12pt 14pt 18pt 24pt"
});
