<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Файловый менеджер</title>
  <link rel="stylesheet" href="/css/styles.css">
  <style>
    .filemanager-container {
      width: 100%;
      height: 80vh !important; /* 90% от высоты окна просмотра */
      box-sizing: border-box;
      padding: 10px;
    }
    .filemanager-container iframe {
      width: 100% !important;
      height: 100% !important;
      border: none;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <h1>Файловый менеджер</h1>
  <div class="filemanager-container">
    <iframe src="/elfinder/elfinder.src.html"></iframe>
  </div>
</body>
</html>
