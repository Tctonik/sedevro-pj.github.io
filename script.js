// Загрузка и отображение данных
async function loadContent() {
    try {
        // Загружаем биографию
        const bioResponse = await fetch('data/bio.json');
        const bioData = await bioResponse.json();
        document.getElementById('bio-content').innerHTML = 
            bioData.content.replace(/\n/g, '<br>');
        
        // Загружаем проекты
        const projectsResponse = await fetch('data/projects.json');
        const projectsData = await projectsResponse.json();
        const container = document.getElementById('projects-container');
        
        container.innerHTML = projectsData.projects.map((project, index) => `
            <div class="tile" style="animation-delay: ${0.1 + index * 0.1}s">
                <div class="tile-content">
                    <h3>${escapeHtml(project.title)}</h3>
                    <p>${escapeHtml(project.description).replace(/\n/g, '<br>')}</p>
                    ${project.link ? `<a href="${escapeHtml(project.link)}" target="_blank" style="
                        color: #64ffda; text-decoration: none; margin-top: 1rem; display: inline-block;
                    ">Посмотреть проект →</a>` : ''}
                </div>
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Ошибка загрузки данных:', error);
        document.getElementById('bio-content').innerHTML = 
            '<p>Добро пожаловать! Редактируйте содержимое в админ-панели.</p>';
    }
}

// Экранирование HTML для безопасности
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Анимация плиток при скролле
function animateTilesOnScroll() {
    const tiles = document.querySelectorAll('.tile');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, { threshold: 0.1 });
    
    tiles.forEach(tile => {
        observer.observe(tile);
    });
}

// Плавный скролл для навигации
document.querySelectorAll('nav a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 80,
                behavior: 'smooth'
            });
        }
    });
});

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    loadContent();
    animateTilesOnScroll();
    
    // Добавляем интерактивность плиткам
    document.addEventListener('mousemove', (e) => {
        const tiles = document.querySelectorAll('.tile');
        tiles.forEach(tile => {
            const rect = tile.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            if (x > 0 && x < rect.width && y > 0 && y < rect.height) {
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                const rotateY = (x - centerX) / 25;
                const rotateX = (centerY - y) / 25;
                
                tile.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px)`;
            } else {
                tile.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
            }
        });
    });
});
