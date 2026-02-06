<?php
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

// Загрузка текущих данных
$bioData = json_decode(file_get_contents('data/bio.json'), true) ?? ['content' => ''];
$projectsData = json_decode(file_get_contents('data/projects.json'), true) ?? ['projects' => []];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container {
            padding: 8rem 3rem 4rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
        }
        
        .logout-btn {
            padding: 0.8rem 1.5rem;
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
            border: 1px solid rgba(255, 107, 107, 0.3);
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255, 107, 107, 0.2);
        }
        
        .admin-section {
            background: rgba(30, 30, 30, 0.7);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(100, 255, 218, 0.1);
        }
        
        .admin-section h3 {
            color: #64ffda;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        textarea, input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            margin-bottom: 1rem;
            font-family: inherit;
        }
        
        textarea {
            min-height: 200px;
            resize: vertical;
        }
        
        .save-btn {
            padding: 1rem 2rem;
            background: #64ffda;
            color: #0a0a0a;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        
        .save-btn:hover {
            opacity: 0.9;
        }
        
        .project-item {
            background: rgba(255, 255, 255, 0.03);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .add-project-btn {
            padding: 1rem 2rem;
            background: rgba(100, 255, 218, 0.1);
            color: #64ffda;
            border: 1px solid rgba(100, 255, 218, 0.3);
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .add-project-btn:hover {
            background: rgba(100, 255, 218, 0.2);
        }
        
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            display: none;
        }
        
        .success {
            background: rgba(100, 255, 218, 0.1);
            color: #64ffda;
            border: 1px solid rgba(100, 255, 218, 0.3);
        }
        
        .error {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
            border: 1px solid rgba(255, 107, 107, 0.3);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h2>Административная панель</h2>
            <a href="logout.php" class="logout-btn">Выйти</a>
        </div>
        
        <div id="message" class="message"></div>
        
        <section class="admin-section">
            <h3>Редактировать биографию</h3>
            <textarea id="bio-content"><?php echo htmlspecialchars($bioData['content']); ?></textarea>
            <button onclick="saveBio()" class="save-btn">Сохранить биографию</button>
        </section>
        
        <section class="admin-section">
            <h3>Редактировать проекты</h3>
            <div id="projects-editor">
                <?php foreach ($projectsData['projects'] as $index => $project): ?>
                <div class="project-item" data-index="<?php echo $index; ?>">
                    <input type="text" value="<?php echo htmlspecialchars($project['title']); ?>" 
                           placeholder="Название проекта" class="project-title">
                    <textarea placeholder="Описание проекта" class="project-description"><?php 
                        echo htmlspecialchars($project['description']); 
                    ?></textarea>
                    <input type="text" value="<?php echo htmlspecialchars($project['link'] ?? ''); ?>" 
                           placeholder="Ссылка на проект (необязательно)" class="project-link">
                    <button onclick="removeProject(this)" style="
                        padding: 0.5rem 1rem;
                        background: rgba(255, 107, 107, 0.1);
                        color: #ff6b6b;
                        border: 1px solid rgba(255, 107, 107, 0.3);
                        border-radius: 6px;
                        cursor: pointer;
                    ">Удалить</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button onclick="addProject()" class="add-project-btn">+ Добавить проект</button>
            <button onclick="saveProjects()" class="save-btn">Сохранить все проекты</button>
        </section>
    </div>
    
    <script>
    function showMessage(text, type) {
        const msg = document.getElementById('message');
        msg.textContent = text;
        msg.className = 'message ' + type;
        msg.style.display = 'block';
        setTimeout(() => msg.style.display = 'none', 3000);
    }
    
    async function saveBio() {
        const content = document.getElementById('bio-content').value;
        
        try {
            const response = await fetch('update_content.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({type: 'bio', content})
            });
            
            const result = await response.json();
            if (result.success) {
                showMessage('Биография сохранена!', 'success');
            } else {
                showMessage('Ошибка: ' + result.error, 'error');
            }
        } catch (error) {
            showMessage('Ошибка сети', 'error');
        }
    }
    
    function addProject() {
        const editor = document.getElementById('projects-editor');
        const newIndex = editor.children.length;
        
        const projectDiv = document.createElement('div');
        projectDiv.className = 'project-item';
        projectDiv.innerHTML = `
            <input type="text" placeholder="Название проекта" class="project-title">
            <textarea placeholder="Описание проекта" class="project-description"></textarea>
            <input type="text" placeholder="Ссылка на проект (необязательно)" class="project-link">
            <button onclick="removeProject(this)" style="
                padding: 0.5rem 1rem;
                background: rgba(255,107,107,0.1);
                color: #ff6b6b;
                border: 1px solid rgba(255,107,107,0.3);
                border-radius: 6px;
                cursor: pointer;
            ">Удалить</button>
        `;
        
        editor.appendChild(projectDiv);
    }
    
    function removeProject(button) {
        if (confirm('Удалить этот проект?')) {
            button.closest('.project-item').remove();
        }
    }
    
    async function saveProjects() {
        const projects = [];
        const items = document.querySelectorAll('.project-item');
        
        items.forEach(item => {
            const title = item.querySelector('.project-title').value.trim();
            const description = item.querySelector('.project-description').value.trim();
            const link = item.querySelector('.project-link').value.trim();
            
            if (title && description) {
                projects.push({
                    title,
                    description,
                    link: link || null
                });
            }
        });
        
        try {
            const response = await fetch('update_content.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({type: 'projects', projects})
            });
            
            const result = await response.json();
            if (result.success) {
                showMessage('Проекты сохранены!', 'success');
            } else {
                showMessage('Ошибка: ' + result.error, 'error');
            }
        } catch (error) {
            showMessage('Ошибка сети', 'error');
        }
    }
    </script>
</body>
</html>
