<?php
$allowedFolders = [];

$skipFolders = ['uploads'];

$baseDir = __DIR__;
$folders = [];

if (!empty($allowedFolders)) {
    foreach ($allowedFolders as $name) {
        $path = $baseDir . DIRECTORY_SEPARATOR . $name;
        if (is_dir($path)) {
            $folders[$name] = $path;
        }
    }
} else {
    $items = scandir($baseDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        if (str_starts_with($item, '.')) continue;  
        if (in_array($item, $skipFolders)) continue; 
        $fullPath = $baseDir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($fullPath)) {
            $folders[$item] = $fullPath;
        }
    }
    ksort($folders);
}

$structure = [];
foreach ($folders as $folderName => $folderPath) {
    $phpFiles = glob($folderPath . DIRECTORY_SEPARATOR . '*.php');
    if (empty($phpFiles)) continue; // skip folders with no PHP files
    $structure[$folderName] = array_map('basename', $phpFiles);
}

$viewFile = null;
if (isset($_GET['open'])) {
    $requested = $_GET['open'];                   
    $parts     = explode('/', $requested, 2);
    if (count($parts) === 2) {
        [$reqFolder, $reqFile] = $parts;
        if (
            isset($structure[$reqFolder]) &&
            in_array($reqFile, $structure[$reqFolder], true)
        ) {
            $viewFile = $requested;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PHP File Navigator</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600&display=swap');

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg:        #0f1117;
    --surface:   #181c27;
    --border:    #2a2f3f;
    --accent:    #4f8ef7;
    --accent2:   #7c3aed;
    --text:      #e2e8f0;
    --muted:     #64748b;
    --success:   #34d399;
    --hover-bg:  #1e2438;
  }

  body {
    font-family: 'IBM Plex Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  header {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 18px 28px;
    display: flex;
    align-items: center;
    gap: 14px;
  }
  header .logo {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 1.15rem;
    font-weight: 600;
    color: var(--accent);
    letter-spacing: -0.5px;
  }
  header .logo span { color: var(--muted); }
  .breadcrumb {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.78rem;
    color: var(--muted);
    margin-left: auto;
  }
  .breadcrumb a { color: var(--accent); text-decoration: none; }
  .breadcrumb a:hover { text-decoration: underline; }

  .layout {
    display: flex;
    flex: 1;
    overflow: hidden;
    height: calc(100vh - 61px);
  }

  aside {
    width: 270px;
    min-width: 200px;
    background: var(--surface);
    border-right: 1px solid var(--border);
    overflow-y: auto;
    padding: 20px 0;
    flex-shrink: 0;
  }
  .folder-group { margin-bottom: 6px; }
  .folder-label {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: var(--muted);
    cursor: default;
    user-select: none;
  }
  .folder-label .icon { font-size: 0.9rem; }
  .folder-label .badge {
    margin-left: auto;
    background: var(--border);
    color: var(--muted);
    font-size: 0.68rem;
    padding: 1px 6px;
    border-radius: 99px;
    font-family: 'IBM Plex Mono', monospace;
  }
  .file-list { list-style: none; }
  .file-list li a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 20px 7px 36px;
    font-size: 0.85rem;
    color: var(--text);
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
    font-family: 'IBM Plex Mono', monospace;
  }
  .file-list li a:hover {
    background: var(--hover-bg);
    color: var(--accent);
    border-left-color: var(--accent);
  }
  .file-list li a.active {
    background: rgba(79,142,247,.08);
    color: var(--accent);
    border-left-color: var(--accent);
  }
  .file-list li a .dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--border);
    flex-shrink: 0;
    transition: background 0.15s;
  }
  .file-list li a:hover .dot,
  .file-list li a.active .dot { background: var(--accent); }

  main {
    flex: 1;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }

  .welcome {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    color: var(--muted);
    text-align: center;
    padding: 40px;
  }
  .welcome .big { font-size: 3rem; }
  .welcome h2 { font-size: 1.1rem; font-weight: 400; color: var(--text); }
  .welcome p  { font-size: 0.85rem; line-height: 1.6; max-width: 380px; }

  .view-bar {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 10px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.8rem;
    color: var(--muted);
  }
  .view-bar .file-path { color: var(--success); }\
  .view-bar a.close-btn {
    margin-left: auto;
    color: var(--muted);
    text-decoration: none;
    font-size: 1.1rem;
    line-height: 1;
    padding: 2px 6px;
    border-radius: 4px;
    transition: background 0.15s, color 0.15s;
  }
  .view-bar a.close-btn:hover { background: var(--border); color: var(--text); }

  iframe {
    flex: 1;
    border: none;
    background: #fff;
    width: 100%;
    height: 100%;
  }

  /* ── Empty state ── */
  .empty-sidebar {
    padding: 20px;
    font-size: 0.82rem;
    color: var(--muted);
    line-height: 1.7;
  }

  /* ── Scrollbar ── */
  aside::-webkit-scrollbar { width: 4px; }
  aside::-webkit-scrollbar-track { background: transparent; }
  aside::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
</style>
</head>
<body>

<header>
  <div class="logo">&lt;<span>/</span>&gt; Project's Folder Navigator</div>
  <?php if ($viewFile): ?>
  <div class="breadcrumb">
    <a href="navigator.php">home</a> / <?= htmlspecialchars($viewFile) ?>
  </div>
  <?php endif; ?>
</header>

<div class="layout">

  <!-- ── Sidebar ── -->
  <aside>
    <?php if (empty($structure)): ?>
      <div class="empty-sidebar">
        No folders with .php files found.<br>
        Create a subfolder and add .php files to it.
      </div>
    <?php else: ?>
      <?php foreach ($structure as $folderName => $files): ?>
        <div class="folder-group">
          <div class="folder-label">
            <span class="icon">📁</span>
            <?= htmlspecialchars($folderName) ?>
            <span class="badge"><?= count($files) ?></span>
          </div>
          <ul class="file-list">
            <?php foreach ($files as $file):
              $link     = 'navigator.php?open=' . urlencode($folderName . '/' . $file);
              $isActive = ($viewFile === $folderName . '/' . $file);
            ?>
            <li>
              <a href="<?= $link ?>" <?= $isActive ? 'class="active"' : '' ?>>
                <span class="dot"></span>
                <?= htmlspecialchars($file) ?>
              </a>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </aside>

  <!-- ── Main area ── -->
  <main>
    <?php if ($viewFile): ?>
      <div class="view-bar">
        ▶ &nbsp;<span class="file-path"><?= htmlspecialchars($viewFile) ?></span>
        <a href="navigator.php" class="close-btn" title="Close">✕</a>
      </div>
      <iframe src="<?= htmlspecialchars($viewFile) ?>" title="<?= htmlspecialchars($viewFile) ?>"></iframe>
    <?php else: ?>
      <div class="welcome">
        <div class="big">📂</div>
        <h2>Select a file to preview</h2>
        <p>Choose a .php file from the sidebar on the left. It will render right here inside the browser.</p>
      </div>
    <?php endif; ?>
  </main>

</div>
</body>
</html>
