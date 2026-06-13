document.addEventListener('DOMContentLoaded', function () {
    const likeButton = document.getElementById('like-button');
    const likeIcon = document.getElementById('like-icon');
    const likeCount = document.getElementById('like-count');

    // 💡 ログインしていない
    if (!likeButton) return;

    likeButton.addEventListener('click', function () {
        const itemId = this.getAttribute('data-item-id');

        const csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        //  Fetch API を使ってリロードなしでLaravelへPOST送信
        fetch(`/items/${itemId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf_token
            }
        })
        .then(response => {
            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;

            //  コントローラーから返ってきた最新データを元に、画面をリアルタイム書き換え
            if (data.isLiked) {
                // いいね登録された場合：ハートを「赤」に染める
                this.classList.add('action-buttons__btn--active');
            } else {
                // いいね解除された場合：ハートを「薄グレー」に戻す
                this.classList.remove('action-buttons__btn--active');
            }
            // 数字を最新の合計数に書き換え
            likeCount.textContent = data.likesCount;
        })
        .catch(error => console.error('Error:', error));
    });
});
