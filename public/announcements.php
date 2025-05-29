<?php
require_once '../includes/db_util.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once '../includes/csrf.php';
csrf_validate();

// Handle new announcement post (admin only)
if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin' && isset($_POST['announcement_content'])) {
    $title = trim($_POST['announcement_title'] ?? '');
    $content = trim($_POST['announcement_content']);
    $category = trim($_POST['announcement_category'] ?? 'General');
    $image = null;
    // Handle image upload
    if (isset($_FILES['announcement_image']) && $_FILES['announcement_image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['announcement_image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['announcement_image']['tmp_name'], '../uploads/' . $image);
    }
    if ($content !== '' && $title !== '') {
        db_execute('INSERT INTO announcements (user_id, title, content, image, category) VALUES (?, ?, ?, ?, ?)', [$_SESSION['user_id'], $title, $content, $image, $category]);
        header('Location: announcements.php');
        exit();
    }
}

$categories = ['General', 'Event', 'Promo', 'Update'];

// 5. Category Filter
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 6;
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : null;
$where = $categoryFilter ? 'WHERE a.category = ?' : '';
$params = $categoryFilter ? [$categoryFilter] : [];
$countSql = "SELECT COUNT(*) FROM announcements a $where";
$totalAnnouncements = !empty($params)
    ? db_fetch_one($countSql, $params)["COUNT(*)"]
    : db_fetch_one($countSql)["COUNT(*)"];
$totalPages = ceil($totalAnnouncements / $perPage);
$offset = ($page - 1) * $perPage;
$announcementsSql = "SELECT a.*, u.first_name, u.last_name, u.role FROM announcements a JOIN users u ON a.user_id = u.id $where ORDER BY a.created_at DESC LIMIT $perPage OFFSET $offset";
$announcements = !empty($params)
    ? db_fetch_all($announcementsSql, $params)
    : db_fetch_all($announcementsSql);

$sort = $_GET['sort'] ?? '';

ob_start();
?>
<div class="max-w-4xl mx-auto py-8 px-2 sm:px-4 flex-1 w-full" aria-live="polite" aria-describedby="announcements-list-desc">
  <span id="announcements-list-desc" class="sr-only">Announcements list updates automatically when you filter or search.</span>
    <!-- Bannered Announcement Title -->
    <div class="w-full flex justify-center mb-8">
      <div class="w-full bg-gradient-to-r from-[#009245] to-[#006837] rounded-2xl py-6 px-8 shadow text-center max-w-full" style="max-width:100%;">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight drop-shadow-sm m-0">Announcements</h1>
      </div>
    </div>
    <?php 
// Only show the upload form to logged-in Admins (must have both user_id and role)
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'Admin' && !empty($_SESSION['user_id'])):
?>
<form method="post" enctype="multipart/form-data" class="mb-8 bg-white rounded-xl shadow p-4 flex flex-col gap-3" aria-label="Post a new announcement">
    <?= csrf_input() ?>
    <input name="announcement_title" type="text" class="border border-[#009245] rounded p-2 focus:outline-none focus:ring-2 focus:ring-[#009245]" placeholder="Announcement Title" required>
    <textarea name="announcement_content" rows="3" class="border border-[#009245] rounded p-2 focus:outline-none focus:ring-2 focus:ring-[#009245] resize-none" placeholder="Write a new announcement..." required></textarea>
    <div class="flex gap-3 items-center">
        <select name="announcement_category" class="border border-[#009245] rounded p-2 focus:outline-none focus:ring-2 focus:ring-[#009245]">
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <label class="flex items-center gap-2 cursor-pointer">
            <i class="fas fa-image text-[#006837]"></i>
            <input type="file" name="announcement_image" accept="image/*" class="hidden">
            <span class="text-sm text-[#006837]">Add image</span>
        </label>
    </div>
    <button type="submit" class="self-end bg-[#009245] text-white px-6 py-2 rounded shadow hover:bg-[#006837] transition focus:outline-none focus:ring-2 focus:ring-[#006837]">
        <i class="fas fa-paper-plane mr-2"></i>Post
    </button>
