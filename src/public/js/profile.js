function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('preview') || document.getElementById('preview-default');
            if (preview.tagName === 'DIV') {
                const img = document.createElement('img');
                img.id = 'preview';
                preview.replaceWith(img);
                img.src = e.target.result;
            } else {
                preview.src = e.target.result;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

