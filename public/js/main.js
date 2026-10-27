document.addEventListener('DOMContentLoaded', () => {
    // 1. Проверяем сохраненную тему при загрузке
    const currentTheme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
    if (currentTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }

    // Иконки SVG для темы
    const sunIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
    const moonIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

    // 2. Ищем правый блок навигации (.nav-right), чтобы вставить кнопку туда
    const navRight = document.querySelector('.nav-right');
    if (navRight && !document.querySelector('.theme-toggle-btn')) {
        const themeBtn = document.createElement('button');
        themeBtn.className = 'theme-toggle-btn';
        themeBtn.type = 'button';
        
        // Устанавливаем иконку в зависимости от текущей темы (если темно — показываем солнце, чтобы переключить на светлую)
        themeBtn.innerHTML = currentTheme === 'dark' ? sunIcon : moonIcon;
        
        // Красивые стили для круглой иконки-кнопки
        themeBtn.style.background = 'transparent';
        themeBtn.style.border = '1px solid var(--border-color)';
        themeBtn.style.color = 'var(--text-main)';
        themeBtn.style.width = '38px';
        themeBtn.style.height = '38px';
        themeBtn.style.borderRadius = '50%';
        themeBtn.style.cursor = 'pointer';
        themeBtn.style.display = 'flex';
        themeBtn.style.alignItems = 'center';
        themeBtn.style.justifyContent = 'center';
        themeBtn.style.transition = 'all 0.2s ease';

        themeBtn.addEventListener('click', () => {
            let theme = document.documentElement.getAttribute('data-theme');
            if (theme === 'dark') {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                themeBtn.innerHTML = moonIcon;
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                themeBtn.innerHTML = sunIcon;
            }
        });

        // Вставляем кнопку в начало блока .nav-right
        navRight.insertBefore(themeBtn, navRight.firstChild);
    }

    // 3. Предпросмотр изображения
    const imageInput = document.querySelector('input[type="file"][name="image"]');
    if (imageInput) {
        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.querySelector('#image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'image-preview';
                        preview.style.maxWidth = '150px';
                        preview.style.marginTop = '10px';
                        preview.style.borderRadius = '6px';
                        preview.style.display = 'block';
                        imageInput.parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
});