</form>
<?php endif; ?>
    <!-- 5. Optional: Announcement Category Filter -->
    <div class="mb-6 flex flex-wrap gap-2 items-center justify-center">
      <a href="?" class="px-3 py-1 rounded-full text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-[#009245] <?php if (!isset($_GET['category'])) echo 'bg-[#009245] text-white'; else echo 'bg-gray-100 text-[#006837]'; ?>">All</a>
      <?php foreach ($categories as $cat): ?>
        <a href="?category=<?= urlencode($cat) ?>" class="px-3 py-1 rounded-full text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-[#009245] <?php if (isset($_GET['category']) && $_GET['category'] === $cat) echo 'bg-[#009245] text-white'; else echo 'bg-gray-100 text-[#006837]'; ?>"><?= htmlspecialchars($cat) ?></a>
      <?php endforeach; ?>
    </div>
    <div class="space-y-8">
        <?php foreach ($announcements as $a): ?>
        <div class="group bg-[#FFFFFF] rounded-2xl shadow p-0 flex flex-col md:flex-row gap-0 overflow-hidden hover:shadow-2xl hover:scale-[1.02] hover:-translate-y-1 transition-transform duration-300 focus-within:ring-2 focus-within:ring-[#009245] w-full">
            <div class="relative w-full md:w-64 aspect-[3/2] bg-gray-100 flex-shrink-0 flex items-center justify-center overflow-hidden min-h-[180px] max-h-[320px]" style="height:320px;">
                <?php if (!empty($a['image'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($a['image']) ?>" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover filter blur-md scale-110 z-0 transition-transform duration-300" style="pointer-events:none;" />
                    <img src="../uploads/<?= htmlspecialchars($a['image']) ?>" alt="Announcement image for <?= htmlspecialchars($a['title']) ?>" class="max-w-full max-h-full object-contain bg-transparent relative z-10 m-auto block group-hover:scale-105 group-hover:brightness-95 transition-transform duration-300" style="object-fit:contain;" loading="lazy">
                <?php else: ?>
                    <div class="flex items-center justify-center w-full h-full text-[#006837] text-4xl bg-[#F3F4F6]">📢</div>
                <?php endif; ?>
            </div>
            <div class="flex-1 p-4 sm:p-8 flex flex-col">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs bg-[#009245] text-white px-2 py-0.5 rounded"><i class="fas fa-tag mr-1"></i><?= htmlspecialchars($a['category']) ?></span>
                    <?php if (!empty($a['is_featured'])): ?>
                      <span class="ml-2 px-2 py-0.5 text-xs bg-[#006837] text-white rounded-full font-bold shadow">Featured</span>
                    <?php endif; ?>
                    <span class="text-xs text-[#006837] flex items-center gap-1"><i class="fas fa-user"></i> <?= htmlspecialchars(trim($a['first_name'] . ' ' . $a['last_name'])) ?><?php if ($a['role'] === 'Admin'): ?><span class="ml-2 px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded">Admin</span><?php endif; ?></span>
                    <span class="ml-auto text-xs text-gray-400 flex items-center gap-1"><i class="far fa-clock"></i> <?php
                        $created = strtotime($a['created_at']);
                        $now = time();
                        $diff = $now - $created;
                        if ($diff < 60*60*24) {
                          echo 'Today';
                        } elseif ($diff < 60*60*48) {
                          echo 'Yesterday';
                        } elseif ($diff < 60*60*24*7) {
                          echo floor($diff/(60*60*24)) . ' days ago';
                        } else {
                          echo date('M j, Y', $created);
                        }
                    ?></span>
                </div>
                <h2 class="text-lg font-bold text-[#1A1A1A] mb-1 truncate" title="<?= htmlspecialchars($a['title']) ?>" tabindex="0" aria-label="<?= htmlspecialchars($a['title']) ?>"><?= htmlspecialchars($a['title']) ?></h2>
                <div class="text-gray-700 text-base line-clamp-3 mb-3 leading-relaxed flex-1">
                    <?= nl2br(htmlspecialchars(mb_strimwidth($a['content'], 0, 140, '...'))) ?>
                </div>
                <a href="announcement-single.php?id=<?= $a['id'] ?>" class="inline-block mt-auto text-xs text-[#009245] font-semibold hover:underline focus:outline-none focus:ring-2 focus:ring-[#009245] transition" aria-label="Read more about <?= htmlspecialchars($a['title']) ?>">Read more &rarr;</a>
            </div>
        </div>
        <?php endforeach; ?>
        <!-- 3. Improved Empty State -->
        <?php if (empty($announcements)): ?>
          <div class="text-gray-400 text-center py-12 flex flex-col items-center gap-4">
            <svg width="64" height="64" fill="none" viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="32" r="30" fill="#F3F4F6"/><path d="M20 40l12-12 12 12" stroke="#006837" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><path d="M32 28v12" stroke="#006837" stroke-width="3" stroke-linecap="round"/></svg>
            <span class="block text-lg">No announcements yet.<br>Check back soon or <a href='contact.php' class='text-[#009245] underline font-semibold'>contact us</a>!</span>
          </div>
        <?php endif; ?>
    </div>
    <!-- 6. Optional: Pagination -->
    <?php if ($totalPages > 1): ?>
      <nav class="flex justify-center mt-10" aria-label="Pagination">
        <ul class="inline-flex -space-x-px">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li>
              <a href="?page=<?= $i ?><?= $categoryFilter ? '&category=' . urlencode($categoryFilter) : '' ?>" class="px-4 py-2 border border-gray-200 bg-white text-[#006837] font-semibold text-sm rounded-l focus:outline-none focus:ring-2 focus:ring-[#009245] transition <?php if ($i == $page) echo 'bg-[#009245] text-white'; ?>" aria-current="<?= $i == $page ? 'page' : false ?>"> <?= $i ?> </a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
$page_title = 'Announcements - Arbiter Coffee Hub';
include 'layout_public.php';
