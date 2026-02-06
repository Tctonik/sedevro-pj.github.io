<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit;
}

// Создаем папку data, если её нет
if (!is_dir('data')) {
    mkdir('data', 0755, true);
}

// Получаем JSON данные
$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Неверный запрос']);
    exit;
}

// Обработка в зависимости от типа данных
if ($input['type'] === 'bio') {
    // Сохраняем биографию
    $bioData = ['content' => $input['content']];
    file_put_contents('data/bio.json', json_encode($bioData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    
    echo json_encode(['success' => true]);
    
} elseif ($input['type'] === 'projects') {
    // Сохраняем проекты
    $projectsData = ['projects' => $input['projects']];
    file_put_contents('data/projects.json', json_encode($projectsData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    
    echo json_encode(['success' => true]);
    
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Неизвестный тип данных']);
}
?>
