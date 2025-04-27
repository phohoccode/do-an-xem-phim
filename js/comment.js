// comment.js
const commentTextarea = document.getElementById('comment');
const commentActions = document.getElementById('commentActions');
const cancelCommentBtn = document.getElementById('cancelComment');
const commentsContainer = document.getElementById('commentsContainer');

const usernameJS = window.usernameJS;
const avatarJS = window.avatarJS;
const slug = window.slug;

// Khi textarea focus thì hiện nút Hủy và Bình luận
commentTextarea.addEventListener('focus', () => {
  commentActions.classList.remove('hidden');
});

// Bấm Hủy thì ẩn nút và xóa nội dung
cancelCommentBtn.addEventListener('click', () => {
  commentTextarea.value = '';
  commentActions.classList.add('hidden');
});

// Hàm format thời gian "X giây/phút/giờ/ngày trước"
function timeAgo(date) {
  const now = new Date();
  const seconds = Math.floor((now - date) / 1000);

  if (seconds < 60) return `${seconds} giây trước`;
  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) return `${minutes} phút trước`;
  const hours = Math.floor(minutes / 60);
  if (hours < 24) return `${hours} giờ trước`;
  const days = Math.floor(hours / 24);
  if (days < 30) return `${days} ngày trước`;
  const months = Math.floor(days / 30);
  if (months < 12) return `${months} tháng trước`;
  const years = Math.floor(months / 12);
  return `${years} năm trước`;
}

// Hàm render một comment
function renderComment(username, avatar, content, createdAt) {
  const createdDate = new Date(createdAt);
  const timeText = timeAgo(createdDate);

  const commentHTML = `
    <div class="flex items-start mb-4">
      <img src="${avatar}" class="comment-avatar rounded-full mr-3 mt-1 w-10 h-10 object-cover" alt="Avatar">
      <div class="bg-[#3a3f58] p-3 rounded-lg w-full">
        <div class="flex items-center space-x-2 mb-1">
          <span class="text-sm text-gray-300 font-semibold">${username}</span>
          <span class="text-xs text-gray-400">• ${timeText}</span>
        </div>
        <p class="text-white mb-2">${content}</p>
        <div class="flex items-center justify-between">
          <button class="flex items-center text-gray-400 hover:text-red-500">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
            Thích
          </button>
          <div class="relative">
            <button class="more-options focus:outline-none">
              <svg class="w-5 h-5 text-gray-400 hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M6 10a2 2 0 114 0 2 2 0 01-4 0zm5 0a2 2 0 114 0 2 2 0 01-4 0zm5 0a2 2 0 114 0 2 2 0 01-4 0z" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
  commentsContainer.insertAdjacentHTML('afterbegin', commentHTML);
}

// Hàm load comment
async function loadComments() {
  try {
    commentsContainer.innerHTML = '';
    const response = await fetch(`get_comments.php?slug=${encodeURIComponent(slug)}`);
    const comments = await response.json();

    comments.reverse().forEach(comment => {
      const createdAt = new Date(comment.created_at);
      renderComment(comment.username || 'Người dùng', comment.avatar || 'img/user.png', comment.content, createdAt);
    });
  } catch (error) {
    console.error('Không thể tải bình luận:', error);
  }
}

// Khi bấm Bình luận
commentActions.querySelector('button.bg-blue-600').addEventListener('click', async () => {
  const content = commentTextarea.value.trim();
  if (content === '') {
    alert('Vui lòng nhập nội dung bình luận!');
    return;
  }

  try {
    const response = await fetch('save_comment.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ content, slug })
    });

    const result = await response.json();

    if (result.success) {
      // Sau khi bình luận thành công
      commentTextarea.value = '';
      commentActions.classList.add('hidden');
      
      // Tự render bình luận mới ra đầu tiên
      const now = new Date();
      renderComment(usernameJS, avatarJS || 'img/user.png', content, now);

    } else {
      console.error(result.message || 'Lỗi lưu bình luận!');
      alert(result.message || 'Vui lòng đăng nhập tài khoản để bình luận');
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Đã xảy ra lỗi. Vui lòng thử lại.');
  }
});


// Khi load trang thì cũng gọi
window.addEventListener('DOMContentLoaded', loadComments);
