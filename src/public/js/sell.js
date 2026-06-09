 document.getElementById('item-image').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const previewContainer = document.getElementById('image-preview');
        const previewImage = document.getElementById('preview-img');

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block'; // プレビュー領域を表示
            }
            reader.readAsDataURL(file);
        }
    });
